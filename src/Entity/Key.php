<?php

/**
 * @file
 * Contains Drupal\key\Entity\Key.
 */

namespace Drupal\key\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\key\KeyInterface;

/**
 * Defines the Key entity.
 *
 * @ConfigEntityType(
 *   id = "key",
 *   label = @Translation("Key"),
 *   handlers = {
 *     "list_builder" = "Drupal\key\Controller\KeyListBuilder",
 *     "form" = {
 *       "add" = "Drupal\key\Form\KeyForm",
 *       "edit" = "Drupal\key\Form\KeyForm",
 *       "delete" = "Drupal\key\Form\KeyDeleteForm"
 *     }
 *   },
 *   config_prefix = "key",
 *   admin_permission = "administer keys",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/config/system/key/add",
 *     "edit-form" = "/admin/config/system/key/manage/{key}",
 *     "delete-form" = "/admin/config/system/key/manage/{key}/delete",
 *     "collection" = "/admin/config/system/key"
 *   }
 * )
 */
class Key extends ConfigEntityBase implements KeyInterface {
  /**
   * The Key ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Key label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Key description.
   *
   * @var string
   */
  protected $description;

  /**
   * The Key label.
   *
   * @var \Drupal\key\KeyProviderInterface
   */
  protected $key_provider;

  /**
   * The settings for the key provider.
   *
   * @var array
   */
  protected $key_provider_settings = [];

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyProvider() {
    return $this->key_provider;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyProviderSettings() {
    return $this->key_provider_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue() {
    // Create instance of the plugin.
    $plugin = \Drupal::service('plugin.manager.key.key_provider');
    $key_provider = $plugin->createInstance($this->key_provider, $this->key_provider_settings);

    // Return its key contents.
    return $key_provider->getKeyValue($this);
  }

}
