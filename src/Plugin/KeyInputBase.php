<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyTypeBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a base class for Key Type plugins.
 */
abstract class KeyInputBase extends KeyPluginBase implements KeyInputInterface {

  /**
   * {@inheritdoc}
   */
  public function getPluginType() {
    return 'key_input';
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValue('key_input_settings'));
  }

}
