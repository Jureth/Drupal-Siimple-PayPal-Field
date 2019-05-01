<?php

namespace Drupal\simple_paypal_field\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent;
use Drupal\simple_paypal_field\Event\PayPalSmartButtonsEvents;
use Drupal\simple_paypal_field\PayPalFieldInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber for PayPal fields.
 */
class SimplePayPalFieldEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

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
   * Class constructor.
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
        [$entity, $field] = $this->collectInstances($fieldInfo);
        $field->setPaymentInfo($event->getDetails());
        $entity->save();
      }
    }
    catch (\Throwable $e) {
      $this->log->error($e);
    }
  }

  /**
   * Loads an entity and field instances according to data if possible.
   *
   * @param array $fieldInfo
   *   Entity/Field information.
   *
   * @return array
   *   Entity and the field as list.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function collectInstances(array $fieldInfo): array {
    $entity = $this->entityTypeManager
      ->getStorage($fieldInfo['entity_type'])
      ->load($fieldInfo['entity_id']);
    // @todo what about base fields?
    if ($entity instanceof FieldableEntityInterface) {
      if ($entity->hasField($fieldInfo['field'])) {
        $field = $entity->get($fieldInfo['field'])->get($fieldInfo['field_id']);
        if ($field instanceof PayPalFieldInterface) {
          return [$entity, $field];
        }
        else {
          throw new \InvalidArgumentException(
            $this->t(
              'The field @name is not PayPal field',
              ['@name' => $fieldInfo['field']]
            )
          );
        }
      }
      else {
        throw new \InvalidArgumentException(
          $this->t(
            'Entity has no field @name',
            ['@name' => $fieldInfo['field']]
          )
        );
      }
    }
    else {
      throw new \InvalidArgumentException(
        $this->t(
          'Entity @type @id not found',
          [
            '@type' => $fieldInfo['entity_type'],
            '@id' => $fieldInfo['entity_id'],
          ]
        )
      );
    }
  }

}
