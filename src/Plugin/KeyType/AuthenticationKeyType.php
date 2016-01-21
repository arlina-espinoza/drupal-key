<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyType\AuthenticationKeyType.
 */

namespace Drupal\key\Plugin\KeyType;

use Drupal\key\Plugin\KeyTypeBase;

/**
 * Defines a generic key type for authentication.
 *
 * @KeyType(
 *   id = "authentication",
 *   label = @Translation("Authentication"),
 *   description = @Translation("A generic key type to use for a password or API key that does not belong to any other defined key type."),
 *   group = "authentication",
 *   key_value = {
 *     "plugin" = "text_field"
 *   }
 * )
 */
class AuthenticationKeyType extends KeyTypeBase {
}
