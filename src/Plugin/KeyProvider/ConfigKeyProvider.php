<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProvider\ConfigKeyProvider.
 */

namespace Drupal\key\Plugin\KeyProvider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyProviderBase;
use Drupal\key\KeyInterface;

/**
 * Adds a key provider that allows a key to be stored in configuration.
 *
 * @KeyProvider(
 *   id = "config",
 *   label = @Translation("Configuration"),
 *   description = @Translation("Allows a key to be stored in Drupal's configuration system."),
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
  public function defaultConfiguration() {
    return [
      'key_value' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key_value'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Key value'),
      '#description' => $this->t("Enter the key to save in Drupal's configuration system."),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['key_value'],
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key) {
    return $this->configuration['key_value'];
  }

}
