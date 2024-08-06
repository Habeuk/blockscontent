<?php

namespace Drupal\blockscontent\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Blocks contents entities.
 */
class BlocksContentsViewsData extends EntityViewsData {

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
