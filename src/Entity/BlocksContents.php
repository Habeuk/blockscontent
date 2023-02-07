<?php

namespace Drupal\blockscontent\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Blocks contents entity.
 *
 * @ingroup blockscontent
 *
 * @ContentEntityType(
 *   id = "blocks_contents",
 *   label = @Translation("Blocks contents"),
 *   bundle_label = @Translation("Blocks contents type"),
 *   handlers = {
 *     "storage" = "Drupal\blockscontent\BlocksContentsStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\blockscontent\BlocksContentsListBuilder",
 *     "views_data" = "Drupal\blockscontent\Entity\BlocksContentsViewsData",
 *     "translation" = "Drupal\blockscontent\BlocksContentsTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\blockscontent\Form\BlocksContentsForm",
 *       "add" = "Drupal\blockscontent\Form\BlocksContentsForm",
 *       "edit" = "Drupal\blockscontent\Form\BlocksContentsForm",
 *       "delete" = "Drupal\blockscontent\Form\BlocksContentsDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\blockscontent\BlocksContentsHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\blockscontent\BlocksContentsAccessControlHandler",
 *   },
 *   base_table = "blocks_contents",
 *   data_table = "blocks_contents_field_data",
 *   revision_table = "blocks_contents_revision",
 *   revision_data_table = "blocks_contents_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer blocks contents entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/blocks_contents/{blocks_contents}",
 *     "add-page" = "/admin/structure/blocks_contents/add",
 *     "add-form" = "/blocks_contents/add/{blocks_contents_type}",
 *     "edit-form" = "/blocks_contents/{blocks_contents}/edit",
 *     "delete-form" = "/blocks_contents/{blocks_contents}/delete",
 *     "version-history" = "/admin/structure/blocks_contents/{blocks_contents}/revisions",
 *     "revision" = "/admin/structure/blocks_contents/{blocks_contents}/revisions/{blocks_contents_revision}/view",
 *     "revision_revert" = "/admin/structure/blocks_contents/{blocks_contents}/revisions/{blocks_contents_revision}/revert",
 *     "revision_delete" = "/admin/structure/blocks_contents/{blocks_contents}/revisions/{blocks_contents_revision}/delete",
 *     "translation_revert" = "/admin/structure/blocks_contents/{blocks_contents}/revisions/{blocks_contents_revision}/revert/{langcode}",
 *     "collection" = "/admin/structure/blocks_contents",
 *   },
 *   bundle_entity_type = "blocks_contents_type",
 *   field_ui_base_route = "entity.blocks_contents_type.edit_form"
 * )
 */
class BlocksContents extends EditorialContentEntityBase implements BlocksContentsInterface {
  
  use EntityChangedTrait;
  use EntityPublishedTrait;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id()
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);
    
    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    
    return $uri_route_parameters;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    
    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);
      
      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
    
    // If no revision author has been set explicitly,
    // make the blocks_contents owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);
    
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')->setLabel(t('Authored by'))->setDescription(t('The user ID of author of the Blocks contents entity.'))->setRevisionable(TRUE)->setSetting('target_type', 'user')->setSetting('handler', 'default')->setTranslatable(TRUE)->setDisplayOptions('view', [
      'label' => 'hidden',
      'type' => 'author',
      'weight' => 0
    ])->setDisplayOptions('form', [
      'type' => 'entity_reference_autocomplete',
      'weight' => 5,
      'settings' => [
        'match_operator' => 'CONTAINS',
        'size' => '60',
        'autocomplete_type' => 'tags',
        'placeholder' => ''
      ]
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE);
    
    $fields['name'] = BaseFieldDefinition::create('string')->setLabel(t('Name'))->setDescription(t('The name of the Blocks contents entity.'))->setRevisionable(TRUE)->setSettings([
      'max_length' => 50,
      'text_processing' => 0
    ])->setDefaultValue('')->setDisplayOptions('view', [
      'label' => 'above',
      'type' => 'string',
      'weight' => -4
    ])->setDisplayOptions('form', [
      'type' => 'string_textfield',
      'weight' => -4
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setRequired(TRUE);
    
    $fields['status']->setDescription(t('A boolean indicating whether the Blocks contents is published.'))->setDisplayOptions('form', [
      'type' => 'boolean_checkbox',
      'weight' => -3
    ]);
    
    $fields['created'] = BaseFieldDefinition::create('created')->setLabel(t('Created'))->setDescription(t('The time that the entity was created.'));
    
    $fields['changed'] = BaseFieldDefinition::create('changed')->setLabel(t('Changed'))->setDescription(t('The time that the entity was last edited.'));
    
    $fields['layout_paragraphs'] = BaseFieldDefinition::create('entity_reference')->setLabel(t(' Sections '))->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)->setDisplayOptions('form', [
      'type' => 'inline_entity_form_complex',
      'weight' => 0
    ])->setDisplayConfigurable('form', TRUE)->setDisplayConfigurable('view', TRUE)->setSetting('target_type', 'paragraph')->setSetting('handler', 'default')->setTranslatable(false)->setSetting('allow_duplicate', true);
    
    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')->setLabel(t('Revision translation affected'))->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))->setReadOnly(TRUE)->setRevisionable(TRUE)->setTranslatable(TRUE);
    
    return $fields;
  }
  
}
