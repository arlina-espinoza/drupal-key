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

    // If the form is rebuilding due to an AJAX call, get the name of the
    // triggering element.
    if ($form_state->isRebuilding() && isset($form_state->getTriggeringElement()['#name'])) {
      $triggering_element = $form_state->getTriggeringElement()['#name'];
    }
    else {
      $triggering_element = '';
    }

    // If the form is not rebuilding, store the original key, so plugins can
    // access it.
    if (!$form_state->isRebuilding()) {
      $form_state->set('original_key', $key);
    }

    $key_types = [];
    foreach ($this->keyTypeManager->getDefinitions() as $plugin_id => $definition) {
      $key_types[$plugin_id] = (string) $definition['label'];
    }

    $key_providers = [];
    foreach ($this->keyProviderManager->getDefinitions() as $plugin_id => $definition) {
      $key_providers[$plugin_id] = (string) $definition['label'];
    }

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

    // This is the element that contains all of the dynamic parts of the form.
    $form['settings'] = array(
      '#type' => 'container',
      '#prefix' => '<div id="key-settings">',
      '#suffix' => '</div>',
    );

    // Key type section.
    $form['settings']['type_section'] = array(
      '#type' => 'details',
      '#title' => $this->t('Type settings'),
      '#open' => TRUE,
    );
    $form['settings']['type_section']['key_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Key type'),
      '#options' => $key_types,
      '#empty_option' => $this->t('- None -'),
      '#empty_value' => '',
      '#default_value' => $key->getKeyType(),
      '#ajax' => array(
        'callback' => [$this, 'ajaxUpdateSettings'],
        'event' => 'change',
        'wrapper' => 'key-settings',
      ),
    );
    $form['settings']['type_section']['key_type_settings'] = array(
      '#type' => 'container',
      '#title' => $this->t('Key type settings'),
      '#title_display' => FALSE,
      '#tree' => TRUE,
    );

    if ($this->keyTypeManager->hasDefinition($key->getKeyType())) {
      // If the form is rebuilding after an AJAX call and the key type
      // field is responsible, clear the key type settings. Otherwise,
      // use the current settings when creating the plugin instance.
      if ($form_state->isRebuilding() && $triggering_element == 'key_type') {
        $user_input = $form_state->getUserInput();
        $user_input['key_type_settings'] = $key_type_settings = array();
        $form_state->setUserInput($user_input);
      }
      else {
        $key_type_settings = $key->getKeyTypeSettings();
      }
      $plugin = $this->keyTypeManager->createInstance($key->getKeyType(), $key_type_settings);

      $form['settings']['type_section']['key_type_settings'] += $plugin->buildConfigurationForm([], $form_state);
    }

    // Key provider section.
    $form['settings']['provider_section'] = array(
      '#type' => 'details',
      '#title' => $this->t('Provider settings'),
      '#open' => TRUE,
    );
    $form['settings']['provider_section']['key_provider'] = array(
      '#type' => 'select',
      '#title' => $this->t('Key provider'),
      '#options' => $key_providers,
      '#empty_option' => $this->t('- Select key provider -'),
      '#empty_value' => '',
      '#required' => TRUE,
      '#default_value' => $key->getKeyProvider(),
      '#ajax' => array(
        'callback' => [$this, 'ajaxUpdateSettings'],
        'event' => 'change',
        'wrapper' => 'key-settings',
      ),
    );
    $form['settings']['provider_section']['key_provider_settings'] = array(
      '#type' => 'container',
      '#title' => $this->t('Key provider settings'),
      '#title_display' => FALSE,
      '#tree' => TRUE,
    );

    if ($this->keyProviderManager->hasDefinition($key->getKeyProvider())) {
      // If the form is rebuilding after an AJAX call and the key provider
      // field is responsible, clear the key provider settings. Otherwise,
      // use the current settings when creating the plugin instance.
      if ($form_state->isRebuilding() && $triggering_element == 'key_provider') {
        $user_input = $form_state->getUserInput();
        $user_input['key_provider_settings'] = $key_provider_settings = array();
        $form_state->setUserInput($user_input);
      }
      else {
        $key_provider_settings = $key->getKeyProviderSettings();
      }
      $plugin = $this->keyProviderManager->createInstance($key->getKeyProvider(), $key_provider_settings);

      $form['settings']['provider_section']['key_provider_settings'] += $plugin->buildConfigurationForm([], $form_state);
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
   * AJAX callback to update the dynamic settings on the form.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   *   The element to update in the form.
   */
  public function ajaxUpdateSettings(array &$form, FormStateInterface $form_state) {
    return $form['settings'];
  }
}
