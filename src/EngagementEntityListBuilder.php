<?php

namespace Drupal\ik_d8_fetzer_engagement;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Engagement Forms entities.
 *
 * @ingroup ik_d8_fetzer_engagement
 */
class EngagementEntityListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Name');
    $header['id'] = $this->t('ID');
    $header['type'] = $this->t('Engagement Type');
    $header['owner'] = $this->t('Owner');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\ik_d_fetzer_engagement\Entity\EngagementEntity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.engagement_entity.edit_form',
      ['engagement_entity' => $entity->id()]
    );
    $row['id'] = $entity->id();
    $row['type'] = $entity->bundle();
    $row['owner'] = $entity->getRevisionUser()->getDisplayName();
    return $row + parent::buildRow($entity);
  }

}
