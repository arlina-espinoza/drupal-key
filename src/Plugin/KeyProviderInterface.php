<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProviderInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\key\KeyInterface;

/**
 * Provides an interface for Key Provider plugins.
 */
interface KeyProviderInterface {

  /**
   * Returns the value of a key.
   *
   * @param \Drupal\key\KeyInterface $key
   *   The key whose value will be retrieved.
   *
   * @return string
   *   The key value.
   */
  public function getKeyValue(KeyInterface $key);

  /**
   * Sets the value of a key.
   *
   * @param string $key_value
   *   The key value.
   *
   * @return string|bool
   *   The key value if successful, FALSE if unsuccessful.
   */
  public function setKeyValue($key_value);

}
