<?php

namespace Drupal\ik_d8_fetzer_engagement;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface EngagementEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Engagement Forms revision IDs for a specific Engagement Forms.
   *
   * @param \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface $entity
   *   The Engagement Forms entity.
   *
   * @return int[]
   *   Engagement Forms revision IDs (in ascending order).
   */
  public function revisionIds(EngagementEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Engagement Forms author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Engagement Forms revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface $entity
   *   The Engagement Forms entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(EngagementEntityInterface $entity);

  /**
   * Unsets the language for all Engagement Forms with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
