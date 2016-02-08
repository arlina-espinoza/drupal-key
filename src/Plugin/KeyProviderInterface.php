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
   * Obscures a key value.
   *
   * @param string $key_value
   *   The key value to obscure.
   * @param array $options
   *   Options to use when obscuring the value.
   *
   * @return string
   *   The obscured key value.
   */
  public static function obscureKeyValue($key_value, array $options);

}
