<?php

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
    return $this->entityTypeManager->getStorage('key')->loadByProperties(['key_provider' => $key_provider_id]);
  }

  /**
   * {@inheritdoc}
   */
  public function getKeysByType($key_type_id) {
    return $this->entityTypeManager->getStorage('key')->loadByProperties(['key_type' => $key_type_id]);
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
  public function getKeyNamesAsOptions($filters = []) {
    $options = [];
    $keys = [];

    // TODO: Make filtering more sophisticated.
    if (empty($filters)) {
      $keys = $this->getKeys();
    }
    else {
      if (isset($filters['type']) && isset($filters['provider'])) {
        $keys = array_intersect_key($this->getKeysByType($filters['type']), $this->getKeysByProvider($filters['provider']));
      }
      elseif (isset($filters['type'])) {
        $keys = $this->getKeysByType($filters['type']);
      }
      elseif (isset($filters['provider'])) {
        $keys = $this->getKeysByProvider($filters['provider']);
      }
    }

    foreach ($keys as $key) {
      $key_id = $key->id();
      $key_title = $key->label();
      $options[$key_id] = (string) $key_title;
    }

    return $options;
  }

}
