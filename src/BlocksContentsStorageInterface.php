<?php

namespace Drupal\blockscontent;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface BlocksContentsStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Blocks contents revision IDs for a specific Blocks contents.
   *
   * @param \Drupal\blockscontent\Entity\BlocksContentsInterface $entity
   *   The Blocks contents entity.
   *
   * @return int[]
   *   Blocks contents revision IDs (in ascending order).
   */
  public function revisionIds(BlocksContentsInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Blocks contents author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Blocks contents revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\blockscontent\Entity\BlocksContentsInterface $entity
   *   The Blocks contents entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(BlocksContentsInterface $entity);

  /**
   * Unsets the language for all Blocks contents with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
