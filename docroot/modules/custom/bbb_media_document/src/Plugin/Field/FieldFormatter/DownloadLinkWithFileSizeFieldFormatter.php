<?php

namespace Drupal\bbb_media_document\Plugin\Field\FieldFormatter;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\media_entity_download\Plugin\Field\FieldFormatter\DownloadLinkFieldFormatter;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Plugin implementation 'media_entity_download_link_with_filesize' formatter.
 *
 * @FieldFormatter(
 *   id = "media_entity_download_link_with_filesize",
 *   label = @Translation("Download link with filesize"),
 *   field_types = {
 *     "file",
 *     "image"
 *   }
 * )
 */
class DownloadLinkWithFileSizeFieldFormatter extends DownloadLinkFieldFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $parent = $items->getParent()->getValue()->id();
    $settings = $this->getSettings();
    $entity = $items->getParent()->getValue();
    $class = 'coh-style-link-button btn-tertiary btn-default coh-style-label-large';
    $link = '<a href="@url" class="@class">@title</a>';
    $assistive_text = '';
    // Get download cta text field value.
    if ($entity->hasField('field_download_cta') && !empty($entity->field_download_cta->value)) {
      $cta = $entity->field_download_cta->value;
    }
    // Get asssitive text field value.
    if ($entity->hasField('field_download_assistive_text') && !empty($entity->field_download_assistive_text->value)) {
      $assistive_text = $entity->field_download_assistive_text->value;
      $link = '<a href="@url" download class="@class">@title<span class="visually-hidden">@assitive</span></a>';
    }

    foreach ($items as $delta => $item) {
      $route_parameters = ['media' => $parent];
      $url_options = [];
      if ($delta > 0) {
        $route_parameters['query']['delta'] = $delta;
      }

      // @todo replace with DI when this issue is fixed: https://www.drupal.org/node/2053415
      /** @var \Drupal\file\FileInterface $file */
      $file = \Drupal::entityTypeManager()->getStorage('file')->load($item->target_id);

      if (empty($file)) {
        continue;
      }

      $filename = $file->getFilename();
      $mime_type = $file->getMimeType();
      $filesize = $file->getSize();
      $ext = '';
      // Get file extension.
      $array = explode('.', $filename);
      $ext = sprintf("%s ", end($array));

      // Check if cta label is set.
      if (isset($cta)) {
        $filename = $cta;
      }

      $filename = sprintf('%s (%s%s)', $filename, strtoupper($ext), format_size($filesize));

      $url_options['attributes'] = [
        'type' => "$mime_type; length={filesize}",
        'title' => $filename,
        // Classes to add to the file field for icons.
        'class' => [
          'file',
          // Add a specific class for each and every mime type.
          'file--mime-' . strtr($mime_type, ['/' => '-', '.' => '-']),
          // Add a more general class for groups of well known MIME types.
          'file--' . file_icon_class($mime_type),
        ],
      ];

      // Add download variant.
      $url_options['query'][$settings['disposition']] = NULL;
      if ($settings['disposition'] == ResponseHeaderBag::DISPOSITION_INLINE) {
        if (!empty($settings['target'])) {
          // Link target only relevant for inline downloads (attachment
          // downloads will never navigate client locations)
          $url_options['attributes']['target'] = $settings['target'];
        }
      }
      if (!empty($settings['rel'])) {
        $url_options['attributes']['rel'] = $settings['rel'];
      }

      $file_uri = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
      $url = Url::fromUri($file_uri, $url_options);
      $baseUrl = \Drupal::request()->getSchemeAndHttpHost();
      $relative_url = str_replace($baseUrl, '', $url->toString());

      $markup = new FormattableMarkup($link, [
        '@url' => $relative_url,
        '@class' => $class,
        '@title' => $filename,
        '@assitive' => $assistive_text,
      ]);

      $elements[$delta] = [
        '#markup' => $markup,
      ];
    }

    return $elements;
  }

}
