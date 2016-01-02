<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyTypeBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\KeyInterface;

/**
 * Defines a base class for Key Type plugins.
 */
abstract class KeyTypeBase extends KeyPluginBase implements KeyTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function getPluginType() {
    return 'key_type';
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValue('key_type_settings'));
  }

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
  public function validateKeyValue($key_value, KeyInterface $key) {
  }

}
