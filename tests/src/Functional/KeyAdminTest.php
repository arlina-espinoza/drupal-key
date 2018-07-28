<?php

namespace Drupal\Tests\key\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests administration of keys.
 *
 * @group key
 */
class KeyAdminTest extends BrowserTestBase {

  use KeyTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['key'];

  /**
   * A user with the 'administer keys' permission.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->createTestKey('key_foo');
    $this->createTestKeyConfigOverride('test_override', 'key_foo');

    $this->adminUser = $this->drupalCreateUser(['administer keys']);

  }

  /**
   * Tests key routes for an authorized user.
   */
  public function testAdminUserRoutes() {

    $this->drupalLogin($this->adminUser);

    $basicKeyRoutes = [
      'entity.key.collection' => [],
      'entity.key.add_form' => [],
      'entity.key.edit_form' => ['key' => 'key_foo'],
      'entity.key.delete_form' => ['key' => 'key_foo'],
    ];

    $overrideKeyRoutes = [
      'entity.key_config_override.collection' => [],
      'entity.key_config_override.add_form' => [],
      'entity.key_config_override.delete_form' => ['key_config_override' => 'test_override'],
    ];

    $this->routeAccessTest($basicKeyRoutes, 200);
    $this->routeAccessTest($overrideKeyRoutes, 403);
  }

}
