<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ProgramType Entity
 *
 * @property int $id
 * @property string $equivalent_to_id
 * @property string $name
 * @property string|null $shortname
 * @property int|null $program_modality_id
 * @property string|null $description
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\EquivalentTo $equivalent_to
 * @property \App\Model\Entity\ProgramModality $program_modality
 * @property \App\Model\Entity\AcademicCalendar[] $academic_calendars
 * @property \App\Model\Entity\AcceptedStudent[] $accepted_students
 * @property \App\Model\Entity\ClassPeriod[] $class_periods
 * @property \App\Model\Entity\Curriculum[] $curriculums
 * @property \App\Model\Entity\ExamPeriod[] $exam_periods
 * @property \App\Model\Entity\ExtendingAcademicCalendar[] $extending_academic_calendars
 * @property \App\Model\Entity\GeneralSetting[] $general_settings
 * @property \App\Model\Entity\GraduationCertificate[] $graduation_certificates
 * @property \App\Model\Entity\GraduationLetter[] $graduation_letters
 * @property \App\Model\Entity\Offer[] $offers
 * @property \App\Model\Entity\OnlineApplicant[] $online_applicants
 * @property \App\Model\Entity\OtherAcademicRule[] $other_academic_rules
 * @property \App\Model\Entity\PlacementAdditionalPoint[] $placement_additional_points
 * @property \App\Model\Entity\PlacementDeadline[] $placement_deadlines
 * @property \App\Model\Entity\PlacementParticipatingStudent[] $placement_participating_students
 * @property \App\Model\Entity\PlacementResultSetting[] $placement_result_settings
 * @property \App\Model\Entity\PlacementRoundParticipant[] $placement_round_participants
 * @property \App\Model\Entity\ProgramProgramTypeClassRoom[] $program_program_type_class_rooms
 * @property \App\Model\Entity\ProgramTypeTransfer[] $program_type_transfers
 * @property \App\Model\Entity\PublishedCourse[] $published_courses
 * @property \App\Model\Entity\Section[] $sections
 * @property \App\Model\Entity\StaffAssigne[] $staff_assignes
 * @property \App\Model\Entity\StudentStatusPattern[] $student_status_patterns
 * @property \App\Model\Entity\Student[] $students
 */
class ProgramType extends Entity
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
        'equivalent_to_id' => true,
        'name' => true,
        'shortname' => true,
        'program_modality_id' => true,
        'description' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'equivalent_to' => true,
        'program_modality' => true,
        'academic_calendars' => true,
        'accepted_students' => true,
        'class_periods' => true,
        'curriculums' => true,
        'exam_periods' => true,
        'extending_academic_calendars' => true,
        'general_settings' => true,
        'graduation_certificates' => true,
        'graduation_letters' => true,
        'offers' => true,
        'online_applicants' => true,
        'other_academic_rules' => true,
        'placement_additional_points' => true,
        'placement_deadlines' => true,
        'placement_participating_students' => true,
        'placement_result_settings' => true,
        'placement_round_participants' => true,
        'program_program_type_class_rooms' => true,
        'program_type_transfers' => true,
        'published_courses' => true,
        'sections' => true,
        'staff_assignes' => true,
        'student_status_patterns' => true,
        'students' => true,
    ];
}
