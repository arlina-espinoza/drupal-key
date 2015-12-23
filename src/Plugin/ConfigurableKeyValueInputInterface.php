<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\ConfigurableKeyValueInputInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\key\KeyInterface;

/**
 * Provides an interface for all Key Value Input plugins.
 */
interface ConfigurableKeyValueInputInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * @param \Drupal\key\KeyInterface $key
   *   The key entity object.
   * @return string
   *   The processed submitted key value.
   */
  public function processSubmittedKeyValue(KeyInterface $key);

}
