<?php

namespace Drupal\key\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\key\KeyConfigOverrideInterface;

/**
 * Defines the KeyConfigOverride entity.
 *
 * @ConfigEntityType(
 *   id = "key_config_override",
 *   label = @Translation("Key Configuration Override"),
 *   module = "key",
 *   handlers = {
 *     "list_builder" = "Drupal\key\Controller\KeyConfigOverrideListBuilder",
 *     "form" = {
 *       "add" = "Drupal\key\Form\KeyConfigOverrideAddForm"
 *     }
 *   },
 *   config_prefix = "config_override",
 *   admin_permission = "administer key configuration overrides",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "add-form" = "/admin/config/development/configuration/key-overrides/add",
 *     "collection" = "/admin/config/development/configuration/key-overrides"
 *   }
 * )
 */
class KeyConfigOverride extends ConfigEntityBase implements KeyConfigOverrideInterface {

  /**
   * The key configuration override ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The key configuration override label.
   *
   * @var string
   */
  protected $label;

  /**
   * The configuration type.
   *
   * @var string
   */
  protected $config_type;

  /**
   * The configuration name.
   *
   * @var string
   */
  protected $config_name;

  /**
   * The configuration item.
   *
   * @var string
   */
  protected $config_item;

  /**
   * The ID of the key to use for the override.
   *
   * @var string
   */
  protected $key_id;

  /**
   * The key entity associated with the override.
   *
   * @var \Drupal\key\KeyInterface
   */
  protected $key;

  /**
   * {@inheritdoc}
   */
  public function getConfigType() {
    return $this->config_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigName() {
    return $this->config_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfigItem() {
    return $this->config_item;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyId() {
    return $this->key_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getKey() {
    if (!isset($this->key)) {
      // TODO: Get the key entity.
    }

    return $this->key;
  }

}
