<?php

declare(strict_types=1);

namespace Drupal\Tests\system\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Tests\system\Traits\TestTrait;

/**
 * Test whether traits are autoloaded during PHPUnit discovery time.
 *
 * @group system
 * @group Test
 */
class TraitAccessTest extends UnitTestCase {

  use TestTrait;

  /**
   * @coversNothing
   */
  public function testSimpleStuff(): void {
    $stuff = $this->getStuff();
    $this->assertSame($stuff, 'stuff', "Same old stuff");
  }

}
