<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyTypeInterface.
 */

namespace Drupal\key\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Provides an interface defining a Key Type plugin.
 */
interface KeyTypeInterface extends PluginInspectionInterface, ConfigurablePluginInterface, PluginFormInterface {}
