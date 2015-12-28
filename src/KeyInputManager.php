<?php
/**
 * @file
 * Contains \Drupal\key\KeyInputManager.
 */

namespace Drupal\key;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;


/**
 * Provides a Key Input plugin manager.
 *
 */
class KeyInputManager extends DefaultPluginManager {

  /**
   * Constructs a new KeyInputManager.
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
    parent::__construct('Plugin/KeyInput', $namespaces, $module_handler, 'Drupal\key\Plugin\KeyInputInterface', 'Drupal\key\Annotation\KeyInput');
    $this->alterInfo('key_input_info');
    $this->setCacheBackend($cache_backend, 'key_input');
  }

}
