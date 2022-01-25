<?php

namespace Drupal\fizz_buzz\Plugin\rest\resource;

use Drupal\Component\Plugin\DependentPluginInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Routing\BcRoute;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Represents FizzBuzz records as resources.
 *
 * @RestResource (
 *   id = "fizz_buzz",
 *   label = @Translation("FizzBuzz"),
 *   uri_paths = {
 *     "canonical" = "/api/fizz-buzz",
 *     "https://www.drupal.org/link-relations/create" = "/api/fizz-buzz"
 *   }
 * )
 *
 * @DCG
 * This plugin exposes database records as REST resources. In order to enable it
 * import the resource configuration into active configuration storage. You may
 * find an example of such configuration in the following file:
 * core/modules/rest/config/optional/rest.resource.entity.node.yml.
 * Alternatively you can make use of REST UI module.
 * @see https://www.drupal.org/project/restui
 * For accessing Drupal entities through REST interface use
 * \Drupal\rest\Plugin\rest\resource\EntityResource plugin.
 */
class FizzBuzzResource extends ResourceBase implements DependentPluginInterface
{

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $dbConnection;

  /**
   * Constructs a Drupal\rest\Plugin\rest\resource\EntityResource object.
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
   * @param \Drupal\Core\Database\Connection $db_connection
   *   The database connection.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, Connection $db_connection)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->dbConnection = $db_connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('database')
    );
  }

  /**
   * Responds to GET requests.
   *
   * @param int $id
   *   The ID of the record.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   */
  public function get()
  {
    $param = \Drupal::request()->query->all();
    //$this->checkNumeric($int_1,$int_2,$int_3);
    $this->logger->notice('FizzBuzz record @id has been requested.', ['@id' => $id]);
    $response = new ResourceResponse($param);
    $response->addCacheableDependency($param);
    return $response;
  }



  /**
   * {@inheritdoc}
   */
  protected function getBaseRoute($canonical_path, $method)
  {
    $route = parent::getBaseRoute($canonical_path, $method);

    // Change ID validation pattern.
    if ($method != 'POST') {
      $route->setRequirement('id', '\d+');
    }

    return $route;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies()
  {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function routes()
  {
    $collection = parent::routes();

    // Take out BC routes added in base class.
    // @see https://www.drupal.org/node/2865645
    // @todo Remove this in Drupal 9.
    foreach ($collection as $route_name => $route) {
      if ($route instanceof BcRoute) {
        $collection->remove($route_name);
      }
    }

    return $collection;
  }
  /**
   * Responds to POST requests and saves the new record.
   *
   * @param mixed $data
   *   Data to write into the database.
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   */
  public function post($data)
  {

    $this->validate($data);

    $id = $this->dbConnection->insert('fizz_buzz')
      ->fields($data)
      ->execute();

    $this->logger->notice('New fizzbuzz record has been created.');

    //  $created_record = $this->loadRecord($id);

    // Return the newly created record in the response body.
    // return new ModifiedResourceResponse($created_record, 201);
  }

  /**
   * Validates incoming record.
   *
   * @param mixed $record
   *   Data to validate.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   */
  protected function validate($record)
  {
    if (!is_array($record) || count($record) == 0) {
      throw new BadRequestHttpException('No record content received.');
    }

    $allowed_fields = [
      'title',
      'description',
      'price',
    ];

    if (count(array_diff(array_keys($record), $allowed_fields)) > 0) {
      throw new BadRequestHttpException('Record structure is not correct.');
    }

    if (empty($record['title'])) {
      throw new BadRequestHttpException('Title is required.');
    } elseif (isset($record['title']) && strlen($record['title']) > 255) {
      throw new BadRequestHttpException('Title is too big.');
    }
    // @DCG Add more validation rules here.
  }

  protected function fizzbuzz($int_1, $int_2, $int_3, $str_1, $str_2, $str_3)
  {
    if ($fb < 1 || !is_numeric($fb)) {
      return '';
    } else if ($fb % 15 == 0) {
      return 'FizzBuzz';
    } else if ($fb % 3 == 0) {
      return 'Fizz';
    } else if ($fb % 5 == 0) {
      return 'Buzz';
    } else {
      return $fb;
    }
  }

  protected function checkNumeric($int_1, $int_2, $int_3)
  {
    if (!is_numeric($int_1)) {
      throw new BadRequestHttpException('The first input is not a number');
    }
    if (!is_numeric($int_2)) {
      throw new BadRequestHttpException('The second input is not a number');
    }
    if (!is_numeric($int_3)) {
      throw new BadRequestHttpException('The third input is not a number');
    }
  }
}
