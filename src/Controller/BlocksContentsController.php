<?php

namespace Drupal\blockscontent\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\blockscontent\Entity\BlocksContentsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BlocksContentsController.
 *
 *  Returns responses for Blocks contents routes.
 */
class BlocksContentsController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Displays a Blocks contents revision.
   *
   * @param int $blocks_contents_revision
   *   The Blocks contents revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($blocks_contents_revision) {
    $blocks_contents = $this->entityTypeManager()->getStorage('blocks_contents')
      ->loadRevision($blocks_contents_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('blocks_contents');

    return $view_builder->view($blocks_contents);
  }

  /**
   * Page title callback for a Blocks contents revision.
   *
   * @param int $blocks_contents_revision
   *   The Blocks contents revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($blocks_contents_revision) {
    $blocks_contents = $this->entityTypeManager()->getStorage('blocks_contents')
      ->loadRevision($blocks_contents_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $blocks_contents->label(),
      '%date' => $this->dateFormatter->format($blocks_contents->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Blocks contents.
   *
   * @param \Drupal\blockscontent\Entity\BlocksContentsInterface $blocks_contents
   *   A Blocks contents object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BlocksContentsInterface $blocks_contents) {
    $account = $this->currentUser();
    $blocks_contents_storage = $this->entityTypeManager()->getStorage('blocks_contents');

    $langcode = $blocks_contents->language()->getId();
    $langname = $blocks_contents->language()->getName();
    $languages = $blocks_contents->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $blocks_contents->label()]) : $this->t('Revisions for %title', ['%title' => $blocks_contents->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all blocks contents revisions") || $account->hasPermission('administer blocks contents entities')));
    $delete_permission = (($account->hasPermission("delete all blocks contents revisions") || $account->hasPermission('administer blocks contents entities')));

    $rows = [];

    $vids = $blocks_contents_storage->revisionIds($blocks_contents);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\blockscontent\Entity\BlocksContentsInterface $revision */
      $revision = $blocks_contents_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $blocks_contents->getRevisionId()) {
          $link = Link::fromTextAndUrl($date, new Url('entity.blocks_contents.revision', [
            'blocks_contents' => $blocks_contents->id(),
            'blocks_contents_revision' => $vid,
          ]))->toString();
        }
        else {
          $link = $blocks_contents->toLink($date)->toString();
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.blocks_contents.translation_revert', [
                'blocks_contents' => $blocks_contents->id(),
                'blocks_contents_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.blocks_contents.revision_revert', [
                'blocks_contents' => $blocks_contents->id(),
                'blocks_contents_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.blocks_contents.revision_delete', [
                'blocks_contents' => $blocks_contents->id(),
                'blocks_contents_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['blocks_contents_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
