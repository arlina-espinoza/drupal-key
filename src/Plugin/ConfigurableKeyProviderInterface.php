<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\ConfigurableKeyProviderInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface for configurable Key Provider plugins.
 */
interface ConfigurableKeyProviderInterface extends KeyProviderInterface, ConfigurablePluginInterface, PluginFormInterface {
}
