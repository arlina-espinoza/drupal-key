<?php

namespace Drupal\key\Plugin\KeyType;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use Drupal\key\Plugin\KeyPluginFormInterface;
use Drupal\key\Plugin\KeyTypeBase;
use Drupal\key\Plugin\KeyTypeMultivalueInterface;

/**
 * Defines a generic key type for authentication with multiple values.
 *
 * @KeyType(
 *   id = "authentication_multivalue",
 *   label = @Translation("Authentication (Multivalue)"),
 *   description = @Translation("A generic key type to use for an authentication key that contains multiple values."),
 *   group = "authentication",
 *   key_value = {
 *     "plugin" = "textarea_field"
 *   },
 *   multivalue = {
 *     "enabled" = true,
 *     "fields" = {}
 *   }
 * )
 */
class AuthenticationMultivalueKeyType extends KeyTypeBase implements KeyTypeMultivalueInterface {

  /**
   * {@inheritdoc}
   */
  public static function generateKeyValue(array $configuration) {
    // Return an empty JSON element.
    return '[]';
  }

  /**
   * {@inheritdoc}
   */
  public function validateKeyValue(array $form, FormStateInterface $form_state, $key_value) {
    // Validation of the key value is optional.
  }

  /**
   * {@inheritdoc}
   */
  public function serialize(array $array) {
    return Json::encode($array);
  }

  /**
   * {@inheritdoc}
   */
  public function unserialize($value) {
    return Json::decode($value);
  }

}
