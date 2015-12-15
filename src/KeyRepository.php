<?php

/**
 * @file
 * Contains \Drupal\key\KeyRepository.
 */

namespace Drupal\key;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Provides a repository for Key configuration entities.
 */
class KeyRepository implements KeyRepositoryInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The key provider plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $keyProviderManager;

  /**
   * Constructs a new KeyRepository.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $key_provider_manager
   *   The key provider plugin manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, PluginManagerInterface $key_provider_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->keyProviderManager = $key_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeys(array $key_ids = NULL) {
    return $this->entityTypeManager->getStorage('key')->loadMultiple($key_ids);
  }

  /**
   * {@inheritdoc}
   */
  public function getKeysByProvider($key_provider_id) {
    return $this->entityTypeManager->getStorage('key')->loadByProperties(array('key_provider' => $key_provider_id));
  }

  /**
   * {@inheritdoc}
   */
  public function getKeysByStorageMethod($storage_method) {
    $key_providers = array_filter($this->keyProviderManager->getDefinitions(), function ($definition) use ($storage_method) {
      return $definition['storage_method'] == $storage_method;
    });

    $keys = [];
    foreach ($key_providers as $key_provider) {
      $keys = array_merge($keys, $this->getKeysByProvider($key_provider['id']));
    }
    return $keys;
  }

  /**
   * {@inheritdoc}
   */
  public function getKey($key_id) {
    return $this->entityTypeManager->getStorage('key')->load($key_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyNamesAsOptions() {
    $options = array();

    foreach ($this->getKeys() as $key) {
      $key_id = $key->id();
      $key_title = $key->label();
      $options[$key_id] = (string) $key_title;
    }

    return $options;
  }

}
