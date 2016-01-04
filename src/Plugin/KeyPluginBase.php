<?php
/**
 * @file
 * Provides \Drupal\key\Plugin\KeyPluginBase.
 */

namespace Drupal\key\Plugin;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base class for all Key plugins.
 */
abstract class KeyPluginBase extends PluginBase implements KeyPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configuration = NestedArray::mergeDeep($this->defaultConfiguration(), $this->configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginType() {
    return $this->pluginDefinition['plugin_type'];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValue($this->pluginDefinition['plugin_type'] . '_settings'));
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return array();
  }

}
