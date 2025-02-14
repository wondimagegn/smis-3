<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Payment Entity
 *
 * @property int $id
 * @property int $student_id
 * @property string $academic_year
 * @property string $semester
 * @property string $reference_number
 * @property float $fee_amount
 * @property bool|null $tutition_fee
 * @property bool|null $meal
 * @property bool|null $accomodation
 * @property bool|null $health
 * @property \Cake\I18n\FrozenTime $payment_date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Student $student
 */
class Payment extends Entity
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
        'student_id' => true,
        'academic_year' => true,
        'semester' => true,
        'reference_number' => true,
        'fee_amount' => true,
        'tutition_fee' => true,
        'meal' => true,
        'accomodation' => true,
        'health' => true,
        'payment_date' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];
}
