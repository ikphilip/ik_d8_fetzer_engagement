<?php

namespace Drupal\ik_d8_fetzer_engagement\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Engagement response edit forms.
 *
 * @ingroup ik_d8_fetzer_engagement
 */
class EngagementResponseForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponse */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Engagement response.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Engagement response.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.engagement_response.canonical', ['engagement_response' => $entity->id()]);
  }

}
