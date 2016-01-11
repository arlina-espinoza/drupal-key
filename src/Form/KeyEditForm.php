<?php

/**
 * @file
 * Contains \Drupal\key\Form\KeyEditForm.
 */

namespace Drupal\key\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class KeyEditForm.
 *
 * @package Drupal\key\Form
 */
class KeyEditForm extends KeyFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Only when the form is first built.
    if (!$form_state->isRebuilding()) {
      /** @var $key \Drupal\key\Entity\Key */
      $key = $this->entity;
      $this->originalKey = clone $key;

      $key_type = $key->getKeyType();
      $key_provider = $key->getKeyProvider();
      $key_input = $key->getKeyInput();

      $obscure_options = [];

      // Add settings from plugins.
      $obscure_options['key_type_id'] = $key_type->getPluginId();
      $obscure_options['key_type_group'] = $key_type->getPluginDefinition()['group'];
      $obscure_options['key_provider_id'] = $key_provider->getPluginId();

      $key_value = [];

      // Get the existing key value.
      $key_value['original'] = $key->getKeyValue();

      // Process the original key value.
      $key_value['processed_original'] = $key_input->processExistingKeyValue($key_value['original']);

      // Obscure the processed key value.
      $key_value['obscured'] = $key_provider->obscureKeyValue($key_value['processed_original'], $obscure_options);

      // Set the current value as the obscured key value.
      $key_value['current'] = $key_value['obscured'];

      // Store the key value information in form state for use by plugins.
      $form_state->set('key_value', $key_value);
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Edit key %label', array('%label' => $this->entity->label()));
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Key %name has been updated.', array('%name' => $this->entity->label())));
  }

}
