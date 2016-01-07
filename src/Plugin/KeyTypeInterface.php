<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyTypeInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyInterface;

/**
 * Provides an interface for all Key Type plugins.
 */
interface KeyTypeInterface {

  /**
   * Allows the Key Type plugin to generate a key value.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key entity object.
   *
   * @return string
   *   The generated key value.
   */
  public function generateKeyValue(KeyInterface $key);

  /**
   * Allows the Key Type plugin to validate the key value.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the plugin form.
   * @param string|null $key_value
   *   The key value to be validated.
   */
  public function validateKeyValue(array $form, FormStateInterface $form_state, $key_value);

}
