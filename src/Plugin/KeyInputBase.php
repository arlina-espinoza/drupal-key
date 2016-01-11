<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyTypeBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a base class for Key Type plugins.
 */
abstract class KeyInputBase extends KeyPluginBase implements KeyInputInterface, KeyPluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValues());
  }

  /**
   * {@inheritdoc}
   */
  public function processSubmittedKeyValue(FormStateInterface $form_state) {
    // This is the default behavior. If a field named 'key_value' exists in
    // the key input settings, remove it from the settings and return it as
    // the submitted value and the processed value. Otherwise, return NULL
    // for each. Input plugins can override this behavior to perform more
    // complex processing.
    $processed_values = array(
      'submitted' => NULL,
      'processed_submitted' => NULL,
    );
    $key_input_settings = $form_state->getValues();
    if (isset($key_input_settings['key_value'])) {
      $processed_values['submitted'] = $processed_values['processed_submitted'] = $key_input_settings['key_value'];
      unset($key_input_settings['key_value']);
      $form_state->setValues($key_input_settings);
    }

    return $processed_values;
  }

  /**
   * {@inheritdoc}
   */
  public function processExistingKeyValue($key_value) {
    // The default behavior is to return the key value as-is.
    return $key_value;
  }

}
