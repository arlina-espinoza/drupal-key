<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyTypeBase.
 */

namespace Drupal\key\Plugin;

use Drupal\key\KeyInterface;

/**
 * Defines a base class for Key Type plugins.
 */
abstract class KeyTypeBase extends KeyPluginBase implements KeyTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function generateKeyValue(KeyInterface $key) {
    // Generate a random 16-character password.
    return user_password(16);
  }

  /**
   * {@inheritdoc}
   */
  public function validateKeyValue(KeyInterface $key, $key_value) {
  }

}
