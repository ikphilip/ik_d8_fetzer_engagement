<?php

namespace Drupal\ik_d8_fetzer_engagement;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Engagement response entity.
 *
 * @see \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponse.
 */
class EngagementResponseAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponseInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished engagement response entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published engagement response entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit engagement response entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete engagement response entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add engagement response entities');
  }

}
