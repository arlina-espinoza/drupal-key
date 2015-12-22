<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProviderInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\key\KeyInterface;

/**
 * Provides an interface for all Key Provider plugins.
 */
interface KeyProviderInterface extends PluginInspectionInterface {

  /**
   * Returns the value of a key from the key provider.
   *
   * @param \Drupal\key\KeyInterface $key
   *
   * @return string
   */
  public function getKeyValue(KeyInterface $key);

}
