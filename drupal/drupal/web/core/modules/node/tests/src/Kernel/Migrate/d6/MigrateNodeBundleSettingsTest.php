<?php

declare(strict_types=1);

namespace Drupal\Tests\node\Kernel\Migrate\d6;

use Drupal\Core\Field\Entity\BaseFieldOverride;
use Drupal\Tests\migrate_drupal\Kernel\d6\MigrateDrupal6TestBase;
use Drupal\node\Entity\Node;

/**
 * Test migrating node settings into the base_field_bundle_override config entity.
 *
 * @group migrate_drupal_6
 */
class MigrateNodeBundleSettingsTest extends MigrateDrupal6TestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['menu_ui'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['node']);
    $this->executeMigration('d6_node_type');

    // Create a config entity that already exists.
    BaseFieldOverride::create([
      'field_name' => 'promote',
      'entity_type' => 'node',
      'bundle' => 'page',
      'label' => 'Promoted to front page',
    ])->save();

    $this->executeMigrations([
      'd6_node_setting_promote',
      'd6_node_setting_status',
      'd6_node_setting_sticky',
    ]);
  }

  /**
   * Tests Drupal 6 node type settings to Drupal 8 migration.
   */
  public function testNodeBundleSettings(): void {
    // Test settings on test_page bundle.
    $node = Node::create(['type' => 'test_page']);
    $this->assertSame(1, $node->status->value);
    $this->assertSame(1, $node->promote->value);
    $this->assertSame(1, $node->sticky->value);

    // Test settings for test_story bundle.
    $node = Node::create(['type' => 'test_story']);
    $this->assertSame(1, $node->status->value);
    $this->assertSame(1, $node->promote->value);
    $this->assertSame(0, $node->sticky->value);

    // Test settings for the test_event bundle.
    $node = Node::create(['type' => 'test_event']);
    $this->assertSame(0, $node->status->value);
    $this->assertSame(0, $node->promote->value);
    $this->assertSame(1, $node->sticky->value);
  }

}
