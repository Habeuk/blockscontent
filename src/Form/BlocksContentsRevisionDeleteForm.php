<?php

namespace Drupal\blockscontent\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Blocks contents revision.
 *
 * @ingroup blockscontent
 */
class BlocksContentsRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The Blocks contents revision.
   *
   * @var \Drupal\blockscontent\Entity\BlocksContentsInterface
   */
  protected $revision;

  /**
   * The Blocks contents storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blocksContentsStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->blocksContentsStorage = $container->get('entity_type.manager')->getStorage('blocks_contents');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'blocks_contents_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.blocks_contents.version_history', ['blocks_contents' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $blocks_contents_revision = NULL) {
    $this->revision = $this->BlocksContentsStorage->loadRevision($blocks_contents_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->BlocksContentsStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Blocks contents: deleted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    $this->messenger()->addMessage(t('Revision from %revision-date of Blocks contents %title has been deleted.', ['%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label()]));
    $form_state->setRedirect(
      'entity.blocks_contents.canonical',
       ['blocks_contents' => $this->revision->id()]
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {blocks_contents_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.blocks_contents.version_history',
         ['blocks_contents' => $this->revision->id()]
      );
    }
  }

}
