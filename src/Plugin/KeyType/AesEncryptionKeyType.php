<?php

/**
 * @file
 * Contains Drupal\key\KeyProvider\AesEncryptionKeyType.
 */

namespace Drupal\key\Plugin\KeyType;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Crypt;
use Drupal\key\Plugin\KeyTypeBase;
use Drupal\key\Plugin\KeyPluginFormInterface;

/**
 * Adds an example key type.
 *
 * @KeyType(
 *   id = "aes_encryption",
 *   label = @Translation("AES Encryption"),
 *   description = @Translation("Used for encrypting and decrypting data with the Advanced Encryption Standard (AES) cipher."),
 *   group = "encryption",
 *   key_value = {
 *     "plugin" = "text_field"
 *   }
 * )
 */
class AesEncryptionKeyType extends KeyTypeBase implements KeyPluginFormInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'key_size' => '128_bits',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key_size'] = array(
      '#type' => 'select',
      '#title' => $this->t('Key size'),
      '#description' => $this->t('The size of the key in bits. 128 bits is 16 bytes.'),
      '#options' => array(
        '128_bits' => $this->t('128 bits'),
        '192_bits' => $this->t('192 bits'),
        '256_bits' => $this->t('256 bits'),
      ),
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
    $bytes = substr($configuration['key_size'], 0, 3) / 8;
    $random_key = Crypt::randomBytes($bytes);

    return $random_key;
  }

  /**
   * {@inheritdoc}
   */
  public function validateKeyValue(array $form, FormStateInterface $form_state, $key_value) {
    // Validate the key size.
    $bytes = substr($form_state->getValues()['key_size'], 0, 3) / 8;
    if (strlen($key_value) != $bytes) {
      $form_state->setErrorByName('key_size', $this->t('The selected key size does not match the actual size of the key.'));
      return;
    }
  }

}
