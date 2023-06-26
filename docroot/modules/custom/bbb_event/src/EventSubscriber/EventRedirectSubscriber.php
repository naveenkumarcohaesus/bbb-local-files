<?php

namespace Drupal\bbb_event\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber to redirect to external event link.
 */
class EventRedirectSubscriber implements EventSubscriberInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * Constructs a new rest CSRF access check.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   *   The current user.
   */
  public function __construct(AccountProxyInterface $account) {
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return ([
      KernelEvents::REQUEST => [
        ['redirectEventNode'],
      ],
    ]);
  }

  /**
   * Redirect requests for event node detail pages to external link.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Request event.
   */
  public function redirectEventNode(RequestEvent $event) {
    // Execute redirection only for anonymous user.
    if (!$this->account->isAnonymous()) {
      return;
    }
    $request = $event->getRequest();

    // Ignore "edit", "revisions", etc. pages from redirection.
    if ($request->attributes->get('_route') !== 'entity.node.canonical') {
      return;
    }

    // Only redirect a certain content type.
    $node = $request->attributes->get('node');
    // Check for external link field.
    if ($node->getType() === 'event' && $node->hasField('field_external_event_link')) {
      $link = $node->get('field_external_event_link')->getValue();
      if (!$node->get('field_external_event_link')->isEmpty()) {
        if (!empty($link[0]['uri'])) {
          // Redirect to external url.
          $response = new TrustedRedirectResponse($link[0]['uri']);
          $event->setResponse($response);
        }
      }
    }
  }

}
