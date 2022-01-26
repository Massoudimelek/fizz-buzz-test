<?php

namespace Drupal\fizz_buzz\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\fizz_buzz\FizzBuzzStatsInterface;

/**
 * Defines the fizz buzz stats entity class.
 *
 * @ContentEntityType(
 *   id = "fizz_buzz_stats",
 *   label = @Translation("Fizz buzz stats"),
 *   label_collection = @Translation("Fizz buzz statses"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\fizz_buzz\FizzBuzzStatsListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\fizz_buzz\Form\FizzBuzzStatsForm",
 *       "edit" = "Drupal\fizz_buzz\Form\FizzBuzzStatsForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "fizz_buzz_stats",
 *   admin_permission = "administer fizz buzz stats",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/fizz-buzz-stats/add",
 *     "canonical" = "/fizz_buzz_stats/{fizz_buzz_stats}",
 *     "edit-form" = "/admin/content/fizz-buzz-stats/{fizz_buzz_stats}/edit",
 *     "delete-form" = "/admin/content/fizz-buzz-stats/{fizz_buzz_stats}/delete",
 *     "collection" = "/admin/content/fizz-buzz-stats"
 *   },
 *   field_ui_base_route = "entity.fizz_buzz_stats.settings"
 * )
 */
class FizzBuzzStats extends ContentEntityBase implements FizzBuzzStatsInterface
{

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime()
  {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp)
  {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
  {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Url'))
      ->setDescription(t('Called url'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['hits'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Hits'))
      ->setDescription(t('Number of hits'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the fizz buzz stats was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the fizz buzz stats was last edited.'));

    return $fields;
  }
}
