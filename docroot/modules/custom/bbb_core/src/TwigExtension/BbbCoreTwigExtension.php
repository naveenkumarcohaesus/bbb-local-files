<?php

namespace Drupal\bbb_core\TwigExtension;

use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Template\TwigExtension;
use Drupal\Core\Url;
use Twig\TwigFunction;

/**
 * Class BbbCoreTwigExtension for twig extension.
 */
class BbbCoreTwigExtension extends TwigExtension {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a new BbbCoreTwigExtension object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('url_route_name', [$this, 'getRouteNameFromUrl']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'bbb_core';
  }

  /**
   * Gets a routeName from Url object.
   *
   * @param \Drupal\Core\Url $url
   *   The URL object to retrieve the path from.
   *
   * @return string
   *   Route Name of the Url object.
   */
  public function getRouteNameFromUrl(Url $url) {
    if ($url->isRouted()) {
      return $url->getRouteName();
    }
    return NULL;
  }

}
