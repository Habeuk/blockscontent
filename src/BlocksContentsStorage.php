<?php

namespace Drupal\blockscontent;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\blockscontent\Entity\BlocksContentsInterface;

/**
 * Defines the storage handler class for Blocks contents entities.
 *
 * This extends the base storage class, adding required special handling for
 * Blocks contents entities.
 *
 * @ingroup blockscontent
 */
class BlocksContentsStorage extends SqlContentEntityStorage implements BlocksContentsStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(BlocksContentsInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {blocks_contents_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {blocks_contents_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(BlocksContentsInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {blocks_contents_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('blocks_contents_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
