<?php

/**
 * @file
 * Contains \Drupal\key\Annotation\KeyType.
 */

namespace Drupal\key\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a key type annotation object.
 *
 * @Annotation
 */
class KeyType extends Plugin {

  /**
   * The plugin ID of the key type.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the key type.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * The description of the key type.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * The group to which this key type belongs.
   *
   * This is the general category of this type of key. Examples include
   * "authentication" and "encryption".
   *
   * @var string
   */
  public $group;

}
