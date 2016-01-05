<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyProviderBase.
 */

namespace Drupal\key\Plugin;

/**
 * Defines a base class for Key Provider plugins.
 */
abstract class KeyProviderBase extends KeyPluginBase implements KeyProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function setKeyValue($key_value) {
    // By default, providers do not set a key value.
    return FALSE;
  }

}
