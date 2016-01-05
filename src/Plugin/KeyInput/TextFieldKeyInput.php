<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyInput\TextFieldKeyInput.
 */

namespace Drupal\key\Plugin\KeyInput;

use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyInputBase;

/**
 * Defines a key input that provides a simple text field.
 *
 * @KeyInput(
 *   id = "text_field",
 *   label = @Translation("Text field"),
 *   description = @Translation("A simple text field.")
 * )
 */
class TextFieldKeyInput extends KeyInputBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['key_value'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Key value'),
      '#required' => TRUE,
      '#default_value' => $this->getConfiguration()['key_value'],
    );

    return $form;
  }

}
