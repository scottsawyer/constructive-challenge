<?php

declare(strict_types=1);

namespace Drupal\Tests\Core\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\Core\Breadcrumb\Breadcrumb
 * @group Breadcrumb
 */
class BreadcrumbTest extends UnitTestCase {

  /**
   * @covers ::setLinks
   */
  public function testSetLinks(): void {
    $breadcrumb = new Breadcrumb();
    $breadcrumb->setLinks([new Link('Home', Url::fromRoute('<front>'))]);
    $this->expectException(\LogicException::class);
    $this->expectExceptionMessage('Once breadcrumb links are set, only additional breadcrumb links can be added.');
    $breadcrumb->setLinks([new Link('None', Url::fromRoute('<none>'))]);
  }

}
