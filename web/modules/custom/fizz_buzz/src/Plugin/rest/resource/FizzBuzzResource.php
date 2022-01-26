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
use Drupal\fizz_buzz\Entity\FizzBuzzStats;

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
   *  Expected input format http://fizzbuzz.docker.localhost:8000/api/fizz-buzz?var1=3&var2=5&var3=100&str1=fizz&str2=buzz&_format=json
   */
  public function get()
  {
    $params = \Drupal::request()->query->all();
    $uri = \Drupal::request()->getRequestUri();

    if ($this->checkParams($params)) {
      $result = $this->Fizzbuzz($params['var1'], $params['var2'], $params['var3'], $params['str1'], $params['str2']);
      // load stat entry if exists
      $query = \Drupal::entityQuery('fizz_buzz_stats')
        ->condition('url', $uri);

      $id = $query->execute();
      var_dump($id);
      die;
      // Add a stat entry. 
      $stat = FizzBuzzStats::create([
        'url' => $uri,
        'hits' => 447,
      ]);
      $stat->save();
      $response = new ResourceResponse($result);
      $response->addCacheableDependency($stat);
      return $response;
    }
    return [];
  }


  protected function checkParams($params)
  {
    if ($this->exists($params['var1']) && $this->exists($params['var2']) && $this->exists($params['var3'])) {
      $this->checkNumericInputs($params['var1'], $params['var2'], $params['var3']);
    }
    return true;
  }


  protected function exists($param)
  {
    if (isset($param) && !empty($param)) {
      return true;
    }
    return false;
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
   * Fizzbuzz function
   *
   * @param int $int_1
   * @param int $int_2
   * @param int $int_3
   * @param string $str_1
   * @param string $str_2
   * @return void
   */
  protected function Fizzbuzz($int_1, $int_2, $int_3, $str_1, $str_2)
  {
    $results = [];
    for ($i = 1; $i <= $int_3; $i++) {
      if ($i % $int_1 === 0 && $i % $int_2  === 0) {
        $results[] = $str_1 . $str_2;
      } else if ($i % $int_1  == 0) {
        $results[] = $str_1;
      } else if ($i % $int_2 == 0) {
        $results[] = $str_2;
      } else {
        $results[] = $i;
      }
    }

    return $results;
  }
  /**
   * Checks for input integrity function.
   *
   * @param int $int_1
   * @param int $int_2
   * @param int $int_3
   * @return void
   */
  protected function checkNumericInputs($int_1, $int_2, $int_3)
  {
    if (!is_numeric($int_1)) {
      throw new BadRequestHttpException('var1 is not a number');
    }
    if (!is_numeric($int_2)) {
      throw new BadRequestHttpException('var2 is not a number');
    }
    if (!is_numeric($int_3)) {
      throw new BadRequestHttpException('var3 input is not a number');
    }
  }
}
