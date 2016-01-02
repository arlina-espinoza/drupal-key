<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyProviderBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a base class for Key Provider plugins.
 */
abstract class KeyProviderBase extends KeyPluginBase implements KeyProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getPluginType() {
    return 'key_provider';
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValue('key_provider_settings'));
  }

}
