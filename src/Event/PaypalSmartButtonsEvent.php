<?php

namespace Drupal\simple_paypal_field\Event;

use Symfony\Component\EventDispatcher\Event;

class PaypalSmartButtonsEvent extends Event {

  /**
   * @var array
   */
  protected $details;

  /**
   * PaypalSmartButtonsEvent constructor.
   *
   * @param $details
   */
  public function __construct($details) {
    $this->details = $details;
  }

  /**
   * @return array
   */
  public function getDetails() {
    return $this->details;
  }

}
