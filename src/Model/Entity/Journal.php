<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Journal Entity
 *
 * @property int $id
 * @property string $journal_title
 * @property string $article_title
 * @property string $author
 * @property string|null $url_address
 * @property string|null $volume
 * @property string|null $issue
 * @property string|null $page_number
 * @property string|null $ISBN
 * @property int $course_id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Course[] $courses
 */
class Journal extends Entity
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
        'journal_title' => true,
        'article_title' => true,
        'author' => true,
        'url_address' => true,
        'volume' => true,
        'issue' => true,
        'page_number' => true,
        'ISBN' => true,
        'course_id' => true,
        'created' => true,
        'modified' => true,
        'courses' => true,
    ];
}
