<?php

namespace Drupal\ik_d8_fetzer_engagement\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Engagement response entities.
 */
class EngagementResponseViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
