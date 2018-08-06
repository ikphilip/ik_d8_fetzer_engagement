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
    $header['id'] = $this->t('Engagement Forms ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.engagement_entity.edit_form',
      ['engagement_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
