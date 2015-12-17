<?php

/**
 * @file
 * Contains \Drupal\key\KeyInterface.
 */

namespace Drupal\key;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a Key entity.
 */
interface KeyInterface extends ConfigEntityInterface {

  /**
   * Gets the description of the key.
   *
   * @return string
   *   The description of this key.
   */
  public function getDescription();

  /**
   * Gets the key type ID.
   *
   * @return string
   *   The ID of the key type for this key.
   */
  public function getKeyType();

  /**
   * Gets the key type settings.
   *
   * @return array
   *   The key type settings.
   */
  public function getKeyTypeSettings();

  /**
   * Gets the key provider ID.
   *
   * @return string
   *   The ID of the key provider for this key.
   */
  public function getKeyProvider();

  /**
   * Gets the key provider settings.
   *
   * @return array
   *   The key provider settings.
   */
  public function getKeyProviderSettings();

  /**
   * Gets the value of the key.
   *
   * @return string
   *   The value of the key.
   */
  public function getKeyValue();

}
