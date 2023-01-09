<?php

namespace Drupal\blockscontent\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for blockscontent routes.
 */
class BlockscontentController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
