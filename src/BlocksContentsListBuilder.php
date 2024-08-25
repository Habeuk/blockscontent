<?php

namespace Drupal\blockscontent;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Blocks contents entities.
 *
 * @ingroup blockscontent
 */
class BlocksContentsListBuilder extends EntityListBuilder {
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Blocks contents ID');
    $header['name'] = $this->t('Name');
    $header['type'] = 'Type';
    return $header + parent::buildHeader();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\blockscontent\Entity\BlocksContents $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute($entity->label(), 'entity.blocks_contents.canonical', [
      'blocks_contents' => $entity->id()
    ]);
    $row['type'] = $entity->bundle();
    return $row + parent::buildRow($entity);
  }
}
