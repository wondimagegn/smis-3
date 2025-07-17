<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AcademicRule Entity
 */
class AcademicRule extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * Virtual fields
     *
     * @var array
     */
    protected $_virtual = ['cmp_sgpa', 'cmp_cgpa'];

    /**
     * Getter for cmp_sgpa virtual field
     *
     * @return string|null
     */
    protected function _getCmpSgpa(): ?string
    {
        if ($this->scmo !== null && $this->sgpa !== null) {
            return $this->scmo . $this->sgpa;
        }
        return null;
    }

    /**
     * Getter for cmp_cgpa virtual field
     *
     * @return string|null
     */
    protected function _getCmpCgpa(): ?string
    {
        if ($this->ccmo !== null && $this->cgpa !== null) {
            return $this->ccmo . $this->cgpa;
        }
        return null;
    }
}
