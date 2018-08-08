<?php

/**
 * @file
 * Contains engagement_response.page.inc.
 *
 * Page callback for Engagement response entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Engagement response templates.
 *
 * Default template: engagement_response.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_engagement_response(array &$variables) {
  // Fetch EngagementResponse Entity Object.
  $engagement_response = $variables['elements']['#engagement_response'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
