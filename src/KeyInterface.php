<?php

/**
 * @file
 * Contains Drupal\key\KeyInterface.
 */

namespace Drupal\key;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a Key entity.
 */
interface KeyInterface extends ConfigEntityInterface {

  /**
   * The description for the key.
   *
   * @return string
   */
  public function getDescription();

  /**
   * The plugin id of the selected key.
   *
   * @return string
   */
  public function getKeyProvider();

  /**
   * The plugin configuration for the selected key.
   *
   * @return array
   */
  public function getKeyProviderSettings();

  /**
   * Gets the value of the key.
   *
   * @return string
   */
  public function getKeyValue();

}
