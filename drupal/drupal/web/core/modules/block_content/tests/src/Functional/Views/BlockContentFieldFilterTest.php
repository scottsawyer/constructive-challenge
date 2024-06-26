<?php

declare(strict_types=1);

namespace Drupal\Tests\block_content\Functional\Views;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\language\Entity\ConfigurableLanguage;

/**
 * Tests block_content field filters with translations.
 *
 * @group block_content
 */
class BlockContentFieldFilterTest extends BlockContentTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['language'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = ['test_field_filters'];

  /**
   * List of block_content infos by language.
   *
   * @var array
   */
  public $blockContentInfos = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp($import_test_views = TRUE, $modules = ['block_content_test_views']): void {
    parent::setUp($import_test_views, $modules);

    // Add two new languages.
    ConfigurableLanguage::createFromLangcode('fr')->save();
    ConfigurableLanguage::createFromLangcode('es')->save();

    // Make the body field translatable. The info is already translatable by
    // definition.
    $field_storage = FieldStorageConfig::loadByName('block_content', 'body');
    $field_storage->setTranslatable(TRUE);
    $field_storage->save();

    // Set up block_content infos.
    $this->blockContentInfos = [
      'en' => 'Food in Paris',
      'es' => 'Comida en Paris',
      'fr' => 'Nourriture en Paris',
    ];

    // Create block_content with translations.
    $block_content = $this->createBlockContent(['info' => $this->blockContentInfos['en'], 'langcode' => 'en', 'type' => 'basic', 'body' => [['value' => $this->blockContentInfos['en']]]]);
    foreach (['es', 'fr'] as $langcode) {
      $translation = $block_content->addTranslation($langcode, ['info' => $this->blockContentInfos[$langcode]]);
      $translation->body->value = $this->blockContentInfos[$langcode];
    }
    $block_content->save();
  }

  /**
   * Tests body and info filters.
   */
  public function testFilters(): void {
    // Test the info filter page, which filters for info contains 'Comida'.
    // Should show just the Spanish translation, once.
    $this->assertPageCounts('test-info-filter', ['es' => 1, 'fr' => 0, 'en' => 0], 'Comida info filter');

    // Test the body filter page, which filters for body contains 'Comida'.
    // Should show just the Spanish translation, once.
    $this->assertPageCounts('test-body-filter', ['es' => 1, 'fr' => 0, 'en' => 0], 'Comida body filter');

    // Test the info Paris filter page, which filters for info contains
    // 'Paris'. Should show each translation once.
    $this->assertPageCounts('test-info-paris', ['es' => 1, 'fr' => 1, 'en' => 1], 'Paris info filter');

    // Test the body Paris filter page, which filters for body contains
    // 'Paris'. Should show each translation once.
    $this->assertPageCounts('test-body-paris', ['es' => 1, 'fr' => 1, 'en' => 1], 'Paris body filter');
  }

  /**
   * Asserts that the given block_content translation counts are correct.
   *
   * @param string $path
   *   Path of the page to test.
   * @param array $counts
   *   Array whose keys are languages, and values are the number of times
   *   that translation should be shown on the given page.
   * @param string $message
   *   Message suffix to display.
   *
   * @internal
   */
  protected function assertPageCounts(string $path, array $counts, string $message): void {
    // Get the text of the page.
    $this->drupalGet($path);
    $text = $this->getTextContent();

    foreach ($counts as $langcode => $count) {
      $this->assertEquals($count, substr_count($text, $this->blockContentInfos[$langcode]), 'Translation ' . $langcode . ' has count ' . $count . ' with ' . $message);
    }
  }

}
