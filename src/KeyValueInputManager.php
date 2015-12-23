<?php
/**
 * @file
 * Contains \Drupal\key\KeyValueInputManager.
 */

namespace Drupal\key;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;


/**
 * Provides a Key Value Input plugin manager.
 *
 */
class KeyValueInputManager extends DefaultPluginManager {

  /**
   * Constructs a new KeyValueInputManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/KeyValueInput', $namespaces, $module_handler, 'Drupal\key\Plugin\ConfigurableKeyValueInputInterface', 'Drupal\key\Annotation\KeyValueInput');
    $this->alterInfo('key_value_input_info');
    $this->setCacheBackend($cache_backend, 'key_value_input');
  }

}
