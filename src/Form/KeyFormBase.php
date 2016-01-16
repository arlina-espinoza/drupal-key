<?php

/**
 * @file
 * Contains \Drupal\key\Form\KeyFormBase.
 */

namespace Drupal\key\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyPluginFormInterface;
use Drupal\key\Plugin\KeyProviderSettableValueInterface;
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
   * @var \Drupal\key\Entity\Key|NULL
   *   The original key entity or NULL if this is a new key.
   */
  protected $originalKey = NULL;

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
  public function buildForm(array $form, FormStateInterface $form_state) {
    // If the form is rebuilding.
    if ($form_state->isRebuilding()) {

      // If a key type change triggered the rebuild.
      if ($form_state->getTriggeringElement()['#name'] == 'key_type') {
        // Update the type and input plugins.
        $this->updateKeyType($form_state);
        $this->updateKeyInput($form_state);
      }

      // If a key provider change triggered the rebuild.
      if ($form_state->getTriggeringElement()['#name'] == 'key_provider') {
        // Update the provider and input plugins.
        $this->updateKeyProvider($form_state);
        $this->updateKeyInput($form_state);
      }
    }
    // If the form is not rebuilding.
    else {
      // Update the input plugin.
      $this->updateKeyInput($form_state);
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

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
    if ($key->getKeyType() instanceof KeyPluginFormInterface) {
      $plugin_form_state = $this->createPluginFormState('key_type', $form_state);
      $form['settings']['type_section']['key_type_settings'] += $key->getKeyType()->buildConfigurationForm([], $plugin_form_state);
      $form_state->setValue('key_type_settings', $plugin_form_state->getValues());
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
    if ($key->getKeyProvider() instanceof KeyPluginFormInterface) {
      $plugin_form_state = $this->createPluginFormState('key_provider', $form_state);
      $form['settings']['provider_section']['key_provider_settings'] += $key->getKeyProvider()->buildConfigurationForm([], $plugin_form_state);
      $form_state->setValue('key_provider_settings', $plugin_form_state->getValues());
    }

    // Key input section.
    $form['settings']['input_section'] = array(
      '#type' => 'details',
      '#title' => $this->t('Value'),
      '#open' => TRUE,
    );
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
    if ($key->getKeyInput() instanceof KeyPluginFormInterface) {
      $plugin_form_state = $this->createPluginFormState('key_input', $form_state);
      $form['settings']['input_section']['key_input_settings'] += $key->getKeyInput()->buildConfigurationForm([], $plugin_form_state);
      $form_state->setValue('key_input_settings', $plugin_form_state->getValues());
    }

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!$form_state->isSubmitted()) {
      return;
    }

    // Make sure each plugin settings field is an array.
    foreach ($this->entity->getPluginTypes() as $type) {
      if (empty($form_state->getValue($type . '_settings'))) {
        $form_state->setValue($type . '_settings', []);
      }
    }

    $processed_values = array(
      'submitted' => NULL,
      'processed_submitted' => NULL,
    );
    foreach ($this->entity->getPlugins() as $type => $plugin) {
      if ($plugin instanceof KeyPluginFormInterface) {
        $plugin_form_state = $this->createPluginFormState($type, $form_state);

        // Special behavior for the Key Input plugin.
        if ($type == 'key_input') {
          // If the provider accepts a key value.
          if ($this->entity->getKeyProvider()->getPluginDefinition()['key_value']['accepted']) {
            $processed_values = $plugin->processSubmittedKeyValue($plugin_form_state);
          }
        }

        $plugin->validateConfigurationForm($form, $plugin_form_state);
        $form_state->setValue($type . '_settings', $plugin_form_state->getValues());
        $this->moveFormStateErrors($plugin_form_state, $form_state);
        $this->moveFormStateStorage($plugin_form_state, $form_state);
      }
    }

    // Store the submitted and processed key values in form state.
    $key_value_data = $form_state->get('key_value');
    $key_value_data['submitted'] = $processed_values['submitted'];
    $key_value_data['processed_submitted'] = $processed_values['processed_submitted'];
    $form_state->set('key_value', $key_value_data);

    // Allow the Key Type plugin to validate the key value. Use the processed
    // key value if there is one. Otherwise, retrieve the key value using the
    // key provider.
    if (!empty($processed_values['processed_submitted'])) {
      $key_value = $processed_values['processed_submitted'];
    }
    else {
      $key_value = $this->entity->getKeyValue();
    }
    $plugin_form_state = $this->createPluginFormState('key_type', $form_state);
    $this->entity->getKeyType()->validateKeyValue($form, $plugin_form_state, $key_value);
    $form_state->setValue('key_type_settings', $plugin_form_state->getValues());
    $this->moveFormStateErrors($plugin_form_state, $form_state);
    $this->moveFormStateStorage($plugin_form_state, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $key_value_data = $form_state->get('key_value');

    foreach ($this->entity->getPlugins() as $type => $plugin) {
      if ($plugin instanceof KeyPluginFormInterface) {
        $plugin_form_state = $this->createPluginFormState($type, $form_state);
        $plugin->submitConfigurationForm($form, $plugin_form_state);
        $form_state->setValue($type . '_settings', $plugin_form_state->getValues());
      }
    }

    // Set the key value if the key provider allows it and either the
    // provider has changed or the submitted value is not the same as
    // the obscured value.

    // If the key provider allows a key value to be set.
    if ($this->entity->getKeyProvider() instanceof KeyProviderSettableValueInterface) {
      // If either the key provider has changed or the submitted value
      // is not the same as the obscured value.
      if ($this->originalKey->getKeyProvider()
          ->getPluginId() != $this->entity->getKeyProvider()
          ->getPluginId() || $key_value_data['submitted'] != $key_value_data['obscured']
      ) {
        // Set the key value.
        $this->entity->setKeyValue($key_value_data['processed_submitted']);
      }
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
  protected function updateKeyType(FormStateInterface $form_state) {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    /** @var $plugin \Drupal\key\Plugin\KeyPluginInterface */
    $plugin = $key->getKeyType();

    $key->setPlugin('key_type', $plugin->getPluginId());

    // If an original key exists and the plugin ID matches the existing one.
    if ($this->originalKey && $this->originalKey->getKeyType()->getPluginId() == $plugin->getPluginId()) {
      // Use the configuration from the original key's plugin.
      $configuration = $this->originalKey->getKeyType()->getConfiguration();
    }
    else {
      // Use the plugin's default configuration.
      $configuration = $plugin->defaultConfiguration();
    }

    $plugin->setConfiguration($configuration);
    $form_state->setValue('key_type_settings', []);
    $form_state->getUserInput()['key_type_settings'] = [];
  }

  /**
   * Update the Key Provider plugin.
   */
  protected function updateKeyProvider(FormStateInterface $form_state) {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    /** @var $plugin \Drupal\key\Plugin\KeyPluginInterface */
    $plugin = $key->getKeyProvider();

    $key->setPlugin('key_provider', $plugin->getPluginId());

    // If an original key exists and the plugin ID matches the existing one.
    if ($this->originalKey && $this->originalKey->getKeyProvider()->getPluginId() == $plugin->getPluginId()) {
      // Use the configuration from the original key's plugin.
      $configuration = $this->originalKey->getKeyProvider()->getConfiguration();
    }
    else {
      // Use the plugin's default configuration.
      $configuration = $plugin->defaultConfiguration();
    }

    $plugin->setConfiguration($configuration);
    $form_state->setValue('key_provider_settings', []);
    $form_state->getUserInput()['key_provider_settings'] = [];
  }

  /**
   * Update the Key Input plugin.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  protected function updateKeyInput(FormStateInterface $form_state) {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    /** @var $plugin \Drupal\key\Plugin\KeyPluginInterface */
    $plugin = $key->getKeyInput();

    // Get the current key value data.
    $key_value_data = $form_state->get('key_value');

    // Determine which Key Input plugin should be used.
    $key_input_id = 'none';
    if ($key->getKeyProvider()->getPluginDefinition()['key_value']['accepted']) {
      $key_input_id = $key->getKeyType()->getPluginDefinition()['key_value']['plugin'];
    }

    // Set the Key Input plugin.
    $key->setPlugin('key_input', $key_input_id);

    // If an original key exists, and the provider plugin IDs match.
    if ($this->originalKey
      && $this->originalKey->getKeyProvider()->getPluginId() == $key->getKeyProvider()->getPluginId())
    {
      // Use the configuration from the original key's plugin.
      $configuration = $this->originalKey->getKeyInput()->getConfiguration();

      // Set the current key value to be the obscured value.
      $key_value_data['current'] = $key_value_data['obscured'];
    }
    else {
      // Use the plugin's default configuration.
      $configuration = $plugin->defaultConfiguration();

      // Clear the current key value.
      $key_value_data['current'] = '';
    }

    $plugin->setConfiguration($configuration);
    $form_state->setValue('key_input_settings', []);
    $form_state->getUserInput()['key_input_settings'] = [];
    $form_state->set('key_value', $key_value_data);
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
   *   A clone of the form state object with values from the plugin.
   */
  protected function createPluginFormState($type, FormStateInterface $form_state) {
    // Clone the form state.
    $plugin_form_state = clone $form_state;

    // Clear the values, except for this plugin type's settings.
    $plugin_form_state->setValues($form_state->getValue($type . '_settings', []));

    return $plugin_form_state;
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

  /**
   * Moves storage variables from one form state to another.
   *
   * @param \Drupal\Core\Form\FormStateInterface $from
   *   The form state object to move from.
   * @param \Drupal\Core\Form\FormStateInterface $to
   *   The form state object to move to.
   */
  protected function moveFormStateStorage(FormStateInterface $from, FormStateInterface $to) {
    foreach ($from->getStorage() as $index => $value) {
      $to->set($index, $value);
    }
  }

}
