<?php

declare(strict_types=1);

namespace Drupal\Tests\Core\Asset;

use Drupal\Core\Asset\CssCollectionGrouper;
use Drupal\Tests\UnitTestCase;

/**
 * Tests the CSS asset collection grouper.
 *
 * @group Asset
 */
class CssCollectionGrouperUnitTest extends UnitTestCase {

  /**
   * A CSS asset grouper.
   *
   * @var \Drupal\Core\Asset\CssCollectionGrouper
   */
  protected $grouper;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->grouper = new CssCollectionGrouper();
  }

  /**
   * Tests \Drupal\Core\Asset\CssCollectionGrouper.
   */
  public function testGrouper(): void {
    $css_assets = [
      'system.base.css' => [
        'group' => -100,
        'type' => 'file',
        'weight' => 0.012,
        'media' => 'all',
        'preprocess' => TRUE,
        'data' => 'core/modules/system/system.base.css',
        'basename' => 'system.base.css',
      ],
      'js.module.css' => [
        'group' => -100,
        'type' => 'file',
        'weight' => 0.013,
        'media' => 'all',
        'preprocess' => TRUE,
        'data' => 'core/modules/system/js.module.css',
        'basename' => 'js.module.css',
      ],
      'jquery.ui.core.css' => [
        'group' => -100,
        'type' => 'file',
        'weight' => 0.004,
        'media' => 'screen',
        'preprocess' => TRUE,
        'data' => 'core/misc/ui/themes/base/jquery.ui.core.css',
        'basename' => 'jquery.ui.core.css',
      ],
      'field.css' => [
        'group' => 0,
        'type' => 'file',
        'weight' => 0.011,
        'media' => 'all',
        'preprocess' => TRUE,
        'data' => 'core/modules/field/theme/field.css',
        'basename' => 'field.css',
      ],
      'external.css' => [
        'group' => 0,
        'type' => 'external',
        'weight' => 0.009,
        'media' => 'all',
        'preprocess' => TRUE,
        'data' => 'http://example.com/external.css',
        'basename' => 'external.css',
      ],
      'elements.css' => [
        'group' => 100,
        'media' => 'all',
        'type' => 'file',
        'weight' => 0.001,
        'preprocess' => TRUE,
        'data' => 'core/themes/example/css/base/elements.css',
        'basename' => 'elements.css',
      ],
      'print.css' => [
        'group' => 100,
        'media' => 'print',
        'type' => 'file',
        'weight' => 0.003,
        'preprocess' => TRUE,
        'data' => 'core/themes/example/css/print.css',
        'basename' => 'print.css',
      ],
    ];

    $groups = $this->grouper->group($css_assets);

    $this->assertCount(5, $groups, "5 groups created.");

    // Check group 1.
    $group = $groups[0];
    $this->assertSame(-100, $group['group']);
    $this->assertSame('file', $group['type']);
    $this->assertSame('all', $group['media']);
    $this->assertTrue($group['preprocess']);
    $this->assertCount(3, $group['items']);
    $this->assertContainsEquals($css_assets['system.base.css'], $group['items']);
    $this->assertContainsEquals($css_assets['js.module.css'], $group['items']);

    // Check group 2.
    $group = $groups[1];
    $this->assertSame(0, $group['group']);
    $this->assertSame('file', $group['type']);
    $this->assertSame('all', $group['media']);
    $this->assertTrue($group['preprocess']);
    $this->assertCount(1, $group['items']);
    $this->assertContainsEquals($css_assets['field.css'], $group['items']);

    // Check group 3.
    $group = $groups[2];
    $this->assertSame(0, $group['group']);
    $this->assertSame('external', $group['type']);
    $this->assertSame('all', $group['media']);
    $this->assertTrue($group['preprocess']);
    $this->assertCount(1, $group['items']);
    $this->assertContainsEquals($css_assets['external.css'], $group['items']);

    // Check group 4.
    $group = $groups[3];
    $this->assertSame(100, $group['group']);
    $this->assertSame('file', $group['type']);
    $this->assertSame('all', $group['media']);
    $this->assertTrue($group['preprocess']);
    $this->assertCount(1, $group['items']);
    $this->assertContainsEquals($css_assets['elements.css'], $group['items']);

    // Check group 5.
    $group = $groups[4];
    $this->assertSame(100, $group['group']);
    $this->assertSame('file', $group['type']);
    $this->assertSame('print', $group['media']);
    $this->assertTrue($group['preprocess']);
    $this->assertCount(1, $group['items']);
    $this->assertContainsEquals($css_assets['print.css'], $group['items']);
  }

}
