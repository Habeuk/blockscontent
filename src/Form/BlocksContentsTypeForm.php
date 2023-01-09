<?php

namespace Drupal\blockscontent\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BlocksContentsTypeForm.
 */
class BlocksContentsTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $blocks_contents_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $blocks_contents_type->label(),
      '#description' => $this->t("Label for the Blocks contents type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $blocks_contents_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\blockscontent\Entity\BlocksContentsType::load',
      ],
      '#disabled' => !$blocks_contents_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $blocks_contents_type = $this->entity;
    $status = $blocks_contents_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Blocks contents type.', [
          '%label' => $blocks_contents_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Blocks contents type.', [
          '%label' => $blocks_contents_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($blocks_contents_type->toUrl('collection'));
  }

}
