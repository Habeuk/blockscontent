<?php

namespace Drupal\blockscontent\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\language\Entity\ContentLanguageSettings;

/**
 * Class BlocksContentsTypeForm.
 */
class BlocksContentsTypeForm extends EntityForm {

  /**
   *
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $blocks_contents_type = $this->entity;
    dump($blocks_contents_type->toArray());
    $entity = \Drupal::entityTypeManager()->getStorage('blocks_contents_type')->load("bloc_test_de_mise_a_jour");
    dump($entity->toArray());
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $blocks_contents_type->label(),
      '#description' => $this->t("Label for the Blocks contents type."),
      '#required' => TRUE
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $blocks_contents_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\blockscontent\Entity\BlocksContentsType::load'
      ],
      '#disabled' => !$blocks_contents_type->isNew()
    ];

    $form['description'] = [
      '#title' => $this->t('Description'),
      '#type' => 'textarea',
      '#default_value' => $blocks_contents_type->getDescription(),
      '#description' => $this->t('This description is important because it allows you to reuse existing types if the fields match')
    ];

    /* You will need additional form elements for your custom properties. */

    if ($this->moduleHandler->moduleExists('language')) {
      $form['language'] = [
        '#type' => 'details',
        '#title' => $this->t('Language settings'),
        '#group' => 'additional_settings'
      ];

      $language_configuration = ContentLanguageSettings::loadByEntityTypeBundle('blocks_contents', $blocks_contents_type->id());
      $form['language']['language_configuration'] = [
        '#type' => 'language_configuration',
        '#entity_information' => [
          'entity_type' => 'blocks_contents',
          'bundle' => $blocks_contents_type->id()
        ],
        '#default_value' => $language_configuration
      ];
    }

    return $form;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $blocks_contents_type = $this->entity;
    $status = $blocks_contents_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Blocks contents type.', [
          '%label' => $blocks_contents_type->label()
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Blocks contents type.', [
          '%label' => $blocks_contents_type->label()
        ]));
    }
    $form_state->setRedirectUrl($blocks_contents_type->toUrl('collection'));
  }

}
