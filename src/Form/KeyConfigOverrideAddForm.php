<?php

namespace Drupal\key\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KeyConfigOverrideAddForm extends EntityForm {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The Key Configuration Override entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Constructs a KeyConfigOverrideAddForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->storage = $entity_type_manager->getStorage('key_config_override');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'key_config_override_add_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config_names = $this->getSimpleConfigurationNames();
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key configuration override name'),
      '#size' => 30,
      '#maxlength' => 64,
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#required' => TRUE,
      '#size' => 30,
      '#maxlength' => 64,
      '#machine_name' => [
        'exists' => [$this->storage, 'load'],
      ],
    ];

    // Only simple configuration objects are currently supported.
    $form['config_name'] = [
      '#type' => 'value',
      '#value' => 'system.simple',
    ];

    $default_config_name = $form_state->getValue('config_name', '');
    $form['config_name'] = [
      '#title' => $this->t('Configuration name'),
      '#type' => 'select',
      '#options' => $config_names,
      '#default_value' => $default_config_name,
      '#empty_value' => '',
      '#empty_option' => t('- Select -'),
      '#ajax' => [
        'callback' => '::updateConfigurationItems',
        'wrapper' => 'edit-config-item-wrapper',
      ],
    ];

//    $config_object = $this->configFactory->get($default_config_name);
//    $config_array = $config_object->get();
//    $config_items = $this->getConfigurationItems($config_array);
//    $form['config_item'] = [
//      '#title' => $this->t('Configuration item'),
//      '#type' => 'select',
//      '#options' => $config_items,
//      '#empty_value' => '',
//      '#empty_option' => t('- Select -'),
//      '#prefix' => '<div id="edit-config-item-wrapper">',
//      '#suffix' => '</div>',
//    ];
    $form['key_id'] = [
      '#title' => $this->t('Key'),
      '#type' => 'key_select',
      '#default_value' => '',
      '#empty_value' => '',
      '#empty_option' => t('- Select -'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));

    return parent::save($form, $form_state);
  }

  /**
   * Handles updating the configuration items.
   */
  public function updateConfigurationItems($form, FormStateInterface $form_state) {
    $form['config_item']['#options'] = [
      '' => t('- Select -'),
    ];

    $config_name = $form_state->getValue('config_name');
    $config_object = $this->configFactory->get($config_name);
    $config_array = $config_object->get();

    $config_items = $this->getConfigurationItems($config_array);
    $form['config_item']['#options'] = array_combine($config_items, $config_items);

    return $form['config_item'];
  }

  /**
   * Get a list of simple configuration names.
   *
   * @return array
   *   The simple configuration names.
   */
  protected function getSimpleConfigurationNames() {
    // Gather the configuration entity prefixes.
    $config_entity_prefixes = [];
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type => $definition) {
      if ($definition->entityClassImplements(ConfigEntityInterface::class)) {
        $config_entity_prefixes[] = $definition->getConfigPrefix() . '.';
      }
    }

    // Find all configuration, then filter out anything matching a
    // configuration entity prefix.
    $names = $this->configFactory->listAll();
    $names = array_combine($names, $names);
    foreach ($names as $config_name) {
      foreach ($config_entity_prefixes as $config_entity_prefix) {
        if (strpos($config_name, $config_entity_prefix) === 0) {
          unset($names[$config_name]);
        }
      }
    }

    return $names;
  }

  /**
   * Recursively create a flat array of configuration items.
   *
   * @param $config_array
   *   An array of configuration items.
   * @param string $prefix
   *   A prefix to add to nested items.
   * @param int $level
   *   The current level of nesting.
   *
   * @return array
   *   The flat array of configuration items.
   */
  protected function getConfigurationItems($config_array, $prefix = '', $level = 0) {
    $config_items = [];

    // Ignore certain items.
    $ignore = [
      'uuid',
      '_core',
    ];

    foreach ($config_array as $key => $value) {
      if (in_array($key, $ignore) && $level == 0) {
        continue;
      }

      if (is_array($value) && $level < 5) {
        $config_items = array_merge($config_items, $this->getConfigurationItems($value, $key . '.', $level + 1));
      }
      else {
        $config_items[] = $prefix . $key;
      }
    }

    return $config_items;
  }

}
