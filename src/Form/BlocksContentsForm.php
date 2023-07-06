<?php

namespace Drupal\blockscontent\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Blocks contents edit forms.
 *
 * @ingroup blockscontent
 */
class BlocksContentsForm extends ContentEntityForm {
  
  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\blockscontent\Entity\BlocksContents $entity */
    // dump($this->entity->bundle());
    // dump($this->entity->toArray());
    // $paragraph =
    // $this->entityTypeManager->getStorage('paragraph')->load('17689');
    // dump($paragraph->toArray());
    $form = parent::buildForm($form, $form_state);
    
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    
    $status = parent::save($form, $form_state);
    
    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Blocks contents.', [
          '%label' => $entity->label()
        ]));
        break;
      
      default:
        $this->messenger()->addMessage($this->t('Saved the %label Blocks contents.', [
          '%label' => $entity->label()
        ]));
    }
    $form_state->setRedirect('entity.blocks_contents.canonical', [
      'blocks_contents' => $entity->id()
    ]);
  }
  
}
