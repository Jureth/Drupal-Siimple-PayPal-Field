<?php

namespace Drupal\Tests\simple_paypal_field\Functional;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Class SimplePayPalFieldTest.
 *
 * @group simple_paypal_field
 *
 * @todo Cover all cases.
 */
class SimplePayPalFieldTest extends BrowserTestBase {

  protected static $modules = ['entity_test', 'simple_paypal_field'];

  protected $webUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->webUser = $this->drupalCreateUser(
      [
        'view test entity',
        'administer entity_test content',
        'access content',
        'simple_paypal_field.administer',
      ]
    );
    $this->drupalLogin($this->webUser);
  }

  /**
   * Tests buttons widget and formatter are shown.
   */
  public function testFieldShowsButtons() {
    $field_type = 'simple_paypal_field';
    $widget_type = 'paypal_smart_buttons';
    $formatter = 'paypal_smart_buttons';

    // Create a field.
    $field_name = 'field_' . mb_strtolower($this->randomMachineName());
    $field_storage = FieldStorageConfig::create(
      [
        'field_name' => $field_name,
        'type' => $field_type,
        'entity_type' => 'entity_test',
      ]
    );
    $field_storage->save();

    FieldConfig::create(
      [
        'entity_type' => 'entity_test',
        'field_storage' => $field_storage,
        'bundle' => 'entity_test',
        'label' => $this->randomMachineName() . '_label',
        'settings' => [
          'amount' => (string) random_int(10, 100),
          'on_label' => 'Hello',
        ],
        'default_value' => [
          [
            'value' => 1,
          ],
        ],
      ]
    )
      ->save();
    // Widgets.
    entity_get_form_display('entity_test', 'entity_test', 'default')
      ->setComponent($field_name, ['type' => $widget_type])
      ->save();

    entity_get_display('entity_test', 'entity_test', 'full')
      ->setComponent($field_name, ['type' => $formatter])
      ->save();

    // Display creation form.
    $this->drupalGet('entity_test/add');
    $this->assertSession()->elementExists('css', '.paypal-button');

    $this->drupalPostForm(NULL, [], t('Save'));
    preg_match('|entity_test/manage/(\d+)|', $this->getUrl(), $match);
    $id = $match[1];
    $this->assertSession()->pageTextContains(
      t('entity_test @id has been created.', ['@id' => $id])
    );

    // Display the entity.
    $entity = EntityTest::load($id);
    $display = entity_get_display(
      $entity->getEntityTypeId(),
      $entity->bundle(),
      'full'
    );
    $content = $display->build($entity);
    $rendered_entity = $this->container->get('renderer')->renderRoot($content);
    $this->assertContains('paypal-button', (string) $rendered_entity);
  }

}
