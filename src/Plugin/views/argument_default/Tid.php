<?php

namespace Drupal\blockscontent\Plugin\views\argument_default;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\taxonomy\TermInterface;
use Drupal\views\Attribute\ViewsArgumentDefault;
use Drupal\views\Plugin\views\argument_default\ArgumentDefaultPluginBase;
use Drupal\blockscontent\Entity\BlocksContentsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\taxonomy\VocabularyStorageInterface;

/**
 * Taxonomy tid default argument.
 */
#[ViewsArgumentDefault(id: 'blockscontent_tid', title: new TranslatableMarkup(' "blocks content" : Taxonomy term ID from URL'))]
class Tid extends ArgumentDefaultPluginBase implements CacheableDependencyInterface {
  
  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;
  
  /**
   * The vocabulary storage.
   *
   * @var \Drupal\taxonomy\VocabularyStorageInterface
   */
  protected $vocabularyStorage;
  
  /**
   * Constructs a new Tid instance.
   *
   * @param array $configuration
   *        A configuration array containing information about the plugin
   *        instance.
   * @param string $plugin_id
   *        The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *        The plugin implementation definition. *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *        The route match.
   * @param \Drupal\taxonomy\VocabularyStorageInterface $vocabulary_storage
   *        The vocabulary storage.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, VocabularyStorageInterface $vocabulary_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    
    $this->routeMatch = $route_match;
    $this->vocabularyStorage = $vocabulary_storage;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('current_route_match'), $container->get('entity_type.manager')->getStorage('taxonomy_vocabulary'));
  }
  
  /**
   *
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    
    $options['limit'] = [
      'default' => FALSE
    ];
    $options['vids'] = [
      'default' => []
    ];
    $options['anyall'] = [
      'default' => ','
    ];
    return $options;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['limit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Limit terms by vocabulary'),
      '#default_value' => $this->options['limit']
    ];
    $options = [];
    $vocabularies = $this->vocabularyStorage->loadMultiple();
    foreach ($vocabularies as $voc) {
      $options[$voc->id()] = $voc->label();
    }
    
    $form['vids'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Vocabularies'),
      '#options' => $options,
      '#default_value' => $this->options['vids']
    ];
    
    $form['anyall'] = [
      '#type' => 'radios',
      '#title' => $this->t('Multiple-value handling'),
      '#default_value' => $this->options['anyall'],
      '#options' => [
        ',' => $this->t('Filter to items that share all terms'),
        '+' => $this->t('Filter to items that share any term')
      ]
    ];
  }
  
  public function getArgument() {
    if (($blocks_contents = $this->routeMatch->getParameter('blocks_contents')) && $blocks_contents instanceof BlocksContentsInterface) {
      $taxonomy = [];
      foreach ($blocks_contents->getFieldDefinitions() as $field) {
        if ($field->getType() == 'entity_reference' && $field->getSetting('target_type') == 'taxonomy_term') {
          $taxonomy_terms = $blocks_contents->{$field->getName()}->referencedEntities();
          /** @var \Drupal\taxonomy\TermInterface $taxonomy_term */
          foreach ($taxonomy_terms as $taxonomy_term) {
            $taxonomy[$taxonomy_term->id()] = $taxonomy_term->bundle();
          }
        }
      }
      if (!empty($this->options['limit'])) {
        $tids = [];
        // Filter by vocabulary
        foreach ($taxonomy as $tid => $vocab) {
          if (!empty($this->options['vids'][$vocab])) {
            $tids[] = $tid;
          }
        }
        return implode($this->options['anyall'], $tids);
      }
      // Return all tids.
      else {
        $keys = implode($this->options['anyall'], array_keys($taxonomy));
        return $keys;
      }
    }
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return [
      'url'
    ];
  }
}