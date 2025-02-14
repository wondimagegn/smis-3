<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GeneralSetting Entity
 *
 * @property int $id
 * @property string $program_id
 * @property string|null $program_type_id
 * @property int $daysAvaiableForGradeChange
 * @property int $daysAvaiableForNgToF
 * @property int $daysAvaiableForDoToF
 * @property int $daysAvailableForFxToF
 * @property int $weekCountForAcademicYear
 * @property int $semesterCountForAcademicYear
 * @property int $weekCountForOneSemester
 * @property int $daysAvailableForStaffEvaluation
 * @property bool $allowStaffEvaluationAfterGradeSubmission
 * @property int $minimumCreditForStatus
 * @property int $maximumCreditPerSemester
 * @property bool $allowMealWithoutCostsharing
 * @property bool $notifyStudentsGradeByEmail
 * @property bool $allowStudentsGradeViewWithouInstructorsEvalution
 * @property bool $allowRegistrationWithoutPayment
 * @property bool $onlyAllowCourseAddForFailedGrades
 * @property bool $allowCourseAddFromHigherYearLevelSections
 * @property bool $allowGradeReportPdfDownloadToStudents
 * @property bool $allowRegistrationSlipPdfDownloadToStudents
 * @property bool $allowStudentsToResetPasswordByEmail
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class GeneralSetting extends Entity
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
        'program_id' => true,
        'program_type_id' => true,
        'daysAvaiableForGradeChange' => true,
        'daysAvaiableForNgToF' => true,
        'daysAvaiableForDoToF' => true,
        'daysAvailableForFxToF' => true,
        'weekCountForAcademicYear' => true,
        'semesterCountForAcademicYear' => true,
        'weekCountForOneSemester' => true,
        'daysAvailableForStaffEvaluation' => true,
        'allowStaffEvaluationAfterGradeSubmission' => true,
        'minimumCreditForStatus' => true,
        'maximumCreditPerSemester' => true,
        'allowMealWithoutCostsharing' => true,
        'notifyStudentsGradeByEmail' => true,
        'allowStudentsGradeViewWithouInstructorsEvalution' => true,
        'allowRegistrationWithoutPayment' => true,
        'onlyAllowCourseAddForFailedGrades' => true,
        'allowCourseAddFromHigherYearLevelSections' => true,
        'allowGradeReportPdfDownloadToStudents' => true,
        'allowRegistrationSlipPdfDownloadToStudents' => true,
        'allowStudentsToResetPasswordByEmail' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
    ];
}
