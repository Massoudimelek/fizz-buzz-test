<?php

namespace Drupal\fizz_buzz;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a fizz buzz stats entity type.
 */
interface FizzBuzzStatsInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the fizz buzz stats creation timestamp.
   *
   * @return int
   *   Creation timestamp of the fizz buzz stats.
   */
  public function getCreatedTime();

  /**
   * Sets the fizz buzz stats creation timestamp.
   *
   * @param int $timestamp
   *   The fizz buzz stats creation timestamp.
   *
   * @return \Drupal\fizz_buzz\FizzBuzzStatsInterface
   *   The called fizz buzz stats entity.
   */
  public function setCreatedTime($timestamp);

}
