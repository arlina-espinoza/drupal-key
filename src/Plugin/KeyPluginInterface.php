<?php

namespace Drupal\key\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\DependentPluginInterface;

/**
 * Provides an interface for all Key plugins.
 */
interface KeyPluginInterface extends ContainerFactoryPluginInterface, PluginInspectionInterface, ConfigurableInterface, DependentPluginInterface {

  /**
   * Returns the type of plugin.
   *
   * @return string
   *   The type of plugin: "key_type", "key_provider", or "key_input".
   */
  public function getPluginType();

}
