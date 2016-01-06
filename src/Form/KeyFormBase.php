<?php

/**
 * @file
 * Contains \Drupal\key\Form\KeyFormBase.
 */

namespace Drupal\key\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for key add and edit forms.
 */
abstract class KeyFormBase extends EntityForm {

  /**
   * The key storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The original key.
   *
   * @var \Drupal\key\Entity\Key|bool
   *   The original key entity or FALSE if this is a new key.
   */
  protected $originalKey = FALSE;

  /**
   * Constructs a new key form base.
   *
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $storage
   *   The key storage.
   */
  public function __construct(ConfigEntityStorageInterface $storage) {
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('key')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var $key \Drupal\key\Entity\Key */
    $key = &$this->entity;

    // Only when the form is first built.
    if (!$form_state->isRebuilding()) {
      // If the key provider accepts a key value, get the current value
      // and add it to the key input plugin configuration.
      if ($key->getKeyProvider()->getPluginDefinition()['key_input']['accepted']) {
        $key->getKeyInput()->setConfiguration(['key_value' => $key->getKeyValue()]);
      }
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
      '#options' => $key->getPluginsAsOptions('key_type'),
      '#required' => TRUE,
      '#default_value' => $key->getKeyType()->getPluginId(),
      '#ajax' => array(
        'callback' => [$this, 'ajaxUpdateSettings'],
        'event' => 'change',
        'wrapper' => 'key-settings',
      ),
    );
    $form['settings']['type_section']['key_type_description'] = array(
      '#markup' => $key->getKeyType()->getPluginDefinition()['description'],
    );
    $form['settings']['type_section']['key_type_settings'] = array(
      '#type' => 'container',
      '#title' => $this->t('Key type settings'),
      '#title_display' => FALSE,
      '#tree' => TRUE,
    );

    // If the form is rebuilding and the key type is responsible,
    // update the key type plugin.
    if ($form_state->isRebuilding() && $form_state->getTriggeringElement()['#name'] == 'key_type') {
      $this->updateKeyType();
    }

    if ($key->getKeyType() instanceof PluginFormInterface) {
      $plugin_state = $this->createPluginState('key_type', $form_state);
      $form['settings']['type_section']['key_type_settings'] += $key->getKeyType()->buildConfigurationForm([], $plugin_state);
      $form_state->setValue('key_type_settings', $plugin_state->getValues());
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
      '#options' => $key->getPluginsAsOptions('key_provider'),
      '#required' => TRUE,
      '#default_value' => $key->getKeyProvider()->getPluginId(),
      '#ajax' => array(
        'callback' => [$this, 'ajaxUpdateSettings'],
        'event' => 'change',
        'wrapper' => 'key-settings',
      ),
    );
    $form['settings']['provider_section']['key_provider_description'] = array(
      '#markup' => $key->getKeyProvider()->getPluginDefinition()['description'],
    );
    $form['settings']['provider_section']['key_provider_settings'] = array(
      '#type' => 'container',
      '#title' => $this->t('Key provider settings'),
      '#title_display' => FALSE,
      '#tree' => TRUE,
    );

    // If the form is rebuilding and the key provider is responsible,
    // update the key provider plugin.
    if ($form_state->isRebuilding() && $form_state->getTriggeringElement()['#name'] == 'key_provider') {
      $this->updateKeyProvider();
    }

    if ($key->getKeyProvider() instanceof PluginFormInterface) {
      $plugin_state = $this->createPluginState('key_provider', $form_state);
      $form['settings']['provider_section']['key_provider_settings'] += $key->getKeyProvider()->buildConfigurationForm([], $plugin_state);
      $form_state->setValue('key_provider_settings', $plugin_state->getValues());
    }

    // Key input section.
    $form['settings']['input_section'] = array(
      '#type' => 'details',
      '#title' => $this->t('Value'),
      '#open' => TRUE,
    );

    // Update the key input plugin.
    $this->updateKeyInput();

    $form['settings']['input_section']['key_input'] = array(
      '#type' => 'value',
      '#value' => $key->getKeyInput()->getPluginId(),
    );
    $form['settings']['input_section']['key_input_settings'] = array(
      '#type' => 'container',
      '#title' => $this->t('Key value settings'),
      '#title_display' => FALSE,
      '#tree' => TRUE,
    );
    if ($key->getKeyInput() instanceof PluginFormInterface) {
      $plugin_state = $this->createPluginState('key_input', $form_state);
      $form['settings']['input_section']['key_input_settings'] += $key->getKeyInput()->buildConfigurationForm([], $plugin_state);
      $form_state->setValue('key_input_settings', $plugin_state->getValues());
    }

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($form_state->isSubmitted()) {
      // Make sure each plugin settings field is an array.
      foreach ($this->entity->getPluginTypes() as $type) {
        if (empty($form_state->getValue($type . '_settings'))) {
          $form_state->setValue($type . '_settings', []);
        }
      }

      foreach ($this->entity->getPlugins() as $type => $plugin) {
        if ($plugin instanceof PluginFormInterface) {
          $plugin_state = $this->createPluginState($type, $form_state);
          $plugin->validateConfigurationForm($form, $plugin_state);
          $form_state->setValue($type . '_settings', $plugin_state->getValues());
          $this->moveFormStateErrors($plugin_state, $form_state);
        }
      }

      // If the provider accepts a key value, get the processed value
      // from the Key Input plugin.
      if ($this->entity->getKeyProvider()->getPluginDefinition()['key_input']['accepted']) {
        $processed_key_value = $this->entity->getKeyInput()->processSubmittedKeyValue($form_state);
        // TODO: Add validation by key type plugin here.
        $form_state->set('processed_key_value', $processed_key_value);
      }
      else {
        $form_state->set('processed_key_value', FALSE);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($this->entity->getPlugins() as $type => $plugin) {
      if ($plugin instanceof PluginFormInterface) {
        $plugin_state = $this->createPluginState($type, $form_state);
        $plugin->submitConfigurationForm($form, $plugin_state);
        $form_state->setValue($type . '_settings', $plugin_state->getValues());
      }
    }

    // If a key value has been processed by the key input plugin,
    // send it to the key provider plugin to set it.
    $processed_key_value = $form_state->get('processed_key_value');
    if (isset($processed_key_value)) {
      $this->entity->setKeyValue($processed_key_value);
    }

    parent::submitForm($form, $form_state);
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

  /**
   * Update the Key Type plugin.
   */
  protected function updateKeyType() {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    /** @var $plugin \Drupal\key\Plugin\KeyPluginInterface */
    $plugin = $key->getKeyType();

    // If an original key exists, restore the plugin configuration
    // from it. Otherwise, reset to the default configuration.
    if ($this->originalKey) {
      $configuration = $this->originalKey->getKeyType()->getConfiguration();
    }
    else {
      $configuration = $plugin->defaultConfiguration();
    }

    $plugin->setConfiguration($configuration);
  }

  /**
   * Update the Key Provider plugin.
   */
  protected function updateKeyProvider() {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    /** @var $plugin \Drupal\key\Plugin\KeyPluginInterface */
    $plugin = $key->getKeyProvider();

    $key->setPlugin('key_provider', $plugin->getPluginId());

    // If an original key exists, restore the plugin configuration
    // from it. Otherwise, reset to the default configuration.
    if ($this->originalKey) {
      $configuration = $this->originalKey->getKeyProvider()->getConfiguration();
    }
    else {
      $configuration = $plugin->defaultConfiguration();
    }

    $plugin->setConfiguration($configuration);
  }

  /**
   * Update the Key Input plugin.
   */
  protected function updateKeyInput() {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    $current_input_id = $key->getKeyInput()->getPluginId();

    // 'None' is the default.
    $new_input_id = 'none';

    if ($key->getKeyProvider()->getPluginDefinition()['key_input']['accepted']) {
      $new_input_id = 'text_field';
    }

    if ($current_input_id != $new_input_id) {
      /** @var $plugin \Drupal\key\Plugin\KeyPluginInterface */
      $plugin = $key->getKeyInput();

      $key->setPlugin('key_input', $new_input_id);
      $plugin->setConfiguration($plugin->defaultConfiguration());
    }
  }

  /**
   * Returns the original key entity
   *
   * @return \Drupal\key\Entity\Key
   *   The original key entity.
   */
  public function getOriginalKey() {
    return $this->originalKey;
  }

  /**
   * Creates a FormStateInterface object for a plugin.
   *
   * @param string $type
   *   The plugin type ID.
   * @param FormStateInterface $form_state
   *   The form state to copy values from.
   *
   * @return FormStateInterface
   *   A new form state object.
   */
  protected function createPluginState($type, FormStateInterface $form_state) {
    // Clone the form state.
    $plugin_state = clone $form_state;

    // Clear the values, except for this plugin type's settings.
    $plugin_state->setValues($form_state->getValue($type . '_settings', []));

    return $plugin_state;
  }

  /**
   * Moves form errors from one form state to another.
   *
   * @param \Drupal\Core\Form\FormStateInterface $from
   *   The form state object to move from.
   * @param \Drupal\Core\Form\FormStateInterface $to
   *   The form state object to move to.
   */
  protected function moveFormStateErrors(FormStateInterface $from, FormStateInterface $to) {
    foreach ($from->getErrors() as $name => $error) {
      $to->setErrorByName($name, $error);
    }
  }

}
