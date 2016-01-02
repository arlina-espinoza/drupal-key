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
   * Returns the configured plugins for the key.
   *
   * @return \Drupal\key\Plugin\KeyPluginInterface[]
   *   An array of plugins, indexed by plugin type.
   */
  public function getPlugins();

  /**
   * Returns the configured key type for the key.
   *
   * @return \Drupal\key\Plugin\KeyTypeInterface
   *   The key type associated with the key.
   */
  public function getKeyType();

  /**
   * Returns the configured key provider for the key.
   *
   * @return \Drupal\key\Plugin\KeyProviderInterface
   *   The key provider associated with the key.
   */
  public function getKeyProvider();

  /**
   * Returns the configured key input for the key.
   *
   * @return \Drupal\key\Plugin\KeyInputInterface
   *   The key input associated with the key.
   */
  public function getKeyInput();

  /**
   * Gets the value of the key.
   *
   * @return string
   *   The value of the key.
   */
  public function getKeyValue();

}
