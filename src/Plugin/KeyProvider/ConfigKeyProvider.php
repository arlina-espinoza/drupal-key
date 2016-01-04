<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProvider\ConfigKeyProvider.
 */

namespace Drupal\key\Plugin\KeyProvider;

use Drupal\key\Plugin\KeyProviderBase;
use Drupal\key\KeyInterface;

/**
 * Adds a key provider that allows a key to be stored in configuration.
 *
 * @KeyProvider(
 *   id = "config",
 *   label = @Translation("Configuration"),
 *   description = @Translation("The Configuration key provider stores the key in Drupal's configuration system."),
 *   storage_method = "config",
 *   key_input = {
 *     "accepted" = TRUE,
 *     "required" = TRUE,
 *     "editable" = TRUE
 *   }
 * )
 */
class ConfigKeyProvider extends KeyProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key) {
    return isset($this->configuration['key_value']) ? $this->configuration['key_value'] : '';
  }

  public function setKeyValue(KeyInterface $key, $key_value) {
    $this->configuration['key_value'] = $key_value;

    return $key_value;
  }

}
