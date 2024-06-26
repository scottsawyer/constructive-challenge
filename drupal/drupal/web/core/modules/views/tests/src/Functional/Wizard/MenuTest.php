<?php

declare(strict_types=1);

namespace Drupal\Tests\views\Functional\Wizard;

use Drupal\Core\Url;

/**
 * Tests the ability of the views wizard to put views in a menu.
 *
 * @group views
 */
class MenuTest extends WizardTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests the menu functionality.
   */
  public function testMenus(): void {
    $this->drupalPlaceBlock('system_menu_block:main');

    // Create a view with a page display and a menu link in the Main Menu.
    $view = [];
    $view['label'] = $this->randomMachineName(16);
    $view['id'] = $this->randomMachineName(16);
    $view['description'] = $this->randomMachineName(16);
    $view['page[create]'] = 1;
    $view['page[title]'] = $this->randomMachineName(16);
    $view['page[path]'] = $this->randomMachineName(16);
    $view['page[link]'] = 1;
    $view['page[link_properties][parent]'] = 'main:';
    $view['page[link_properties][title]'] = $this->randomMachineName(16);
    $this->drupalGet('admin/structure/views/add');
    $this->submitForm($view, 'Save and edit');

    // Make sure there is a link to the view from the front page (where we
    // expect the main menu to display).
    $this->drupalGet('');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->linkExists($view['page[link_properties][title]']);
    $this->assertSession()->linkByHrefExists(Url::fromUri('base:' . $view['page[path]'])->toString());

    // Make sure the link is associated with the main menu.
    /** @var \Drupal\Core\Menu\MenuLinkManagerInterface $menu_link_manager */
    $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
    /** @var \Drupal\Core\Menu\MenuLinkInterface $link */
    $link = $menu_link_manager->createInstance('views_view:views.' . $view['id'] . '.page_1');
    $url = $link->getUrlObject();
    $this->assertEquals('view.' . $view['id'] . '.page_1', $url->getRouteName(), "Found a link to {$view['page[path]']} in the main menu");
    $metadata = $link->getMetaData();
    $this->assertEquals(['view_id' => $view['id'], 'display_id' => 'page_1'], $metadata);
  }

}
