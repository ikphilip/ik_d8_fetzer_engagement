<?php

namespace Drupal\ik_d8_fetzer_engagement\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Engagement Forms entity.
 *
 * @ingroup ik_d8_fetzer_engagement
 *
 * @ContentEntityType(
 *   id = "engagement_entity",
 *   label = @Translation("Engagement Forms"),
 *   bundle_label = @Translation("Engagement Forms type"),
 *   handlers = {
 *     "storage" = "Drupal\ik_d8_fetzer_engagement\EngagementEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ik_d8_fetzer_engagement\EngagementEntityListBuilder",
 *     "views_data" = "Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityForm",
 *       "add" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityForm",
 *       "edit" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityForm",
 *       "delete" = "Drupal\ik_d8_fetzer_engagement\Form\EngagementEntityDeleteForm",
 *     },
 *     "access" = "Drupal\ik_d8_fetzer_engagement\EngagementEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\ik_d8_fetzer_engagement\EngagementEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "engagement_entity",
 *   revision_table = "engagement_entity_revision",
 *   revision_data_table = "engagement_entity_field_revision",
 *   admin_permission = "administer engagement forms entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/engagement_entity/{engagement_entity}",
 *     "add-page" = "/admin/structure/engagement_entity/add",
 *     "add-form" = "/admin/structure/engagement_entity/add/{engagement_entity_type}",
 *     "edit-form" = "/admin/structure/engagement_entity/{engagement_entity}/edit",
 *     "delete-form" = "/admin/structure/engagement_entity/{engagement_entity}/delete",
 *     "version-history" = "/admin/structure/engagement_entity/{engagement_entity}/revisions",
 *     "revision" = "/admin/structure/engagement_entity/{engagement_entity}/revisions/{engagement_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/engagement_entity/{engagement_entity}/revisions/{engagement_entity_revision}/revert",
 *     "revision_delete" = "/admin/structure/engagement_entity/{engagement_entity}/revisions/{engagement_entity_revision}/delete",
 *     "collection" = "/admin/structure/engagement_entity",
 *   },
 *   bundle_entity_type = "engagement_entity_type",
 *   field_ui_base_route = "entity.engagement_entity_type.edit_form"
 * )
 */
class EngagementEntity extends RevisionableContentEntityBase implements EngagementEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the engagement_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Engagement Forms entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Engagement Forms entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Engagement Forms is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
