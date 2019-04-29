<?php

namespace Drupal\simple_paypal_field;

/**
 * Interface for a PayPal field.
 */
interface PayPalFieldInterface {

  /**
   * Updates the field with payment information.
   *
   * @param array $info
   *   Payment information.
   */
  public function setPaymentInfo(array $info);

}
