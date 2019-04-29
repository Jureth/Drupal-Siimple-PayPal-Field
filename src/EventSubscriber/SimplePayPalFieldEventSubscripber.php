<?php

namespace Drupal\simple_paypal_field\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent;
use Drupal\simple_paypal_field\Event\PayPalSmartButtonsEvents;
use Drupal\simple_paypal_field\PayPalFieldInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for PayPal fields.
 */
class SimplePayPalFieldEventSubscripber implements EventSubscriberInterface {

  /**
   * Drupal Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $log;

  /**
   * SimplePayPalFieldEventSubscripber constructor.
   */
  public function __construct(EntityTypeManagerInterface $manager, LoggerChannelInterface $log) {
    $this->entityTypeManager = $manager;
    $this->log = $log;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PayPalSmartButtonsEvents::APPROVE_ORDER] = ['updateField'];
    return $events;
  }

  /**
   * Update PayPal field value with data from PayPal.
   *
   * @param \Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent $event
   *   Event object.
   */
  public function updateField(PaypalSmartButtonsEvent $event) {
    $fieldInfo = $event->getField();
    try {
      if ($fieldInfo['entity_type'] && $fieldInfo['entity_id'] && $fieldInfo['bundle']) {
        $entity = $this->entityTypeManager
          ->getStorage($fieldInfo['entity_type'])
          ->load($fieldInfo['entity_id']);
        if ($entity instanceof FieldableEntityInterface) {
          if ($entity->hasField($fieldInfo['field'])) {
            $field = $entity->get($fieldInfo['field'])->get(
              $fieldInfo['field_id']
            );
            if ($field instanceof PayPalFieldInterface) {
              $field->setPaymentInfo($event->getDetails());
              $entity->save();
            }
            else {
              $this->log->error(
                'The field is not PayPal field'
              );
            }
          }
          else {
            $this->log->error(
              'Entity has no field ' . $fieldInfo['field']
            );
          }
        }
        else {
          $this->log->error(
            'Entity ' . $fieldInfo['entity_type'] . ' ' . $fieldInfo['entity_id'] . ' not found'
          );
        }
      }
    }
    catch (\Throwable $e) {
      $this->log->error('Exception ' . $e->getMessage());
    }
  }

}
