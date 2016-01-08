<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyTypeBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a base class for Key Type plugins.
 */
abstract class KeyTypeBase extends KeyPluginBase implements KeyTypeInterface {

  /**
   * {@inheritdoc}
   */
  public static function generateKeyValue(array $configuration) {
    // Generate a random 16-character password.
    return user_password(16);
  }

  /**
   * {@inheritdoc}
   */
  public function validateKeyValue(array $form, FormStateInterface $form_state, $key_value) {
    // Validation of the key value is optional.
  }

}
