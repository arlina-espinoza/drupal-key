<?php

namespace Drupal\key\Controller;

use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;
use Drupal\key\KeyRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of key configuration overrides.
 *
 * @see \Drupal\key\Entity\KeyConfigOverride
 */
class KeyConfigOverrideListBuilder extends ConfigEntityListBuilder {

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface;
   */
  protected $configManager;

  /**
   * The key repository.
   *
   * @var \Drupal\key\KeyRepositoryInterface;
   */
  protected $keyRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, ConfigManagerInterface $config_manager, KeyRepositoryInterface $key_repository) {
    $this->configManager = $config_manager;
    $this->keyRepository = $key_repository;
    parent::__construct($entity_type, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('config.manager'),
      $container->get('key.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = [
      'data' => $this->t('Override name'),
      'class' => [RESPONSIVE_PRIORITY_LOW],
    ];
    $header['config_id'] = $this->t('Configuration');
    $header['key_id'] = $this->t('Key');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\key\KeyConfigOverrideInterface */

    $key = $this->keyRepository->getKey($entity->getKeyId());

    $row['label'] = $entity->label();
    $row['config_id'] = $entity->getConfigName() . ':' . $entity->getConfigItem();
    $row['key_id'] = $key->label();

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();
    $build['table']['#empty'] = $this->t('No key configuration overrides are available. <a href=":link">Add an override</a>.', [':link' => Url::fromRoute('entity.key_config_override.add_form')->toString()]);
    return $build;
  }

}
