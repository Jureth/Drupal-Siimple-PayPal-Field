<?php

namespace Drupal\simple_paypal_field\Plugin\Field\FieldType;

use Drupal;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * Defines the 'paid_boolean' entity field type.
 *
 * @FieldType(
 *   id = "simple_paypal_field",
 *   label = @Translation("Simple PayPal field"),
 *   description = @Translation("A base field for PayPal buttons with storing payment status"),
 *   default_widget = "paypal_smart_buttons",
 *   default_formatter = "paypal_smart_buttons",
 *   cardinality = 1
 * )
 */
class SimplePayPalField extends FieldItemBase implements OptionsProviderInterface {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    $settings = [
      'amount' => '0.01',
    ];
    return $settings + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Boolean value'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'int',
          'size' => 'tiny',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $element['amount'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Amount (USD)'),
      '#default_value' => $this->getSetting('amount'),
      '#required' => TRUE,
      '#element_validate' => [
        // @todo I'm not sure it's the right way.
        [static::class, 'validateFloat'],
      ],
    ];

    return $element;
  }

  /**
   * Float validator.
   *
   * @param array $element
   *   Element to validate.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   FormState instance.
   * @param array $form
   *   Form array.
   */
  public static function validateFloat(array &$element, FormStateInterface $formState, array &$form) {
    $value = floatval($formState->getValue('settings')['amount']);
    if (!$value) {
      $formState->setError($element);
    }
    $formated_value = number_format(floatval($value), 2, '.', '');
    $formState->setValueForElement($element, $formated_value);
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    return [0, 1];
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      0 => $this->t('Not paid'),
      1 => $this->t('Paid'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return [0, 1];
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    return $this->getPossibleOptions($account);
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = mt_rand(0, 1);
    return $values;
  }

}
