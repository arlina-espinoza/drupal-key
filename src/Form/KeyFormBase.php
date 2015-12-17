<?php

/**
 * @file
 * Contains \Drupal\key\Form\KeyFormBase.
 */

namespace Drupal\key\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for key add and edit forms.
 */
abstract class KeyFormBase extends EntityForm {

  /**
   * The entity being used by this form.
   *
   * @var \Drupal\key\KeyInterface
   */
  protected $entity;

  /**
   * The key storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The key type manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $keyTypeManager;

  /**
   * The key provider manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $keyProviderManager;

  /**
   * Constructs a new key form.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The key storage.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $key_type_manager
   *   The key type plugin manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $key_provider_manager
   *   The key provider plugin manager.
   */
  public function __construct(EntityStorageInterface $storage, PluginManagerInterface $key_type_manager, PluginManagerInterface $key_provider_manager) {
    $this->storage = $storage;
    $this->keyTypeManager = $key_type_manager;
    $this->keyProviderManager = $key_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('key'),
      $container->get('plugin.manager.key.key_type'),
      $container->get('plugin.manager.key.key_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var $key \Drupal\key\KeyInterface */
    $key = $this->entity;

    // Store the original key, so plugins can access it.
    if (!$form_state->isRebuilding()) {
      $form_state->set('original_key', $key);
    }

    $key_types = [];
    foreach ($this->keyTypeManager->getDefinitions() as $plugin_id => $definition) {
      $key_types[$plugin_id] = (string) $definition['title'];
    }

    $key_providers = [];
    foreach ($this->keyProviderManager->getDefinitions() as $plugin_id => $definition) {
      $key_providers[$plugin_id] = (string) $definition['title'];
    }

    $form['#tree'] = TRUE;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Key name'),
      '#maxlength' => 255,
      '#default_value' => $key->label(),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $key->id(),
      '#machine_name' => array(
        'exists' => array($this->storage, 'load'),
      ),
      '#disabled' => !$key->isNew(),
    );
    $form['description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $key->getDescription(),
      '#description' => $this->t('A short description of the key.'),
    );

    $form['key_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Key Type'),
      '#options' => $key_types,
      '#empty_option' => $this->t('- None -'),
      '#empty_value' => '',
      '#ajax' => [
        'callback' => [$this, 'getKeyTypeForm'],
        'event' => 'change',
        'wrapper' => 'key-type-form',
      ],
      '#default_value' => $key->getKeyType(),
    );
    $form['key_type_settings'] = [
      '#prefix' => '<div id="key-type-form">',
      '#suffix' => '</div>',
    ];
    if ($this->keyTypeManager->hasDefinition($key->getKeyType())) {
      // @todo compare ids to ensure appropriate plugin values.
      $plugin = $this->keyTypeManager->createInstance($key->getKeyType(), $key->getKeyTypeSettings());
      $form['key_type_settings'] += $plugin->buildConfigurationForm([], $form_state);
    }

    $form['key_provider'] = array(
      '#type' => 'select',
      '#title' => $this->t('Key Provider'),
      '#options' => $key_providers,
      '#empty_option' => $this->t('- Select key provider -'),
      '#empty_value' => '',
      '#ajax' => [
        'callback' => [$this, 'getKeyProviderForm'],
        'event' => 'change',
        'wrapper' => 'key-provider-form',
      ],
      '#required' => TRUE,
      '#default_value' => $key->getKeyProvider(),
    );
    $form['key_provider_settings'] = [
      '#prefix' => '<div id="key-provider-form">',
      '#suffix' => '</div>',
    ];
    if ($this->keyProviderManager->hasDefinition($key->getKeyProvider())) {
      // @todo compare ids to ensure appropriate plugin values.
      $plugin = $this->keyProviderManager->createInstance($key->getKeyProvider(), $key->getKeyProviderSettings());
      $form['key_provider_settings'] += $plugin->buildConfigurationForm([], $form_state);
    }

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $key_type_id = $form_state->getValue('key_type');
    if ($this->keyTypeManager->hasDefinition($key_type_id)) {
      $key_type = $this->keyTypeManager->createInstance($key_type_id, []);
      $key_type->submitConfigurationForm($form, $form_state);
    }
    else {
      $form_state->setValue('key_type_settings', []);
    }

    $key_provider_id = $form_state->getValue('key_provider');
    if ($this->keyProviderManager->hasDefinition($key_provider_id)) {
      $key_provider = $this->keyProviderManager->createInstance($key_provider_id, []);
      $key_provider->submitConfigurationForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Only run plugin validation if the form is being submitted.
    if ($form_state->isSubmitted()) {
      $key_type_id = $form_state->getValue('key_type');
      if ($this->keyTypeManager->hasDefinition($key_type_id)) {
        $key_type = $this->keyTypeManager->createInstance($key_type_id, []);
        $key_type->validateConfigurationForm($form, $form_state);
      }

      $key_provider_id = $form_state->getValue('key_provider');
      if ($this->keyProviderManager->hasDefinition($key_provider_id)) {
        $key_provider = $this->keyProviderManager->createInstance($key_provider_id, []);
        $key_provider->validateConfigurationForm($form, $form_state);
      }
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
  }

  /**
   * AJAX action to load the key type settings form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   *   The element to update in the form.
   */
  public function getKeyTypeForm(array &$form, FormStateInterface $form_state) {
    return $form['key_type_settings'];
  }

  /**
   * AJAX action to retrieve the appropriate key provider into the form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   *   The element to update in the form.
   */
  public function getKeyProviderForm(array &$form, FormStateInterface $form_state) {
    return $form['key_provider_settings'];
  }

}
