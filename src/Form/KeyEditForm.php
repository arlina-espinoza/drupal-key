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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    drupal_set_message($this->t('Key %name has been updated.', array('%name' => $this->entity->label())));
  }

}
