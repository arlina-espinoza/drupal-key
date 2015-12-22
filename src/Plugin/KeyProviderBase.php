<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyProviderBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base class for Key Provider plugins.
 */
abstract class KeyProviderBase extends PluginBase implements KeyProviderInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

}
