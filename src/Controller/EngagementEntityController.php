<?php

namespace Drupal\ik_d8_fetzer_engagement\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface;

/**
 * Class EngagementEntityController.
 *
 *  Returns responses for Engagement Forms routes.
 */
class EngagementEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Engagement Forms  revision.
   *
   * @param int $engagement_entity_revision
   *   The Engagement Forms  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($engagement_entity_revision) {
    $engagement_entity = $this->entityManager()->getStorage('engagement_entity')->loadRevision($engagement_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('engagement_entity');

    return $view_builder->view($engagement_entity);
  }

  /**
   * Page title callback for a Engagement Forms  revision.
   *
   * @param int $engagement_entity_revision
   *   The Engagement Forms  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($engagement_entity_revision) {
    $engagement_entity = $this->entityManager()->getStorage('engagement_entity')->loadRevision($engagement_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $engagement_entity->label(), '%date' => format_date($engagement_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Engagement Forms .
   *
   * @param \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface $engagement_entity
   *   A Engagement Forms  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(EngagementEntityInterface $engagement_entity) {
    $account = $this->currentUser();
    $langcode = $engagement_entity->language()->getId();
    $langname = $engagement_entity->language()->getName();
    $languages = $engagement_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $engagement_entity_storage = $this->entityManager()->getStorage('engagement_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $engagement_entity->label()]) : $this->t('Revisions for %title', ['%title' => $engagement_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all engagement forms revisions") || $account->hasPermission('administer engagement forms entities')));
    $delete_permission = (($account->hasPermission("delete all engagement forms revisions") || $account->hasPermission('administer engagement forms entities')));

    $rows = [];

    $vids = $engagement_entity_storage->revisionIds($engagement_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\ik_d8_fetzer_engagement\EngagementEntityInterface $revision */
      $revision = $engagement_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $engagement_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.engagement_entity.revision', ['engagement_entity' => $engagement_entity->id(), 'engagement_entity_revision' => $vid]));
        }
        else {
          $link = $engagement_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
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
              'url' => Url::fromRoute('entity.engagement_entity.revision_revert', ['engagement_entity' => $engagement_entity->id(), 'engagement_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.engagement_entity.revision_delete', ['engagement_entity' => $engagement_entity->id(), 'engagement_entity_revision' => $vid]),
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

    $build['engagement_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
