<?php

/**
 * @file
 * Contains \Drupal\key\Annotation\KeyProvider.
 */

namespace Drupal\key\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a key provider annotation object.
 *
 * @Annotation
 */
class KeyProvider extends Plugin {

  /**
   * The plugin ID of the key provider.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the key provider.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $title;

  /**
   * The description of the key provider.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * The storage method of the key provider.
   *
   * This is an enumeration of {file, config, database, remote}.
   *
   * @var string
   */
  public $storage_method;

}
