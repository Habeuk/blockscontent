<?php

/**
 * @file
 * Contains blocks_contents.page.inc.
 *
 * Page callback for Blocks contents entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Blocks contents templates.
 *
 * Default template: blocks_contents.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_blocks_contents(array &$variables) {
  // Fetch BlocksContents Entity Object.
  $blocks_contents = $variables['elements']['#blocks_contents'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
