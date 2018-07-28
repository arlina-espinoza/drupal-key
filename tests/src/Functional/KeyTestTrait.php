<?php

namespace Drupal\Tests\key\Functional;

use Drupal\Core\Url;
use Drupal\key\Entity\Key;
use Drupal\key\Entity\KeyConfigOverride;

/**
 * Used for key access tests.
 *
 * @group key
 */
trait KeyTestTrait {

  /**
   * A key entity to use for testing.
   *
   * @var \Drupal\key\KeyInterface
   */
  protected $testKey;

  /**
   * A key configuration override entity to use for testing.
   *
   * @var \Drupal\key\KeyConfigOverrideInterface
   */
  protected $testKeyConfigOverride;

  /**
   * Tests each route for the currently signed-in user.
   */
  protected function routeAccessTest($routes, $response) {
    foreach ($routes as $route => $parameters) {
      $url = Url::fromRoute($route, $parameters);
      $this->drupalGet($url);
      $this->assertResponse($response);
    }
  }

  /**
   * Make a key for testing operations that require a key.
   */
  protected function createTestKey($key_id) {
    $this->testKey = Key::create([
      'id' => $key_id,
      'label' => 'Test key',
    ]);
    $this->testKey->save();
  }

  /**
   * Make a key configuration override for testing operations.
   */
  protected function createTestKeyConfigOverride($override_id, $key_id) {
    $this->testKeyConfigOverride = KeyConfigOverride::create([
      'id' => $override_id,
      'label' => 'Test key configuration override',
      'config_type' => 'system.simple',
      'config_prefix' => '',
      'config_name' => 'system.site',
      'config_item' => 'name',
      'key_id' => $key_id,
    ]);

    $this->testKeyConfigOverride->save();
  }

}
