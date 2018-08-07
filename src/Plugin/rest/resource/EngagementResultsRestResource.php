<?php

namespace Drupal\ik_d8_fetzer_engagement\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\ik_d8_fetzer_engagement\Entity\EngagementEntity;
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
  public function get(string $webformId) {
    $entity = [
      'status' => 200,
      'message' => 'OK',
      'content' => $webformId
    ];

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    return new ResourceResponse($entity, 200);
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

    $write = $this->writeResponse($data, $entity);

    if ($write['error']) {
      throw new BadRequestHttpException('Bad request. Data missing.');
    }

    return new ModifiedResourceResponse($write, 200);
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
  public function writeResponse (array $data, EngagementEntity $entity) {
    $fields = [
      'fid' => (int) $entity->id(),
      'uid' => (int) $this->currentUser->id(),
      'time' => REQUEST_TIME,
    ];

    // Prepare data for INSERT.
    switch ($entity->bundle()) {
      case 'feedback':
        $fields['feedback'] = $data['thoughts'] ?? NULL;
        $fields['name'] = $data['name'] ?? NULL;
        $fields['email'] = $data['email'] ?? NULL;
        $fields['permission'] = (int) $data['permission'] ?? 0;

        if (is_null($fields['feedback'])) {
          $fields['error'] = 1;
        }
        break;
      case 'poll':
        $fields['poll_choices'] = $data['options'] ? serialize($data['options']) : NULL;
        $fields['word'] = $data['other'] ?? NULL;

        if (is_null($fields['poll_choices'])) {
          $fields['error'] = 1;
        }
        break;
      case 'words':
        $fields['word'] = $data['word'] ?? NULL;
        $fields['name'] = $data['name'] ?? NULL;
        $fields['email'] = $data['email'] ?? NULL;

        if (is_null($fields['word']) || empty($fields['word'])) {
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

    // INSERT Data
    $database = \Drupal::database();
    $transaction = $database->startTransaction();
    try {
      $result = $database->insert('fetzer_engagement_responses')
        ->fields($fields)
        ->execute();

      return ['rid' => (int) $result];
    }
    catch (Exception $e) {
      $transaction->rollBack();

      \Drupal::logger('IK Fetzer')->error($e->getMessage());

      return ['error' => 1];
    }
  }

}
