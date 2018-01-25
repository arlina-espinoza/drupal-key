<?php

namespace Drupal\key;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Provides key overrides for configuration.
 */
class KeyConfigOverrides implements ConfigFactoryOverrideInterface {

  /**
   * @var array
   */
  protected $mapping;

  /**
   * @var bool
   */
  protected $inOverride = FALSE;

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    if ($this->inOverride) {
      return [];
    }
    $this->inOverride = TRUE;

    $mapping = $this->getMapping();
    if (!$mapping) {
      return [];
    }

    $overrides = [];

    foreach ($names as $name) {
      if (!key_exists($name, $mapping)) {
        continue;
      }

      $override = [];

      foreach ($mapping[$name] as $config_item => $key_id) {
        $key_value = \Drupal::service('key.repository')->getKey($key_id)->getKeyValue();

        if (!isset($key_value)) {
          continue;
        }

        // Turn the dot-separated configuration item name into a nested
        // array and set the value.
        $config_item_parents = explode('.', $config_item);
        NestedArray::setValue($override, $config_item_parents, $key_value);
      }

      if ($override) {
        $overrides[$name] = $override;
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

  /**
   * Get a mapping of key configuration overrides.
   *
   * @return array
   *   A mapping of key configuration overrides.
   */
  protected function getMapping() {
    if (!$this->mapping) {
      $mapping = [];
      $config_factory = \Drupal::configFactory();
      $override_ids = $config_factory->listAll('key.config_override.');
      $overrides = $config_factory->loadMultiple($override_ids);

      foreach ($overrides as $id => $override) {
        $override = $override->get();

        $config_id = '';
        if (!empty($override['config_prefix'])) {
          $config_id .= $override['config_prefix'] . '.';
        }
        if (isset($override['config_name'])) {
          $config_id .= $override['config_name'];
        }

        $config_item = $override['config_item'];
        $key_id = $override['key_id'];

        $mapping[$config_id][$config_item] = $key_id;
      }

      $this->mapping = $mapping;
    }

    return $this->mapping;
  }

}
