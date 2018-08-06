<?php

namespace Drupal\ik_d8_fetzer_engagement;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface;

/**
 * Defines the storage handler class for Engagement Forms entities.
 *
 * This extends the base storage class, adding required special handling for
 * Engagement Forms entities.
 *
 * @ingroup ik_d8_fetzer_engagement
 */
class EngagementEntityStorage extends SqlContentEntityStorage implements EngagementEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(EngagementEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {engagement_entity_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {engagement_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(EngagementEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {engagement_entity_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('engagement_entity_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
