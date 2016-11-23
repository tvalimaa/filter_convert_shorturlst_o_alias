<?php

namespace Drupal\shorturlstoalias\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a filter to help celebrate good times!
 *
 * @Filter(
 *   id = "shorturlstoalias",
 *   title = @Translation("Short urls to alias path"),
 *   description = @Translation("Convert short urls to alias path"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class Filtershorturlstoalias extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    global $base_root;
    $pattern = "/(?<=href=(\"|'))[^\"']+(?=(\"|'))/";
    preg_match_all($pattern, $text, $matches);
    $urls = $matches[0];
    $newpath = [];
    foreach($urls as $url => $path) {
      if (strpos($path, 'node') !== false || strpos($path, 'taxonomy') !== false) {
        $variable = explode("/", $path);
        if (strpos($path, 'node') !== false) {
          $dpath = '/' . $variable[count($variable) - 2] . '/' . $variable[count($variable) - 1];
        } else if (strpos($path, 'taxonomy') !== false) {
          $dpath = '/' . $variable[count($variable) - 3] . '/' . $variable[count($variable) - 2] . '/' . $variable[count($variable) - 1];
        } else {
          $dpath = '';
        }
        $newpath[$url] = $base_root . \Drupal::service('path.alias_manager')->getAliasByPath($dpath, $langcode);
      } else {
        unset($urls[$url]);
      }
    }
    return new FilterProcessResult(str_replace($urls, $newpath, $text));
  }
}