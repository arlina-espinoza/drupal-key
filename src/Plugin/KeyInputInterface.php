<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyInputInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an interface for Key Input plugins.
 */
interface KeyInputInterface {

  /**
   * Process a submitted key value.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return string
   *   The processed key value.
   */
  public function processSubmittedKeyValue(FormStateInterface $form_state);

  /**
   * Process an existing key value.
   *
   * @param string $key_value
   *   The existing key value.
   *
   * @return array
   *   The submitted value (with index "submitted") and the processed
   *   value (with index "processed_submitted").
   */
  public function processExistingKeyValue($key_value);

}
