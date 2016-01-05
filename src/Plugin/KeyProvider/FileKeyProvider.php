<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyProvider\FileKeyProvider.
 */

namespace Drupal\key\Plugin\KeyProvider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyProviderBase;
use Drupal\key\KeyInterface;

/**
 * Adds a key provider that allows a key to be stored in a file.
 *
 * @KeyProvider(
 *   id = "file",
 *   label = @Translation("File"),
 *   description = @Translation("The File key provider allows a key to be stored in a file, preferably outside of the web root."),
 *   storage_method = "file",
 *   key_input = {
 *     "accepted" = FALSE,
 *     "required" = FALSE,
 *     "editable" = FALSE
 *   }
 * )
 */
class FileKeyProvider extends KeyProviderBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'file_location' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['file_location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('File location'),
      '#description' => $this->t('The location of the file in which the key will be stored. The path may be absolute (e.g., %abs), relative to the Drupal directory (e.g., %rel), or defined using a stream wrapper (e.g., %str).', array(
        '%abs' => '/etc/keys/foobar.key',
        '%rel' => '../keys/foobar.key',
        '%str' => 'private://keys/foobar.key',
      )),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['file_location'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $key_provider_settings = $form_state->getValues();
    $file = $key_provider_settings['file_location'];

    // Does the file exist?
    if (!is_file($file)) {
      $form_state->setErrorByName('file_location', $this->t('There is no file at the specified location.'));
      return;
    }

    // Is the file readable?
    if ((!is_readable($file))) {
      $form_state->setErrorByName('file_location', $this->t('The file at the specified location is not readable.'));
      return;
    }

    parent::validateConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue(KeyInterface $key) {
    $file = $this->configuration['file_location'];

    // Make sure the file exists and is readable.
    if (!is_file($file) || !is_readable($file)) {
      return NULL;
    }

    $key = file_get_contents($file);

    return $key;
  }

}
