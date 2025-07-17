<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Announcement Entity
 */
class Announcement extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'headline' => true,
        'story' => true,
        'is_published' => true,
        'announcement_start' => true,
        'announcement_end' => true,
        'user_id' => true,
        'created' => true,
        'user' => true
    ];
}
