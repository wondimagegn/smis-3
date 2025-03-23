<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class GradeTypesTable extends Table
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

        $this->setTable('grade_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Courses', [
            'foreignKey' => 'grade_type_id',
        ]);
        $this->hasMany('GradeScales', [
            'foreignKey' => 'grade_type_id',
        ]);


        $this->hasMany('Grades', [
            'foreignKey' => 'grade_type_id',
            'dependent' => true, // Cascade delete related records
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
            ->notEmptyString('type', 'Please provide grade type name, it is required.')
            ->add('type', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'You have already set up the given grade type.',
            ]);

        return $validator;
    }

    function allowDelete($grade_id = null)
    {
        if ($this->GradeScaleDetail->find('count', array('conditions' => array('GradeType.id' => $grade_id))) > 0) {
            return false;
        } else {
            return true;
        }
    }

    function is_duplicated($grade = null)
    {
        if (!empty($grade['GradeType'])) {
            $conditions['Grade.type'] = $grade['GradeType']['type'];
            $count = $this->find('count', array('conditions' => $conditions));
            if ($count > 0) {
                $this->invalidate('duplicate_entry', 'Duplicate data entry. You have already recorded the grade type.');
                return false;
            } else {
                return true;
            }
        }

        if (!empty($grade['Grade'])) {
            //
        }
    }

    function unset_empty_rows($data = null)
    {
        if (!empty($data['GradeType'])) {
            $skip_first_row = 0;
            foreach ($data['GradeType'] as $k => &$v) {
                if ($skip_first_row == 0) {
                    //
                } else {
                    if (empty($v['grade']) && empty($v['point_value'])) {
                        unset($data['GradeType'][$k]);
                    }
                }
                $skip_first_row++;
            }
        }
        return $data;
    }

    function getGradeScaleDetails($grade_type_id = null, $program_id = null, $model = 'College', $foreign_key = null, $active = 1, $own = 0)
    {
        $grade_scale_options = array();

        if ($active !== "") {
            $grade_scale_options['GradeScale.active'] = $active;
        }

        // commented out because it works during college delegation for freshman purpose but now registrat is responsible
        // no need to say own thing
        // $grade_scale_options['GradeScale.own'] = $own;

        $grade_scale_options['GradeScale.model'] = $model;
        $grade_scale_options['GradeScale.foreign_key'] = $foreign_key;

        $grade_scale_detail = $this->find('first', array(
            'conditions' => array(
                'GradeType.id' => $grade_type_id,
                'GradeType.active' => 1
            ),
            'contain' => array(
                'Grade' => array(
                    'GradeScaleDetail' => array(
                        'GradeScale' => array(
                            'conditions' => $grade_scale_options
                        )
                    )
                )
            )
        ));

        $grade_scales = array();

        foreach ($grade_scale_detail['Grade']['0']['GradeScaleDetail'] as $key => $grade_scale_detail_temp) {
            if (isset($grade_scale_detail_temp['GradeScale']) && !empty($grade_scale_detail_temp['GradeScale']) && ($program_id == "" || ($program_id != "" && $program_id == $grade_scale_detail_temp['GradeScale']['program_id']))) {
                $grade_scales[] = $grade_scale_detail_temp['GradeScale'];
            }
        }

        $grade_scale_and_type['GradeType'] = $grade_scale_detail['GradeType'];
        $grade_scale_and_type['GradeScale'] = $grade_scales;

        return $grade_scale_and_type;
    }

    function grade_type_data()
    {
        return $this->data['GradeType'];
    }

    function is_grade_type_attached_to_course($grade_type_id = null)
    {
        $courses = $this->Course->find('count', array('conditions' => array('Course.grade_type_id' => $grade_type_id)));
        if ($courses == 0) {
            return true;
        } else {
            return false;
        }
    }
}
