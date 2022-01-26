<?php

namespace Drupal\fizz_buzz\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the fizz buzz stats entity edit forms.
 */
class FizzBuzzStatsForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New fizz buzz stats %label has been created.', $message_arguments));
      $this->logger('fizz_buzz')->notice('Created new fizz buzz stats %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The fizz buzz stats %label has been updated.', $message_arguments));
      $this->logger('fizz_buzz')->notice('Updated new fizz buzz stats %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.fizz_buzz_stats.canonical', ['fizz_buzz_stats' => $entity->id()]);
  }

}
