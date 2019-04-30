<?php

namespace Drupal\simple_paypal_field\Plugin\Field\FieldType;

/**
 * Defines the 'paid_boolean' entity field type.
 *
 * @FieldType(
 *   id = "auto_off_paypal_field",
 *   label = @Translation("One-time payment field"),
 *   description = @Translation("A field which disables itself after payment was made"),
 *   default_widget = "paypal_smart_buttons",
 *   default_formatter = "paypal_smart_buttons",
 *   cardinality = 1
 * )
 */
class OneTimePaymentField extends SimplePayPalField {

  /**
   * {@inheritdoc}
   */
  public function setPaymentInfo(array $info) {
    // Simply set to 'off'.
    // @todo check order status.
    $this->setValue(0);
  }

}
