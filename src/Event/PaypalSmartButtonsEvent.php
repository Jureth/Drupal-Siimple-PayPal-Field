<?php

namespace Drupal\simple_paypal_field\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * PayPal smart buttons event.
 */
class PaypalSmartButtonsEvent extends Event {

  /**
   * Order details.
   *
   * @var array
   */
  protected $details;

  /**
   * Field details.
   *
   * @var array|null
   *
   * @todo Store and return the field instance?
   */
  protected $field;

  /**
   * PaypalSmartButtonsEvent constructor.
   *
   * @param array $details
   *   Payment details as it sent from paypal.
   * @param array|null $field
   *   Field details if it exists.
   */
  public function __construct(array $details, ?array $field = NULL) {
    $this->details = $details;
    $this->field = $field;
  }

  /**
   * Getter for 'details' field.
   *
   * @return array
   *   Array contains payment details
   */
  public function getDetails(): array {
    return $this->details;
  }

  /**
   * Getter for 'field' field.
   *
   * @return array|null
   *   Array contains field information.
   */
  public function getField(): ?array {
    return $this->field;
  }

}
