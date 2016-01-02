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
 *   description = @Translation("A Basic key is one that is not logically associated with any other defined type.")
 * )
 */
class BasicKeyType extends KeyTypeBase {
}
