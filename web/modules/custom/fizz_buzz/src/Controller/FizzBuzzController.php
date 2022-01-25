<?php

namespace Drupal\fizz_buzz\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for fizz_buzz routes.
 */
class FizzBuzzController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
