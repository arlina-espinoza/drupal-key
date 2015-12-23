<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyValueInput\NoneKeyValueInput.
 */

namespace Drupal\key\Plugin\KeyValueInput;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\ConfigurableKeyValueInputBase;
use Drupal\key\KeyInterface;

/**
 * Defines a key value input for providers that don't accept a value.
 *
 * @KeyValueInput(
 *   id = "none",
 *   label = @Translation("None"),
 *   description = @Translation("A key value input for providers that don't accept a value.")
 * )
 */
class NoneKeyValueInput extends ConfigurableKeyValueInputBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key_value_message'] = array(
      '#markup' => $this->t("The currently selected provider does not accept a key value."),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration = array();
  }

  /**
   * {@inheritdoc}
   */
  public function processSubmittedKeyValue(KeyInterface $key) {
    return '';
  }

}
