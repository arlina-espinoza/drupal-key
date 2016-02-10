<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyType\EncryptionKeyType.
 */

namespace Drupal\key\Plugin\KeyType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Crypt;
use Drupal\key\Plugin\KeyTypeBase;
use Drupal\key\Plugin\KeyPluginFormInterface;

/**
 * Defines a generic key type for encryption.
 *
 * @KeyType(
 *   id = "encryption",
 *   label = @Translation("Encryption"),
 *   description = @Translation("A generic key type to use for an encryption key that does not belong to any other defined key type."),
 *   group = "encryption",
 *   key_value = {
 *     "plugin" = "text_field"
 *   }
 * )
 */
class EncryptionKeyType extends KeyTypeBase implements KeyPluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'key_size' => 128,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key_size'] = array(
      '#type' => 'select',
      '#title' => $this->t('Key size'),
      '#description' => $this->t('The size of the key in bits, with 8 bits per byte.'),
      '#options' => array_combine(range(32, 512, 32), range(32, 512, 32)),
      '#default_value' => $this->getConfiguration()['key_size'],
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValues());
  }

  /**
   * {@inheritdoc}
   */
  public static function generateKeyValue(array $configuration) {
    if (!empty($configuration['key_size'])) {
      $bytes = $configuration['key_size'] / 8;
    }
    else {
      // If no key size has been defined, use 32 bytes as the default.
      $bytes = 32;
    }
    $random_key = Crypt::randomBytes($bytes);

    return $random_key;
  }

  /**
   * {@inheritdoc}
   */
  public function validateKeyValue(array $form, FormStateInterface $form_state, $key_value) {
    if (empty($form_state->getValue('key_size'))) {
      return;
    }

    // Validate the key size.
    $bytes = $form_state->getValue('key_size') / 8;
    if (strlen($key_value) != $bytes) {
      $form_state->setErrorByName('key_size', $this->t('The selected key size does not match the actual size of the key.'));
    }
  }

}
