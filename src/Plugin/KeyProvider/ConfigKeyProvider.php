<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProvider\ConfigKeyProvider.
 */

namespace Drupal\key\Plugin\KeyProvider;

use Drupal\key\Plugin\KeyProviderBase;
use Drupal\key\Plugin\KeyProviderSettableValueInterface;
use Drupal\key\KeyInterface;

/**
 * Adds a key provider that allows a key to be stored in configuration.
 *
 * @KeyProvider(
 *   id = "config",
 *   label = @Translation("Configuration"),
 *   description = @Translation("The Configuration key provider stores the key in Drupal's configuration system."),
 *   storage_method = "config",
 *   key_value = {
 *     "accepted" = TRUE,
 *     "required" = TRUE,
 *     "editable" = TRUE
 *   }
 * )
 */
class ConfigKeyProvider extends KeyProviderBase implements KeyProviderSettableValueInterface {

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key) {
    return isset($this->configuration['key_value']) ? $this->configuration['key_value'] : '';
  }

  /**
   * {@inheritdoc}
   */
  public function setKeyValue(KeyInterface $key, $key_value) {
    if ($this->configuration['key_value'] = $key_value) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteKeyValue(KeyInterface $key) {
    // Nothing needs to be done, since the value will have been deleted
    // with the Key entity.
    return TRUE;
  }

}
