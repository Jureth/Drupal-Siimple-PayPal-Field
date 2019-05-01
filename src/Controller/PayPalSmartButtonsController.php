<?php

namespace Drupal\simple_paypal_field\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\simple_paypal_field\Event\PaypalSmartButtonsEvent;
use Drupal\simple_paypal_field\Event\PayPalSmartButtonsEvents;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller class for requests from the smart buttons.
 */
class PayPalSmartButtonsController extends ControllerBase {

  /**
   * Drupal event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('event_dispatcher'));
  }

  /**
   * PayPalSmartButtonsController constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   Drupal event dispatcher.
   */
  public function __construct(EventDispatcherInterface $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;
  }

  /**
   * Callback requested after approving the order.
   *
   * Dispatches the 'approve' event to Drupal.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request object.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Response for buttons.
   */
  public function approve(Request $request) {
    $content = $request->getContent();
    if (!empty($content)) {
      $data = json_decode($content, TRUE);
      $element = [];
      try {
        [
          $element['entity_type'],
          $element['bundle'],
          $element['field'],
          $element['entity_id'],
          $element['field_id'],
        ] = explode('-', $data['element']);
      }
      finally {
        unset($data['element']);
      }
      $this->getLogger('simple_paypal_field')->debug(var_export($data['details'], TRUE));

      $event = new PaypalSmartButtonsEvent($data['details'], $element);
      $this->eventDispatcher->dispatch(
        PayPalSmartButtonsEvents::APPROVE_ORDER,
        $event
      );
      return new JsonResponse('ok');
    }
    else {
      return new JsonResponse(NULL, Response::HTTP_NOT_ACCEPTABLE);
    }
  }

}
