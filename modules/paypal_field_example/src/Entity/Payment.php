<?php

namespace Drupal\paypal_field_example\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines a payment entity.
 *
 * @ContentEntityType(
 *   id = "payment",
 *   label = @Translation("Payment"),
 *   base_table = "payments",
 *   handlers = {
 *     "list_builder" = "Drupal\paypal_field_example\Entity\Builder\PaymentListBuilder",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "order_id" = "order_id",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/payments/{payment}",
 *     "collection" = "/admin/content/payments",
 *   }
 * )
 */
class Payment extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $definitions = parent::baseFieldDefinitions(
      $entity_type
    );

    $definitions['order_id'] = BaseFieldDefinition::create('string')
      ->setLabel('PayPal Order Id')
      ->setCardinality(1);
    $definitions['created'] = BaseFieldDefinition::create('timestamp')
      ->setReadOnly(TRUE)
      ->setRequired(TRUE)
      ->setCardinality(1)
      ->setLabel('Created');
    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage, array &$values) {
    $values['created'] = time();
    parent::preCreate($storage, $values);
  }

}
