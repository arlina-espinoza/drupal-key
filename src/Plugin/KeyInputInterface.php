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

}
