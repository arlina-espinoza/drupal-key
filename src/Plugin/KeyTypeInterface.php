<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyTypeInterface.
 */

namespace Drupal\key\Plugin;

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
   * @param string $key_value
   *   The key value
   * @param \Drupal\key\KeyInterface $key
   *   The key entity object.
   */
  public function validateKeyValue($key_value, KeyInterface $key);

}
