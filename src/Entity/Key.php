<?php

/**
 * @file
 * Contains \Drupal\key\Entity\Key.
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
 *       "add" = "Drupal\key\Form\KeyAddForm",
 *       "edit" = "Drupal\key\Form\KeyEditForm",
 *       "delete" = "Drupal\key\Form\KeyDeleteForm"
 *     }
 *   },
 *   config_prefix = "key",
 *   admin_permission = "administer keys",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/config/system/keys/add",
 *     "edit-form" = "/admin/config/system/keys/manage/{key}",
 *     "delete-form" = "/admin/config/system/keys/manage/{key}/delete",
 *     "collection" = "/admin/config/system/keys"
 *   }
 * )
 */
class Key extends ConfigEntityBase implements KeyInterface {

  /**
   * The key description.
   *
   * @var string
   */
  protected $description = '';

  /**
   * The key type ID.
   *
   * @var string
   */
  protected $key_type = 'basic';

  /**
   * The key type settings.
   *
   * @var array
   */
  protected $key_type_settings = [];

  /**
   * The key provider ID.
   *
   * @var string
   */
  protected $key_provider;

  /**
   * The key provider settings.
   *
   * @var array
   */
  protected $key_provider_settings = [];

  /**
   * The key input ID.
   *
   * @var string
   */
  protected $key_input = 'none';

  /**
   * The key input settings.
   *
   * @var array
   */
  protected $key_input_settings = [];

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyType() {
    return $this->key_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyTypeSettings() {
    return $this->key_type_settings;
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
  public function getKeyInput() {
    return $this->key_input;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyInputSettings() {
    return $this->key_input_settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue() {
    // Create instance of the key provider plugin.
    $plugin = \Drupal::service('plugin.manager.key.key_provider');
    $key_provider = $plugin->createInstance($this->key_provider, $this->key_provider_settings);

    // Return the key value.
    return $key_provider->getKeyValue($this);
  }

}
