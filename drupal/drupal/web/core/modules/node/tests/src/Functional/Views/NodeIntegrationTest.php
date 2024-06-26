<?php

declare(strict_types=1);

namespace Drupal\Tests\node\Functional\Views;

/**
 * Tests the node integration into views.
 *
 * @group node
 */
class NodeIntegrationTest extends NodeTestBase {

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_node_view'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests basic node view with a node type argument.
   */
  public function testNodeViewTypeArgument(): void {
    // Create two content types with three nodes each.
    $types = [];
    $all_nids = [];
    for ($i = 0; $i < 2; $i++) {
      $type = $this->drupalCreateContentType(['name' => '<em>' . $this->randomMachineName() . '</em>']);
      $types[] = $type;

      for ($j = 0; $j < 5; $j++) {
        // Ensure the right order of the nodes.
        $node = $this->drupalCreateNode(['type' => $type->id(), 'created' => \Drupal::time()->getRequestTime() - ($i * 5 + $j)]);
        $nodes[$type->id()][$node->id()] = $node;
        $all_nids[] = $node->id();
      }
    }

    $this->drupalGet('test-node-view');
    $this->assertSession()->statusCodeEquals(404);

    $this->drupalGet('test-node-view/all');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertNids($all_nids);

    foreach ($types as $type) {
      $this->drupalGet("test-node-view/{$type->id()}");
      $this->assertSession()->assertEscaped($type->label());
      $this->assertNids(array_keys($nodes[$type->id()]));
    }
  }

  /**
   * Ensures that a list of nodes appear on the page.
   *
   * @param array $expected_nids
   *   An array of node IDs.
   *
   * @internal
   */
  protected function assertNids(array $expected_nids = []): void {
    $result = $this->xpath('//span[@class="field-content"]');
    $nids = [];
    foreach ($result as $element) {
      $nids[] = (int) $element->getText();
    }
    $this->assertEquals($expected_nids, $nids);
  }

}
