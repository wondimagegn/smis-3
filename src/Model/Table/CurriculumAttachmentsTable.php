<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CurriculumAttachmentsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('curriculum_attachments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['curriculum_id'], 'Curriculums'));

        return $rules;
    }

    // the course given taken from previous curriculum ?
    public function isEquivalentTakenCoursesFromCurriculum($course_id, $student_id)
    {
        $currentStudentCurriculum = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $student_id), 'recursive' => -1));

        $curriculumAttachmentHistoryOfStudent = $this->find('list', array('conditions' => array('CurriculumAttachment.student_id' => $student_id, 'CurriculumAttachment.CurriculumAttachment.student_id !=' => $currentStudentCurriculum['Student']['curriculum_id']), 'fields' => array('CurriculumAttachment.student_id', 'CurriculumAttachment.curriculum_id')));

        if (!empty($curriculumAttachmentHistoryOfStudent)) {
            foreach ($curriculumAttachmentHistoryOfStudent as $k => $v) {
                $courseDetails = $this->Curriculum->Course->find('first', array('conditions' => array('Course.id' => $course_id, 'Course.curriculum_id' => $v), 'recursive' => -1));
                if (!empty($courseDetails)) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

}
