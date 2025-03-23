<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PrerequisitesTable extends Table
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

        $this->setTable('prerequisites');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PrerequisiteCourses', [
            'foreignKey' => 'prerequisite_course_id',
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

        $validator
            ->boolean('co_requisite')
            ->notEmptyString('co_requisite');

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

        $rules->add($rules->existsIn(['course_id'], 'Courses'));
        $rules->add($rules->existsIn(['prerequisite_course_id'], 'PrerequisiteCourses'));

        return $rules;
    }

    public function isCourseExist()
    {

        // if the course has no prerequist return true
        if (
            !isset($this->data['Prerequisite']['prerequisite_course_id']) || strcasecmp(
                $this->data['Prerequisite']['prerequisite_course_id'],
                'none'
            ) == 0 ||
            empty($this->data['Prerequisite']['prerequisite_course_id'])
        ) {
            return true;
        }

        // check user has enter a valid course code
        $is_course_code_exist = $this->Course->find('count', array(
            'conditions' => array(
                'Course.course_code' =>
                    $this->data['Prerequisite']['prerequisite_course_id']
            )
        ));
        if ($is_course_code_exist > 0) {
            return true;
        }

        return false;
    }

    public function prerequisiteCourseCodeUnique($data = null)
    {

        // check user has selected unique prerequisite course code
        //$is_prerequisite_course_code_exist=null;
        $pre_count = 0;
        $is_unique = 1;
        //$coming_form=array();
        if (!empty($data['Prerequisite'])) {
            $pre_count = count($data['Prerequisite']);
            $data['Prerequisite'] = array_values($data['Prerequisite']);
            for ($i = 0; $i < ($pre_count - 1); $i++) {
                for ($j = $i + 1; $j < $pre_count; $j++) {
                    if (strcasecmp(
                            $data['Prerequisite'][$i]['prerequisite_course_id'],
                            $data['Prerequisite'][$j]['prerequisite_course_id']
                        ) == 0) {
                        $is_unique = 0;
                        break 2;
                    }
                }
            }
        }
        if ($is_unique == 0) {
            $this->invalidate(
                'prerequisite',
                'The prerequisite course  you selected have duplicated course id. Please select a unique prerequisite course, or delete one of the duplicated prerequisite.'
            );
            return false;
        } else {
            return true;
        }
    }

    public function deletePrerequisiteList($course_id = null, $data = null)
    {

        $dontdeleteids = array();
        $deleteids = array();
        $deleteids = $this->find(
            'list',
            array(
                'conditions' => array('Prerequisite.course_id' => $course_id),
                'fields' => 'id'
            )
        );
        if (!empty($data['Prerequisite'])) {
            foreach ($data['Prerequisite'] as $in => $va) {
                if (!empty($va['id'])) {
                    if (in_array($va['id'], $deleteids)) {
                        $dontdeleteids[] = $va['id'];
                    }
                }
            }
        }
        if (!empty($dontdeleteids)) {
            foreach ($deleteids as $in => &$va) {
                if (in_array($va, $dontdeleteids)) {
                    unset($deleteids[$in]);
                }
            }
        }


        if (!empty($deleteids)) {
            $this->deleteAll(array(
                'Prerequisite.id' => $deleteids
            ), false);
        }
    }
}
