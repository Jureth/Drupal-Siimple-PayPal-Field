<?php

namespace Drupal\simple_paypal_field\EventSubscriber;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent;
use Drupal\simple_paypal_field\Event\PayPalSmartButtonsEvents;
use Drupal\simple_paypal_field\PayPalFieldInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SimplePayPalFieldEventSubscripber implements EventSubscriberInterface {

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * The array keys are event names and the value can be:
   *
   *  * The method name to call (priority defaults to 0)
   *  * An array composed of the method name to call and the priority
   *  * An array of arrays composed of the method names to call and respective
   *    priorities, or 0 if unset
   *
   * For instance:
   *
   *  * ['eventName' => 'methodName']
   *  * ['eventName' => ['methodName', $priority]]
   *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
   *
   * @return array The event names to listen to
   */
  public static function getSubscribedEvents() {
    $events[PayPalSmartButtonsEvents::APPROVE_ORDER] = ['updateField'];
    return $events;
  }

  public function updateField(PaypalSmartButtonsEvent $event) {
    $fieldInfo = $event->getField();
    try {
      if ($fieldInfo['entity_type'] && $fieldInfo['entity_id'] && $fieldInfo['bundle']) {
        $entity = \Drupal::entityTypeManager()
          ->getStorage($fieldInfo['entity_type'])
          ->load($fieldInfo['entity_id']);
        if ($entity instanceOf FieldableEntityInterface) {
          if ($entity->hasField($fieldInfo['field'])) {
            $field = $entity->get($fieldInfo['field'])->get(
              $fieldInfo['field_id']
            );
            if ($field instanceof PayPalFieldInterface) {
              $field->setPaymentInfo($event->getDetails());
              $entity->save();
            }
            else {
              \Drupal::logger('simple_paypal_field')->error(
                'The field is not PayPal field'
              );
            }
          }
          else {
            \Drupal::logger('simple_paypal_field')->error(
              'Entity has no field ' . $fieldInfo['field']
            );
          }
        }
        else {
          \Drupal::logger('simple_paypal_field')->error(
            'Entity ' . $fieldInfo['entity_type'] . ' ' . $fieldInfo['entity_id'] . ' not found'
          );
        }
      }
    }catch (\Throwable $e) {
      \Drupal::logger('simple_paypal_field')->error('Exception ' . $e->getMessage());
    }
  }

}
