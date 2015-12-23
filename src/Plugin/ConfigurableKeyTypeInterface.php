<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\ConfigurableKeyTypeInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface for configurable Key Type plugins.
 */
interface ConfigurableKeyTypeInterface extends KeyTypeInterface, ConfigurablePluginInterface, PluginFormInterface {
}
