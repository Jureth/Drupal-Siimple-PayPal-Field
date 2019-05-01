<?php

namespace Drupal\paypal_field_example\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent;
use Drupal\simple_paypal_field\Event\PayPalSmartButtonsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * PayPal Field Example event subscriber.
 */
class PayPalFieldExampleSubscriber implements EventSubscriberInterface {

  /**
   * Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $manager;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $manager
   *   Entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PayPalSmartButtonsEvents::APPROVE_ORDER => ['createPaymentEntity'],
    ];
  }

  /**
   * Creates an payment entity.
   *
   * @param \Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent $event
   *   The event.
   */
  public function createPaymentEntity(PaypalSmartButtonsEvent $event) {
    $details = $event->getDetails();
    $this->manager->getStorage('payment')
      ->create(['order_id' => $details['id']])
      ->save();
  }

}
