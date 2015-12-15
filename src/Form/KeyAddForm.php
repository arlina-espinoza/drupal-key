<?php

/**
 * @file
 * Contains \Drupal\key\Form\KeyAddForm.
 */

namespace Drupal\key\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class KeyAddForm.
 *
 * @package Drupal\key\Form
 */
class KeyAddForm extends KeyFormBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Key %name has been created.', array('%name' => $this->entity->label())));
  }

}
