<?php

namespace Drupal\ik_d8_fetzer_engagement\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntity;
use Drupal\ik_d8_fetzer_engagement\Entity\EngagementResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "engagement_results_rest_resource",
 *   label = @Translation("Engagement results rest resource"),
 *   uri_paths = {
 *     "canonical" = "/engagement_rest/{form_id}",
 *     "https://www.drupal.org/link-relations/create" = "/engagement_rest"
 *   }
 * )
 */
class EngagementResultsRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The bundles used by Engagement Entity
   * 
   * array
   */
  protected $engagementBundles;

  /**
   * The entity where the results are saved.
   * 
   * string
   */
  protected $resultsEntity = 'engagement_response';

  /**
   * Constructs a new EngagementResultsRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;

    $this->engagementBundles = $bundle_info = \Drupal::entityManager()->getBundleInfo('engagement_entity');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('ik_d8_fetzer_engagement'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to GET requests.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity object.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get(string $formId) {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $response = [];

    $entity = EngagementEntity::load($formId);
    if (is_null($entity->id->value)) {
      return new ModifiedResourceResponse(['error' => 'Engagement entity form not found.'], 200);
    }

    // Return custom data object appropriate for the Engagement entity requested.
    $response = $this->retrieveFormResponseData($entity);

    return new ModifiedResourceResponse($response, 200);
  }

    /**
   * Write the response to the database table.
   * 
   * @param array $data
   * 
   * @param Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntity entity
   *   EngagementEntity object.
   * 
   * @return array
   */
  public function insertFormResponseData (array $data, EngagementEntity $entity) {
    $fields = [
      'field_engagement_form' => (int) $entity->id(),
      'uid' => (int) $this->currentUser->id(),
    ];

    // Prepare data for INSERT.
    switch ($entity->bundle()) {
      case 'feedback':
        $fields['field_feedback'] = $data['thoughts'] ?? NULL;
        $fields['field_name'] = $data['name'] ?? NULL;
        $fields['field_email'] = $data['email'] ?? NULL;
        $fields['field_permission'] = (boolean) $data['permission'] ?? FALSE;

         // Required for this engagement type
        if (is_null($fields['field_feedback'])) {
          $fields['error'] = 1;
        }
        break;
      case 'poll':
        $fields['field_poll_choices'] = $data['options'] ? serialize($data['options']) : NULL;
        $fields['field_word'] = $data['other'] ?? NULL;

        if (is_null($fields['field_poll_choices'])) {
          $fields['error'] = 1;
        }
        break;
      case 'words':
        $fields['field_word'] = trim($data['word']) ?? NULL;
        $fields['field_name'] = $data['name'] ?? NULL;
        $fields['field_email'] = $data['email'] ?? NULL;

        if (is_null($fields['field_word']) || empty($fields['field_word'])) {
          $fields['error'] = 1;
        }
        break;
      default:
        $fields['error'] = 1;
    }

    // Check for any errors.
    if ($fields['error']) {
      return $fields;
    }

    // Create response entity
    $result = EngagementResponse::create($fields)
      ->save();

    if ($result) {
      return ['rid' => (int) $result];
    }

    return ['error' => 1];
  }

    /**
   * Responds to POST requests.
   *
   * @param array $data
   *   The POST request data.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post(array $data) {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    if (empty($data)) {
      throw new BadRequestHttpException('No POST data.');
    }

    // Engagement Entity ID required in order to POST data.
    if (!isset($data['fid'])) {
      throw new BadRequestHttpException('Bad request. Data missing.');
    }

    // Load the entity referenced by the POST.
    $entity = EngagementEntity::load($data['fid']);

    if (is_null($entity)) {
      throw new BadRequestHttpException('Bad request. Data missing.');
    }

    $response = $this->insertFormResponseData($data, $entity);

    if ($response['error']) {
      throw new BadRequestHttpException('Bad request. Data missing.');
    }

    return new ModifiedResourceResponse($response, 200);
  }

  /**
   * Retrieve the data in custom format for our Engagement entity types
   * 
   * @param Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntity entity
   *   EngagementEntity object.
   * 
   * @return array
   */
  public function retrieveFormResponseData (EngagementEntity $entity) {
    $response = [];
    $data = $this->getEngagementResponses($entity->id->value);

    if ($entity->bundle() === 'poll') {
      $total = count($data);

      $response = [
        'data' => [
          'total' => $total,
        ],
      ];

      // @TODO Add support for "or Other" and custom response.
      // Get options from entity
      $options = [];
      foreach($entity->field_options as $key => $option) {
        $options[$key] = $option->value;
        $response['data']['options'] = $options;
      }

      // Count results
      $results = [];
      foreach ($data as $nid => $record) {
        $choices = unserialize($record->field_poll_choices->value);
        foreach($choices as $cid => $choice) {
          if (isset($results[$cid])) {
            if ($choice > 0) {
              $results[$cid]++;
            }
          } else {
            if ($choice > 0) {
              $results[$cid] = 1;
            } else {
              $results[$cid] = 0;
            }
          }
        }
      }

      // Calculate the percentages.
      foreach ($results as $rid => $result) {
        $results[$rid] = (float) $result / $total;
      }

      $response['data']['results'] = $results;

    } else if ($entity->bundle() === 'words') {
      // Build an array which counts the frequency of words submitted for this Engagement form.
      foreach ($data as $fid => $record) {
        $word = strtolower($record->field_word->value);
        if (isset($response['data'][$word])) {
          $response['data'][$word]++;
        } else {
          $response['data'][$word] = 1;
        }
      }
    }

    return $response;
  }

  /**
   * Get the results from the Engagement Response entities.
   * 
   * @param string $entityId
   * 
   * @return array
   */
  private function getEngagementResponses (string $entityId) {
      $responses = \Drupal::entityQuery($this->resultsEntity)
      ->condition('field_engagement_form', $entityId, '=')
      ->execute();

      return EngagementResponse::loadMultiple(array_keys($responses));
  }

}
