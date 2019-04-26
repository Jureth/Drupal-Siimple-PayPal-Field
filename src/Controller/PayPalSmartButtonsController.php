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

class PayPalSmartButtonsController extends ControllerBase {

  protected $eventDispatcher;

  public static function create(ContainerInterface $container) {
    return new static($container->get('event_dispatcher'));
  }

  public function __construct(EventDispatcherInterface $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;
  }

  public function approve(Request $request) {
    $content = $request->getContent();
    if (!empty($content)) {
      $data = json_decode($content, TRUE);
      $event = new PaypalSmartButtonsEvent($data);
      $this->eventDispatcher->dispatch(
        PayPalSmartButtonsEvents::APPROVE_ORDER,
        $event
      );
      return new JsonResponse($data);
    }
    else {
      return new JsonResponse(NULL, Response::HTTP_NOT_ACCEPTABLE);
    }

  }

}
