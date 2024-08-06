<?php

namespace Drupal\blockscontent\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Blocks contents entities.
 *
 * @ingroup blockscontent
 */
interface BlocksContentsInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Blocks contents name.
   *
   * @return string
   *   Name of the Blocks contents.
   */
  public function getName();

  /**
   * Sets the Blocks contents name.
   *
   * @param string $name
   *   The Blocks contents name.
   *
   * @return \Drupal\blockscontent\Entity\BlocksContentsInterface
   *   The called Blocks contents entity.
   */
  public function setName($name);

  /**
   * Gets the Blocks contents creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Blocks contents.
   */
  public function getCreatedTime();

  /**
   * Sets the Blocks contents creation timestamp.
   *
   * @param int $timestamp
   *   The Blocks contents creation timestamp.
   *
   * @return \Drupal\blockscontent\Entity\BlocksContentsInterface
   *   The called Blocks contents entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Blocks contents revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Blocks contents revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\blockscontent\Entity\BlocksContentsInterface
   *   The called Blocks contents entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Blocks contents revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Blocks contents revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\blockscontent\Entity\BlocksContentsInterface
   *   The called Blocks contents entity.
   */
  public function setRevisionUserId($uid);

}
