<?php

namespace Drupal\ik_d8_fetzer_engagement\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EngagementEntityTypeForm.
 */
class EngagementEntityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $engagement_entity_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $engagement_entity_type->label(),
      '#description' => $this->t("Label for the Engagement Forms type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $engagement_entity_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntityType::load',
      ],
      '#disabled' => !$engagement_entity_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $engagement_entity_type = $this->entity;
    $status = $engagement_entity_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Engagement Forms type.', [
          '%label' => $engagement_entity_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Engagement Forms type.', [
          '%label' => $engagement_entity_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($engagement_entity_type->toUrl('collection'));
  }

}
