<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyPluginInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface for all Key plugins.
 */
interface KeyPluginInterface extends ContainerFactoryPluginInterface, PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * Returns the type of plugin.
   *
   * @return string
   *   The type of plugin, "key_type", "key_provider", or "key_input".
   */
  public function pluginType();

}
