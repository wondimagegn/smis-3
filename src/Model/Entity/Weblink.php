<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Weblink Entity
 *
 * @property int $id
 * @property string $title
 * @property string $url_address
 * @property string|null $author
 * @property string|null $year
 * @property int $course_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Course[] $courses
 */
class Weblink extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'title' => true,
        'url_address' => true,
        'author' => true,
        'year' => true,
        'course_id' => true,
        'created' => true,
        'modified' => true,
        'courses' => true,
    ];
}
