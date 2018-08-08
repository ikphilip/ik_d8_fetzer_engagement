<?php

namespace Drupal\ik_d8_fetzer_engagement\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Engagement response entities.
 *
 * @ingroup ik_d8_fetzer_engagement
 */
interface EngagementResponseInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Engagement response name.
   *
   * @return string
   *   Name of the Engagement response.
   */
  public function getName();

  /**
   * Sets the Engagement response name.
   *
   * @param string $name
   *   The Engagement response name.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponseInterface
   *   The called Engagement response entity.
   */
  public function setName($name);

  /**
   * Gets the Engagement response creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Engagement response.
   */
  public function getCreatedTime();

  /**
   * Sets the Engagement response creation timestamp.
   *
   * @param int $timestamp
   *   The Engagement response creation timestamp.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponseInterface
   *   The called Engagement response entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Engagement response published status indicator.
   *
   * Unpublished Engagement response are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Engagement response is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Engagement response.
   *
   * @param bool $published
   *   TRUE to set this Engagement response to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponseInterface
   *   The called Engagement response entity.
   */
  public function setPublished($published);

}
