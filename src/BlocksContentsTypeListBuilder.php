<?php

namespace Drupal\blockscontent;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Blocks contents type entities.
 */
class BlocksContentsTypeListBuilder extends ConfigEntityListBuilder {
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Blocks contents type');
    $header['id'] = $this->t('Machine name');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['description'] = $entity->getDescription();
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }
  
}
