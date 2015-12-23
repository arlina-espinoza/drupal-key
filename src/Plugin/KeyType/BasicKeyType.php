<?php

/**
 * @file
 * Contains \Drupal\key\Plugin\KeyType\BasicKeyType.
 */

namespace Drupal\key\Plugin\KeyType;

use Drupal\key\Plugin\KeyTypeBase;

/**
 * Defines a basic key type.
 *
 * @KeyType(
 *   id = "basic",
 *   label = @Translation("Basic"),
 *   description = @Translation("A basic key.")
 * )
 */
class BasicKeyType extends KeyTypeBase {
}
