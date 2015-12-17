<?php

/**
 * @file
 * Contains \Drupal\key\Controller\KeyListBuilder.
 */

namespace Drupal\key\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of keys.
 *
 *  @see \Drupal\key\Entity\Key
 */
class KeyListBuilder extends ConfigEntityListBuilder {

  /**
   * The key type plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $keyTypeManager;

  /**
   * The key provider plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $keyProviderManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, PluginManagerInterface $key_type_manager, PluginManagerInterface $key_provider_manager) {
    parent::__construct($entity_type, $storage);
    $this->keyTypeManager = $key_type_manager;
    $this->keyProviderManager = $key_provider_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('plugin.manager.key.key_type'),
      $container->get('plugin.manager.key.key_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Key');
    $header['type'] = array(
      'data' => $this->t('Type'),
      'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
    );
    $header['provider'] = array(
      'data' => $this->t('Provider'),
      'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
    );

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    if ($entity->getKeyType() && $key_type = $this->keyTypeManager->getDefinition($entity->getKeyType())) {
      $row['type'] = $key_type['title'];
    }
    else {
      $row['type'] = '';
    }

    $row['provider'] = $this->keyProviderManager->getDefinition($entity->getKeyProvider())['title'];


    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t('No keys are available. <a href=":link">Add a key</a>.', array(':link' => Url::fromRoute('entity.key.add_form')->toString()));
    return $build;
  }

}
