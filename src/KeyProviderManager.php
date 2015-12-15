<?php
/**
 * @file
 * Contains \Drupal\key\KeyProviderManager.
 */

namespace Drupal\key;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a Key Provider plugin manager.
 *
 */
class KeyProviderManager extends DefaultPluginManager {

  /**
   * Constructs a new KeyProviderManager.
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
    parent::__construct('Plugin/KeyProvider', $namespaces, $module_handler, 'Drupal\key\KeyProviderInterface', 'Drupal\key\Annotation\KeyProvider');
    $this->alterInfo('key_provider_info');
    $this->setCacheBackend($cache_backend, 'key_provider');
  }

}
