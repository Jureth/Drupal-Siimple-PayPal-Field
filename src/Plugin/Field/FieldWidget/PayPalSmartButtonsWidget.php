<?php

namespace Drupal\simple_paypal_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'boolean_checkbox' widget.
 *
 * @FieldWidget(
 *   id = "paypal_smart_buttons",
 *   label = @Translation("Paid widget"),
 *   field_types = {
 *     "simple_paypal_field"
 *   },
 *   multiple_values = FALSE
 * )
 */
class PayPalSmartButtonsWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['value'] = array_merge($element, [
      '#type' => 'checkbox',
      '#default_value' => !empty($items[0]->value),
      '#title_display' => 'after',
      '#title' => $this->fieldDefinition->getLabel(),
    ]);
    return $element;
  }

}
