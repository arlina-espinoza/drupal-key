<?php

namespace Drupal\key;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides key overrides for configuration.
 */
class KeyConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var array
   */
  protected $mapping;

  /**
   * @var bool
   */
  protected $inOverride = FALSE;

  /**
   * Creates a new KeyConfigOverride instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    if ($this->inOverride) {
      return [];
    }
    $this->inOverride = TRUE;

    $overrides = [];

    $mapping = $this->getMapping();
    $key_storage = $this->entityTypeManager->getStorage('key');

    foreach ($names as $name) {
      if (!isset($mapping[$name])) {
        continue;
      }

      $config = [];

      foreach ($mapping[$name] as $item => $key_id) {
        $key = $key_storage->load($key_id);
        if (!$key) {
          continue;
        }

        $key_value = $key->getKeyValue();
        if (!$key_value) {
          continue;
        }

        $config[$item] = $key_value;
      }

      if ($config) {
        $overrides[$name] = $config;
      }
    }

    $this->inOverride = FALSE;

    return $overrides;
  }

  /**
   * {@inheritdoc} 
   */
  public function getCacheSuffix() {
    return 'key_config_override';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name) {
    $cache_metadata = new CacheableMetadata();
    $cache_metadata->addCacheTags(['extensions']);
    return $cache_metadata;
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

  protected function getMapping() {
    if (!$this->mapping) {
      $overrides = $this->entityTypeManager
        ->getStorage('key_config_override')
        ->loadMultiple();

      foreach ($overrides as $override) {
        $type = $override->getConfigType();
        $name = $override->getConfigName();
        $item = $override->getConfigItem();
        $key_id = $override->getKeyId();

        if ($type !== 'system.simple') {
          $def = $this->entityTypeManager->getDefinition($type);
          $name = $def->getConfigPrefix() . '.' . $name;
        }

        $this->mapping[$name][$item] = $key_id;
      }
    }

    return $this->mapping;
  }

}
