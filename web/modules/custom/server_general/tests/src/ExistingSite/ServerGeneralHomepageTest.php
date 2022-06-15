<?php

namespace Drupal\Tests\server_general\ExistingSite;

use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Tests for the Homepage.
 */
class ServerGeneralHomepageTest extends ExistingSiteBase {

  /**
   * The homepage is cache-able.
   */
  public function testHomepageCache() {
    $this->drupalGet('/');
    $this->assertSession()->responseHeaderEquals('Cache-Control', 'max-age=1800, public');
    $this->drupalGet($url);
    $this->assertSession()->responseHeaderExists('X-Drupal-Cache', 'HIT');
  }

}
