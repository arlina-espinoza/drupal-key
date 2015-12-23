<?php

/**
 * @file
 * Contains \Drupal\key\Annotation\KeyValueInput.
 */

namespace Drupal\key\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a key value input annotation object.
 *
 * @Annotation
 */
class KeyValueInput extends Plugin {

  /**
   * The plugin ID of the key value input.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the key value input.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * The description of the key value input.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

}
