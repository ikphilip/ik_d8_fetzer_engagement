<?php

namespace Drupal\ik_d8_fetzer_engagement;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntity;

/**
 * Defines a class to build a listing of Engagement response entities.
 *
 * @ingroup ik_d8_fetzer_engagement
 */
class EngagementResponseListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Engagement response ID');
    $header['form'] = $this->t('Form');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponse */
    $row['id'] = $entity->id();
    $row['form'] = EngagementEntity::load($entity->field_engagement_form->target_id)->label();
    return $row + parent::buildRow($entity);
  }

}
