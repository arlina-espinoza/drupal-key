<?php

/**
 * @file
 * Contains \Drupal\key\Entity\Key.
 */

namespace Drupal\key\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\key\KeyInterface;
use Drupal\key\Plugin\KeyPluginCollection;
use Drupal\key\Plugin\KeyProviderSettableValueInterface;

/**
 * Defines the Key entity.
 *
 * @ConfigEntityType(
 *   id = "key",
 *   label = @Translation("Key"),
 *   module = "key",
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
class Key extends ConfigEntityBase implements KeyInterface, EntityWithPluginCollectionInterface {

  /**
   * The key ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The key label.
   *
   * @var string
   */
  protected $label;

  /**
   * The key description.
   *
   * @var string
   */
  protected $description = '';

  /**
   * The types of plugins used by a the key entity.
   *
   * @var array
   */
  protected $pluginTypes = ['key_type', 'key_provider', 'key_input'];

  /**
   * The key type plugin id.
   *
   * @var string
   */
  protected $key_type = 'basic';

  /**
   * The key provider plugin id.
   *
   * @var string
   */
  protected $key_provider = 'config';

  /**
   * The key input plugin id.
   *
   * @var string
   */
  protected $key_input = 'none';

  /**
   * The key type plugin settings.
   *
   * @var array
   */
  protected $key_type_settings = [];

  /**
   * The key provider plugin settings.
   *
   * @var array
   */
  protected $key_provider_settings = [];

  /**
   * The key input plugin settings.
   *
   * @var array
   */
  protected $key_input_settings = [];

  /**
   * The plugin collections, indexed by plugin type.
   *
   * @var \Drupal\key\Plugin\KeyPluginCollection[]
   */
  protected $pluginCollections;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Return the list of plugin types supported by key entities.
   *
   * @return array
   *   The list of plugin types.
   */
  public function getPluginTypes() {
    return $this->pluginTypes;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugins() {
    $plugins = [];
    foreach ($this->pluginTypes as $type) {
      $plugins[$type] = $this->getPlugin($type);
    }

    return $plugins;
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin($type) {
    return $this->getPluginCollection($type)->get($this->$type);
  }

  /**
   * {@inheritdoc}
   */
  public function setPlugin($type, $id) {
    $this->$type = $id;
    $this->getPluginCollection($type)->addInstanceID($id);
  }

  /**
   * Returns a list of plugins, for use in forms.
   *
   * @param string $type
   *   The plugin type to use.
   *
   * @return array
   *   The list of plugins, indexed by ID.
   */
  public function getPluginsAsOptions($type) {
    $manager = \Drupal::service("plugin.manager.key.$type");

    $options = [];
    foreach ($manager->getDefinitions() as $id => $definition) {
      $options[$id] = ($definition['label']);
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyType() {
    return $this->getPlugin('key_type');
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyProvider() {
    return $this->getPlugin('key_provider');
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyInput() {
    return $this->getPlugin('key_input');
  }

  /**
   * Create a plugin collection of the requested plugin type.
   *
   * @param $type
   *   The plugin type.
   *
   * @return \Drupal\key\Plugin\KeyPluginCollection
   *   The plugin collection.
   */
  public function getPluginCollection($type) {
    if (!isset($this->pluginCollections[$type . '_settings'])) {
      $this->pluginCollections[$type . '_settings'] = new KeyPluginCollection(
        \Drupal::service("plugin.manager.key.$type"),
        $this->get($type),
        $this->get($type . '_settings'));
    }

    return $this->pluginCollections[$type . '_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    $plugin_collections = [];
    foreach ($this->pluginTypes as $type) {
      $plugin_collections[$type . '_settings'] = $this->getPluginCollection($type);
    }

    return $plugin_collections;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeyValue() {
    return $this->getKeyProvider()->getKeyValue($this);
  }

  /**
   * {@inheritdoc}
   */
  public function setKeyValue($key_value) {
    if ($this->getKeyProvider() instanceof KeyProviderSettableValueInterface) {
      return $this->getKeyProvider()->setKeyValue($this, $key_value);
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteKeyValue() {
    if ($this->getKeyProvider() instanceof KeyProviderSettableValueInterface) {
      return $this->getKeyProvider()->deleteKeyValue($this);
    }
    else {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    // If an original key exists.
    if (isset($this->original)) {
      /** @var $original \Drupal\key\Entity\Key */
      $original = $this->original;

      // If the original key's provider allows setting a key value and
      // the plugin ID is different from the one that was just saved with
      // the entity.
      if ($original->getKeyProvider() instanceof KeyProviderSettableValueInterface
        && $original->getKeyProvider()->getPluginId() != $this->getKeyProvider()->getPluginId()
      ) {
        // Allow the original key's provider to delete the key value.
        $original->deleteKeyValue();
      }
    }

    parent::postSave($storage, $update);
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    foreach ($entities as $id => $key) {
      /** @var $key \Drupal\key\Entity\Key */
      // Give the key provider plugin the opportunity to delete the key value.
      if ($key->getKeyProvider() instanceof KeyProviderSettableValueInterface) {
        $key->deleteKeyValue();
      }
    }

    parent::postDelete($storage, $entities);
  }

}
