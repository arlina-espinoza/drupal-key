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
      $this->originalKeyData = $this->getOriginalKeyData();
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

  /**
   * Return the original key data.
   *
   * @return array
   *   The original key data.
   */
  public function getOriginalKeyData() {
    /** @var $key \Drupal\key\Entity\Key */
    $key = $this->entity;

    // Build an array with the original key data.
    $original_key_data = array(
      'id' => $key->id(),
      'label' => $key->label(),
      'description' => $key->getDescription(),
    );
    foreach ($key->getPlugins() as $type => $plugin) {
      /** @var $plugin \Drupal\key\Plugin\KeyPluginBase */
      $original_key_data += array(
        $type => $plugin->getPluginId(),
        $type . '_settings' => $plugin->getConfiguration(),
        $type . '_definition' => $plugin->getPluginDefinition(),
      );
    }

    $original_key_data['key_value'] = $key->getKeyValue();

    return $original_key_data;
  }

}
