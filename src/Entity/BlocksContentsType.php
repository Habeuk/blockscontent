<?php

namespace Drupal\blockscontent\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Blocks contents type entity.
 *
 * @ConfigEntityType(
 *   id = "blocks_contents_type",
 *   label = @Translation("Blocks contents type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\blockscontent\BlocksContentsTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\blockscontent\Form\BlocksContentsTypeForm",
 *       "edit" = "Drupal\blockscontent\Form\BlocksContentsTypeForm",
 *       "delete" = "Drupal\blockscontent\Form\BlocksContentsTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\blockscontent\BlocksContentsTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description"
 *   },
 *   config_prefix = "blocks_contents_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "blocks_contents",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/blocks_contents_type/{blocks_contents_type}",
 *     "add-form" = "/admin/structure/blocks_contents_type/add",
 *     "edit-form" = "/admin/structure/blocks_contents_type/{blocks_contents_type}/edit",
 *     "delete-form" = "/admin/structure/blocks_contents_type/{blocks_contents_type}/delete",
 *     "collection" = "/admin/structure/blocks_contents_type"
 *   }
 * )
 */
class BlocksContentsType extends ConfigEntityBundleBase implements BlocksContentsTypeInterface {
  
  /**
   * The Blocks contents type ID.
   *
   * @var string
   */
  protected $id;
  
  /**
   * The Blocks contents type label.
   *
   * @var string
   */
  protected $label;
  
  /**
   * The Blocks contents type description.
   *
   * @var string
   */
  public $description;
  
  /**
   *
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }
  
}
