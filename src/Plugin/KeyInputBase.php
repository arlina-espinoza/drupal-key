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
abstract class KeyInputBase extends KeyPluginBase implements KeyInputInterface {

  /**
   * {@inheritdoc}
   */
  public function processSubmittedKeyValue(FormStateInterface $form_state) {
    // This is the default behavior. If a field named 'key_value' exists in
    // the key input settings, remove it from the settings and return the
    // value. Otherwise, return an empty string. Input plugins can override
    // this behavior to perform more complex processing.
    $key_input_settings = $form_state->getValues();
    $processed_key_value = '';
    if (isset($key_input_settings['key_value'])) {
      $processed_key_value = $key_input_settings['key_value'];
      unset($key_input_settings['key_value']);
      $form_state->setValues($key_input_settings);
    }

    return $processed_key_value;
  }

}
