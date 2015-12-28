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
   * Returns the value of a key from the key provider.
   *
   * @param \Drupal\key\KeyInterface $key
   *
   * @return string
   */
  public function getKeyValue(KeyInterface $key);

}
