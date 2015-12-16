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
   * @param \Drupal\Component\Plugin\PluginManagerInterface $key_provider_manager
   *   The key provider plugin manager.
   */
  public function __construct(EntityStorageInterface $storage, PluginManagerInterface $key_provider_manager) {
    $this->storage = $storage;
    $this->keyProviderManager = $key_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('key'),
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
    $plugin = $this->keyProviderManager->createInstance($form_state->getValue('key_provider'), []);
    $plugin->submitConfigurationForm($form, $form_state);
    $form_state->setValue('key_provider_settings', $plugin->getConfiguration());
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Only run key provider settings validation if the form is being submitted
    if ($form_state->isSubmitted()) {
      $plugin = $this->keyProviderManager->createInstance($form_state->getValue('key_provider'), []);
      $plugin->validateConfigurationForm($form, $form_state);
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
