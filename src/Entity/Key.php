<?php

/**
 * @file
 * Contains \Drupal\key\Entity\Key.
 */

namespace Drupal\key\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\key\KeyInterface;
use Drupal\key\Plugin\KeyPluginCollection;

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
  protected $pluginCollection;

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
   * Returns the configured plugin of the requested type.
   *
   * @param string $type
   *   The plugin type to return.
   *
   * @return \Drupal\key\Plugin\KeyPluginInterface
   *   The plugin.
   */
  protected function getPlugin($type) {
    $collections = $this->getPluginCollections();
    return $collections[$type . '_settings']->get($this->$type);
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
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    if (!isset($this->pluginCollection)) {
      $this->pluginCollection = [];
      foreach ($this->pluginTypes as $type) {
        $this->pluginCollection[$type . '_settings'] = new KeyPluginCollection(
          \Drupal::service("plugin.manager.key.$type"),
          $this->get($type),
          $this->get($type . '_settings'));
      }
    }

    return $this->pluginCollection;
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
