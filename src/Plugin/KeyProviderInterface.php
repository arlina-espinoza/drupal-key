<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProviderInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\key\KeyInterface;

/**
 * Provides an interface defining a Key Provider plugin.
 */
interface KeyProviderInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns the value of a key from the key provider.
   *
   * @param \Drupal\key\KeyInterface $key
   *
   * @return string
   */
  public function getKeyValue(KeyInterface $key);

}
