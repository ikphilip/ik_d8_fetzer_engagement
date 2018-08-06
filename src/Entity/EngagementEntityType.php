<?php

namespace Drupal\ik_d8_fetzer_engagement\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Engagement Forms type entity.
 *
 * @ConfigEntityType(
 *   id = "engagement_entity_type",
 *   label = @Translation("Engagement Forms type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ik_d8_fetzer_engagement\EngagementEntityTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityTypeForm",
 *       "edit" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityTypeForm",
 *       "delete" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\ik_d8_fetzer_engagement\EngagementEntityTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "engagement_entity_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "engagement_entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/engagement_entity_type/{engagement_entity_type}",
 *     "add-form" = "/admin/structure/engagement_entity_type/add",
 *     "edit-form" = "/admin/structure/engagement_entity_type/{engagement_entity_type}/edit",
 *     "delete-form" = "/admin/structure/engagement_entity_type/{engagement_entity_type}/delete",
 *     "collection" = "/admin/structure/engagement_entity_type"
 *   }
 * )
 */
class EngagementEntityType extends ConfigEntityBundleBase implements EngagementEntityTypeInterface {

  /**
   * The Engagement Forms type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Engagement Forms type label.
   *
   * @var string
   */
  protected $label;

}
