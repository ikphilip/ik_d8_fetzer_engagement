<?php

namespace Drupal\ik_d8_fetzer_engagement\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Engagement Forms entities.
 *
 * @ingroup ik_d8_fetzer_engagement
 */
interface EngagementEntityInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Engagement Forms name.
   *
   * @return string
   *   Name of the Engagement Forms.
   */
  public function getName();

  /**
   * Sets the Engagement Forms name.
   *
   * @param string $name
   *   The Engagement Forms name.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface
   *   The called Engagement Forms entity.
   */
  public function setName($name);

  /**
   * Gets the Engagement Forms creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Engagement Forms.
   */
  public function getCreatedTime();

  /**
   * Sets the Engagement Forms creation timestamp.
   *
   * @param int $timestamp
   *   The Engagement Forms creation timestamp.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface
   *   The called Engagement Forms entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Engagement Forms published status indicator.
   *
   * Unpublished Engagement Forms are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Engagement Forms is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Engagement Forms.
   *
   * @param bool $published
   *   TRUE to set this Engagement Forms to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface
   *   The called Engagement Forms entity.
   */
  public function setPublished($published);

  /**
   * Gets the Engagement Forms revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Engagement Forms revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface
   *   The called Engagement Forms entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Engagement Forms revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Engagement Forms revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityInterface
   *   The called Engagement Forms entity.
   */
  public function setRevisionUserId($uid);

}
