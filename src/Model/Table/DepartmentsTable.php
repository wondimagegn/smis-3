<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DepartmentsTable extends Table
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

        $this->setTable('departments');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MoodleCategories', [
            'foreignKey' => 'moodle_category_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('AcademicCalendars', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Courses', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Curriculums', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('DepartmentStudyPrograms', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('DepartmentTransfers', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('ExtendingAcademicCalendars', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Offers', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('OnlineApplicants', [
            'foreignKey' => 'department_id',
        ]);

        $this->hasMany('ParticipatingDepartments', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Preferences', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Sections', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Specializations', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('StaffAssignes', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'department_id',
        ]);
        $this->hasMany('TakenProperties', [
            'foreignKey' => 'department_id',
        ]);

        $this->hasMany('YearLevels', [
            'foreignKey' => 'department_id',
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
            ->scalar('name')
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('shortname')
            ->maxLength('shortname', 10)
            ->allowEmptyString('shortname');

        $validator
            ->scalar('name_start_date')
            ->allowEmptyString('name_start_date');

        $validator
            ->scalar('name_end_date')
            ->allowEmptyString('name_end_date');

        $validator
            ->scalar('amharic_name')
            ->maxLength('amharic_name', 200)
            ->allowEmptyString('amharic_name');

        $validator
            ->scalar('amharic_short_name')
            ->maxLength('amharic_short_name', 50)
            ->allowEmptyString('amharic_short_name');

        $validator
            ->boolean('applay')
            ->notEmptyString('applay');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->notEmptyString('type');

        $validator
            ->scalar('type_amharic')
            ->maxLength('type_amharic', 100)
            ->notEmptyString('type_amharic');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 30)
            ->allowEmptyString('phone');

        $validator
            ->scalar('institution_code')
            ->maxLength('institution_code', 100)
            ->allowEmptyString('institution_code');

        $validator
            ->boolean('allow_year_based_curriculums')
            ->notEmptyString('allow_year_based_curriculums');

        $validator
            ->boolean('accept_course_dispatch')
            ->notEmptyString('accept_course_dispatch');

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
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['moodle_category_id'], 'MoodleCategories'));

        return $rules;
    }


    function isUniqueDepartmentInCollege()
    {
        $count = 0;
        if (!empty($this->data['Department']['id'])) {
            $count = $this->find('count', array(
                'conditions' => array(
                    'Department.college_id' => $this->data['Department']['college_id'],
                    'Department.name' => trim($this->data['Department']['name']),
                    'Department.id <> ' => $this->data['Department']['id']
                )
            ));
        } else {
            $count = $this->find('count', array(
                'conditions' => array(
                    'Department.college_id' => $this->data['Department']['college_id'],
                    'Department.name' => trim($this->data['Department']['name'])
                )
            ));
        }
        if ($count > 0) {
            return false;
        }
        return true;
    }

    function canItBeDeleted($department_id = null)
    {
        if ($this->YearLevel->find('count', array('conditions' => array('YearLevel.department_id' => $department_id))) > 0) {
            return false;
        }

        if ($this->Student->find('count', array('conditions' => array('Student.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->Section->find('count', array('conditions' => array('Section.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->GradeScale->find('count', array('conditions' => array('GradeScale.model' => 'Department', 'GradeScale.foreign_key' => $department_id))) > 0) {
            return false;
        } else if ($this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->Curriculum->find('count', array('conditions' => array('Curriculum.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.department_id' => $department_id))) > 0) {
            return false;
        } else if ($this->Staff->find('count', array('conditions' => array('Staff.department_id' => $department_id))) > 0) {
            return false;
        } else {
            return true;
        }
    }

    function allDepartmentsByCollege($include_freshman_program = 0, $only_active = 0)
    {
        $departments_organized = array();

        if (empty($only_active)) {
            $active = array(0 => 0, 1 => 1);
        } else {
            $active =  $only_active;
        }

        $departments_data = $this->College->find('all', array(
            'contain' => array(
                'Department' => array(
                    'conditions' => array(
                        'Department.active' => $active
                    )
                )
            )
        ));

        //debug($departments_data);
        if (!empty($departments_data)) {
            foreach ($departments_data as $key => $college_and_department) {
                $departments_organized[$college_and_department['College']['name']] = array();
                if ($include_freshman_program == 1) {
                    $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = $college_and_department['College']['shortname'] . ' - Pre/Freshman/Remedial';
                }
                foreach ($college_and_department['Department'] as $key => $department) {
                    $departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
                }
            }
        }
        //array_unshift($sections_organized, array('' => '--- Select Section ---'));
        //debug($departments_organized);
        return $departments_organized;
    }

    //Filter list of departments by thier privligae (It is for registrar)
    function allDepartmentsByCollege2($include_all_department = 0, $privilaged_department_ids = array(), $privilaged_collage_ids = array(), $only_active = '')
    {
        $departments_organized = array();

        if (empty($only_active)) {
            $active = array(0 => 0, 1 => 1);
        } else {
            $active =  $only_active;
        }

        if (!empty($privilaged_department_ids)) {
            $departments_data = $this->College->find('all', array(
                'contain' => array(
                    'Department' => array(
                        'conditions' => array(
                            'Department.id' => $privilaged_department_ids,
                            'Department.active' => $active
                        ),
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                )
            ));
        } else if (!empty($privilaged_collage_ids)) {
            $departments_data = $this->College->find('all', array(
                'conditions' => array(
                    'College.id' => $privilaged_collage_ids,
                    'College.active' => $active
                ),
                'contain' => array(
                    'Department' => array(
                        'conditions' => array(
                            'Department.active' => $active
                        ),
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                )
            ));
        } else {
            $departments_data = $this->College->find('all', array(
                'conditions' => array(
                    'College.active' => $active
                ),
                'contain' => array(
                    'Department' => array(
                        'conditions' => array(
                            'Department.active' => $active
                        ),
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                )
            ));
            //debug($departments_data);
        }

        if (!empty($departments_data)) {
            foreach ($departments_data as $key => $college_and_department) {
                if ($include_all_department == 1) {
                    // Added By Neway
                    if (!empty($privilaged_collage_ids) && !is_array($privilaged_collage_ids) && is_numeric($privilaged_collage_ids) && $college_and_department['College']['id'] == $privilaged_collage_ids) {
                        //for College role
                        $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
                    } else if (!empty($privilaged_department_ids) && !empty($privilaged_collage_ids) && is_array($privilaged_collage_ids) && in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
                        $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
                    } else if (!empty($privilaged_collage_ids) && is_array($privilaged_collage_ids) && in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
                        $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
                    } else if (!empty($privilaged_department_ids) && empty($privilaged_collage_ids)) {
                        //for department or registrar role without college_id passed
                        $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
                    } else if (empty($privilaged_collage_ids) && empty($privilaged_department_ids)) {
                        // other roles than colllege, department & registrar
                        $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
                    }
                    // end Added By neway
                    //$departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . '';
                } else if (is_array($privilaged_department_ids) && in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
                    $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'Pre/Freshman/Remedial - ' . $college_and_department['College']['shortname'];
                }
                foreach ($college_and_department['Department'] as $key => $department) {
                    if (is_array($privilaged_department_ids) && !empty($privilaged_department_id)) {
                        if (in_array($department['id'], $privilaged_department_ids)) {
                            $departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
                        }
                    } else if (isset($privilaged_department_id) && $department['id'] == $privilaged_department_ids) {
                        debug($department);
                        $departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
                    } else {
                        $departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
                    }
                }
            }
        }
        //array_unshift($sections_organized, array('' => '--- Select Section ---'));
        //debug($departments_organized);
        return $departments_organized;
    }

    function onlyFreshmanInAllColleges($college_ids = null, $only_active = 0)
    {
        $departments = array();

        if (empty($only_active)) {
            $active = array(0 => 0, 1 => 1);
        } else {
            $active =  $only_active;
        }

        if (!empty($college_ids)) {
            $colleges = $this->College->find('all', array(
                'conditions' => array(
                    'College.id' => $college_ids,
                    'College.active' => $active
                ),
                'recursive' => -1
            ));
        } else {
            $colleges = $this->College->find('all', array(
                'conditions' => array(
                    'College.active' => $active
                ),
                'recursive' => -1
            ));
        }

        if (!empty($colleges)) {
            foreach ($colleges as $k => $v) {
                $departments[$v['College']['name']]['c~' . $v['College']['id']] = 'Pre/Freshman/Remedial - ' . $v['College']['shortname'];
            }
        }

        return $departments;
    }

    //Filter list of departments by college (It is for college privliage use like grade view)
    function allCollegeDepartments($college_id = null, $only_active = 0, $include_freshman_program = 0)
    {
        $departments_organized = array();

        if (empty($only_active)) {
            $active = array(0 => 0, 1 => 1);
        } else {
            $active =  $only_active;
        }

        if (isset($college_id) && !empty($college_id)) {

            $departments_data = $this->College->Department->find('all', array(
                'conditions' => array(
                    'Department.college_id' => $college_id,
                    'Department.active' => $active
                ),
                'contain' => array(
                    'College' => array('id', 'name', 'shortname')
                ),
                'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC'),
                'recursive' => -1
            ));

            //$departments_organized['c~' . $college_id] = 'Freshman Program';

            if (!empty($departments_data)) {
                foreach ($departments_data as $key => $department) {
                    if ($include_freshman_program || 1) {
                        $departments_organized['c~' . $department['College']['id']] = 'Pre/Freshman/Remedial - ' . $department['College']['shortname'];
                    }
                    $departments_organized[$department['Department']['id']] = $department['Department']['name'];
                }
            }
        }
        return $departments_organized;
    }

    //Filter list of departments by thier privligae (It is for registrar)
    function allDepartmentsByCollege3($include_all_department = 0, $privilaged_department_ids = array(), $privilaged_collage_ids = array(), $only_active = 0)
    {
        $departments_organized = array();

        if (empty($only_active)) {
            $active = array(0 => 0, 1 => 1);
        } else {
            $active =  $only_active;
        }

        $departments_data = $this->College->find('all', array(
            'conditions' => array(
                'College.active' => $active
            ),
            'contain' => array(
                'Department' => array(
                    'conditions' => array(
                        'Department.active' => $active
                    ),
                ),
                'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
            )
        ));

        if (!empty($departments_data)) {
            foreach ($departments_data as $key => $college_and_department) {
                if ($include_all_department == 1) {
                    $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['shortname'] . ' Students';
                }
                /* else if(in_array($college_and_department['College']['id'], $privilaged_collage_ids)) {
                    $departments_organized[$college_and_department['College']['name']]['c~'.$college_and_department['College']['id']] = 'Freshman Program';
                } */
                foreach ($college_and_department['Department'] as $key => $department) {
                    //	if(in_array($department['id'], $privilaged_department_ids)) {
                    $departments_organized[$college_and_department['College']['name']][$department['id']] = $department['name'];
                    //  }
                }
            }
        }
        //array_unshift($sections_organized, array('' => '--- Select Section ---'));
        //debug($departments_organized);
        return $departments_organized;
    }

    function allDepartmentInCollegeIncludingPre($department_ids = null, $college_ids = null, $includePre = 0, $only_active = 0)
    {
        $departments = array();

        if (empty($only_active)) {
            $active = array(0 => 0, 1 => 1);
        } else {
            $active =  $only_active;
        }

        if (!empty($department_ids)) {
            $college_s = $this->find('all', array(
                'conditions' => array(
                    'Department.id' => $department_ids,
                    'Department.active' => $active
                ),
                'contain' => array(
                    'College' => array(
                        'conditions' => array(
                            'College.active' => $active
                        )
                    )
                ),
                'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
            ));

            if (!empty($college_s)) {
                foreach ($college_s as $k => $v) {
                    if ($includePre) {
                        $departments[$v['College']['name']]['c~' . $v['College']['id']] = 'Pre/Freshman/Remedial - ' . $v['College']['shortname'];
                    } else {
                        //$departments[$v['Department']['id']] = $v['Department']['name'];
                    }
                    $departments[$v['College']['name']][$v['Department']['id']] = $v['Department']['name'];
                }
            }
        }

        if (!empty($college_ids)) {
            $college_s = $this->find('all', array(
                'conditions' => array(
                    'Department.college_id' => $college_ids,
                    'Department.active' => $active
                ),
                'contain' => array(
                    'College' => array(
                        'conditions' => array(
                            'College.active' => $active
                        )
                    )
                ),
                'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
            ));

            if (!empty($college_s)) {
                foreach ($college_s as $k => $v) {
                    if ($includePre) {
                        $departments[$v['College']['name']]['c~' . $v['College']['id']] = 'Pre/Freshman/Remedial - ' . $v['College']['shortname'];
                    } else {
                        //$departments[$v['Department']['id']] = $v['Department']['name'];
                    }
                    $departments[$v['College']['name']][$v['Department']['id']] = $v['Department']['name'];
                }
            }
        }
        return $departments;
    }

    function allUnits($role_id = null, $unit_id = null, $allUnit = 0)
    {
        $departments_organized = array();
        //debug($allUnit);

        if ($role_id == ROLE_COLLEGE && $allUnit == 0) {
            $departments_data = $this->College->find('all', array(
                'conditions' => array('College.id' => $unit_id),
                'contain' => array(
                    'Department' => array(
                        'conditions' => array('Department.active' => 1),
                        'Specialization',
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                )
            ));

            if (!empty($departments_data)) {
                foreach ($departments_data as $key => $college_and_department) {
                    $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['name'] . '';
                }
            }
        } elseif ($role_id == ROLE_DEPARTMENT && $allUnit == 0) {
            debug($unit_id);
            $departments_data = $this->find('all', array('conditions' => array('Department.id' => $unit_id), 'contain' => array('College', 'Specialization')));
            debug($departments_data);

            if (!empty($departments_data)) {
                foreach ($departments_data as $key => $department) {
                    $departments_organized[$department['College']['name']]['d~' . $department['Department']['id']] = 'Department of ' . $department['Department']['name'];
                    if (!empty($department['Specialization'])) {
                        foreach ($department['Specialization'] as $skey => $spec) {
                            $departments_organized[$department['Department']['name']][$spec['id']] = $spec['name'];
                        }
                    }
                }
            }
        } elseif ($role_id == ROLE_REGISTRAR && $allUnit == 0) {
            $departments_data = $this->College->find('all', array(
                'contain' => array(
                    'Department' => array(
                        'conditions' => array('Department.active' => 1),
                        'Specialization',
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                ),
                'conditions' => array('College.active' => 1),
            ));

            if (!empty($departments_data)) {
                foreach ($departments_data as $key => $college_and_department) {
                    $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['name'] . '';
                    foreach ($college_and_department['Department'] as $key => $department) {
                        $departments_organized[$college_and_department['College']['name']]['d~' . $department['id']] =  $department['name'];
                        if (!empty($department['Specialization'])) {
                            foreach ($department['Specialization'] as $skey => $spec) {
                                $departments_organized[$department['Department']['name']][$spec['id']] = $spec['name'];
                            }
                        }
                    }
                }
            }
        } elseif ($allUnit == 1) {
            $departments_data = $this->College->find('all', array(
                'contain' => array(
                    'Department' => array(
                        'conditions' => array('Department.active' => 1),
                        'Specialization',
                        'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                    )
                ),
                'conditions' => array('College.active' => 1)
            ));

            if (!empty($departments_data)) {
                foreach ($departments_data as $key => $college_and_department) {
                    $departments_organized[$college_and_department['College']['name']]['c~' . $college_and_department['College']['id']] = 'All ' . $college_and_department['College']['name'] . '';
                    foreach ($college_and_department['Department'] as $key => $department) {
                        $departments_organized[$college_and_department['College']['name']]['d~' . $department['id']] = 'Department of ' . $department['name'];
                        if (!empty($department['Specialization'])) {
                            foreach ($department['Specialization'] as $skey => $spec) {
                                $departments_organized[$department['Department']['name']][$spec['id']] = $spec['name'];
                            }
                        }
                    }
                }
            }
        }
        return $departments_organized;
    }

}
