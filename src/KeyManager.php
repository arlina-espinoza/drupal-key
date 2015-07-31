<?php

/**
 * @file
 * Contains \Drupal\key\KeyManager.
 */

namespace Drupal\key;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Responsible for the key service.
 */
class KeyManager {

  /**
   * Create the KeyManager.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entityManager
   *   The entity manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $pluginManager
   *   The plugin manager.
   */
  public function __construct(EntityManagerInterface $entityManager, ConfigFactoryInterface $configFactory, PluginManagerInterface $pluginManager) {
    $this->entityManager = $entityManager;
    $this->configFactory = $configFactory;
    $this->pluginManager = $pluginManager;
  }

  /*
   * Loading all keys.
   */
  public function getKeys() {
    return $this->entityManager->getStorage('key')->loadMultiple();
  }

  /*
   * Loading keys that are of the specified key type.
   *
   * @param string $key_type
   *   The key type ID to use.
   */
  public function getKeysByType($key_type_id) {
    return $this->entityManager->getStorage('key')->loadByProperties(array('key_type' => $key_type_id));
  }

  /*
   * Loading keys that are of the specified key type.
   *
   * @param string $storage_method
   *   The storage method of the key type.
   */
  public function getKeysByStorageMethod($storage_method) {
    $key_types = array_filter($this->pluginManager->getDefinitions(), function ($definition) use ($storage_method) {
      return $definition['storage_method'] == $storage_method;
    });

    $keys = [];
    foreach ($key_types as $key_type_id => $key_type) {
      $keys = array_merge($keys, $this->getKeysByType($key_type_id));
    }
    return $keys;
  }

  /*
   * Loading a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKey($key_id) {
    return $this->entityManager->getStorage('key')->load($key_id);
  }

  /*
   * Loading key contents for a specific key.
   *
   * @param string $key_id
   *   The key ID to use.
   */
  public function getKeyValue($key_id) {
    return $this->entityManager->getStorage('key')->load($key_id)->getKeyValue();
  }

  /*
   * Loading the configured default key.
   */
  public function getDefaultKey() {
    $key_id = $this->configFactory->get('key.default_config')->get('default_key');
    if ($key_id) {
      return $this->entityManager->getStorage('key')->load($key_id);
    }
    return NULL;
  }

  /*
   * Loading the key contents for the configured default key.
   */
  public function getDefaultKeyContents() {
    $key_id = \Drupal::config('key.default_config')->get('default_key');
    if ($key_id) {
      $key = \Drupal::entityManager()->getStorage('key')->load($key_id);
      return $key->getContents();
    }
    return NULL;
  }

}
