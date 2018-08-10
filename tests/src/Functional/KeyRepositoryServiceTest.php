<?php

namespace Drupal\Tests\key\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Key\KeyInterface;

/**
 * Tests the key.repository service.
 *
 * @group key
 */
class KeyRepositoryServiceTest extends BrowserTestBase {

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
   * Test getKeyValue functions.
   */
  public function testConfigKeyProviderService() {

    $this->createTestKey('testing_key_0');

    // Test getKey.
    $gottenKey = \Drupal::service('key.repository')->getKey('testing_key_0');

    $this->assertInstanceOf(KeyInterface::class, $gottenKey);

    $this->createTestKey('testing_key_1');

    // Test getKeysByProvider.
    $keys = \Drupal::service('key.repository')->getKeysByProvider('config');
    $this->assertEqual(count($keys), '2', 'The getKeysByProvider function is not returning 2 config keys');
    foreach ($keys as $key) {
      $this->assertInstanceOf(KeyInterface::class, $key);
    }
  }

}
