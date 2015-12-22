<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\ConfigurableKeyTypeInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\key\KeyInterface;

/**
 * Provides an interface for configurable Key Type plugins.
 */
interface ConfigurableKeyTypeInterface extends PluginInspectionInterface {

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
