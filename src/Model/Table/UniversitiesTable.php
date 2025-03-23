<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UniversitiesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('universities');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->hasMany('Attachments', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'University'],
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Please provide university name, it is required.')
            ->add('name', 'unique', [
                'rule' => function ($value, $context) {
                    return $this->isUniqueName($value, $context);
                },
                'message' => 'The university name should be unique. The name is already taken. Use another one.'
            ]);

        $validator
            ->requirePresence('p_o_box', 'create')
            ->notEmptyString('p_o_box', 'Please provide university registrar P.O.Box, it is required.');

        $validator
            ->requirePresence('telephone', 'create')
            ->notEmptyString('telephone', 'Please provide university registrar telephone, it is required.');

        $validator
            ->requirePresence('fax', 'create')
            ->notEmptyString('fax', 'Please provide university registrar fax, it is required.');

        $validator
            ->requirePresence('amharic_name', 'create')
            ->notEmptyString('amharic_name', 'Please provide university Amharic name, it is required.');

        return $validator;
    }

    public function isUniqueName($name, $context)
    {
        $conditions = ['name' => trim($name)];
        if (!empty($context['data']['id'])) {
            $conditions['id !='] = $context['data']['id'];
        }
        return !$this->exists($conditions);
    }

    public function getStudentUniversity($studentId = null)
    {
        if (!$studentId) {
            return [];
        }

        $studentsTable = $this->getTableLocator()->get('Students');
        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain(['GraduateLists'])
            ->first();

        if (!$studentDetail) {
            return [];
        }

        $admissionYear = substr($studentDetail->admissionyear, 0, 4);
        $conditions = ['Universities.academic_year <=' => $admissionYear];

        if (!empty($studentDetail->graduate_list) && !empty($studentDetail->graduate_list->graduate_date)) {
            $graduateYear = substr($studentDetail->graduate_list->graduate_date, 0, 4);
            $conditions = [
                'OR' => [
                    ['Universities.academic_year <=' => $admissionYear],
                    [
                        'Universities.applicable_for_current_student' => 1,
                        'Universities.academic_year <=' => $graduateYear
                    ]
                ]
            ];
        }

        return $this->find()
            ->where($conditions)
            ->contain(['Attachments' => function (Query $q) {
                return $q->order(['Attachments.created' => 'DESC']);
            }])
            ->order(['Universities.academic_year' => 'DESC'])
            ->first();
    }
    function attach_temp_photo($data = null)
    {
        //unset empty inputs for attachment
        if (!empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $k => &$dv) {
                if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
                    unset($data['Attachment'][$k]);
                } else {
                    if ($k == 0) {
                        $dv['group'] = 'background';
                    } else {
                        $dv['group'] = 'logo';
                    }
                    $dv['model'] = 'University';
                }
            }

            if (empty($data['Attachment'])) {
                unset($data['Attachment']);
            }
        }

        return $data;
    }
    function getSectionUniversity($sectionId)
    {
        $studentsSectionsTable = $this->getTableLocator()->get('StudentsSections');
        $studentsTable = $this->getTableLocator()->get('Students');

        // Fetch the student associated with the section
        $sectionDetail = $studentsSectionsTable->find()
            ->where(['section_id' => $sectionId])
            ->first();

        if (!$sectionDetail) {
            return [];
        }

        // Fetch student details, including graduate list
        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $sectionDetail->student_id])
            ->contain(['GraduateLists'])
            ->first();

        if (!$studentDetail) {
            return [];
        }

        $admissionYear = substr($studentDetail->admissionyear, 0, 4);
        $conditions = ['Universities.academic_year <=' => $admissionYear];

        if (!empty($studentDetail->graduate_list) && !empty($studentDetail->graduate_list->graduate_date)) {
            $graduateYear = substr($studentDetail->graduate_list->graduate_date, 0, 4);
            $conditions = [
                'OR' => [
                    ['Universities.academic_year <=' => $admissionYear],
                    [
                        'Universities.applicable_for_current_student' => 1,
                        'Universities.academic_year <=' => $graduateYear
                    ]
                ]
            ];
        }

        return $this->find()
            ->where($conditions)
            ->contain(['Attachments' => function ($q) {
                return $q->order(['Attachments.created' => 'DESC']);
            }])
            ->order(['Universities.academic_year' => 'DESC'])
            ->first() ?: [];
    }
    function getAcceptedStudentUnivrsity($acceptedStudentId = null)
    {
        $acceptedStudentsTable = $this->getTableLocator()->get('AcceptedStudents');
        $universitiesTable = $this->getTableLocator()->get('Universities');

        // Fetch the accepted student details, including related Student and GraduateList
        $studentDetail = $acceptedStudentsTable->find()
            ->where(['AcceptedStudents.id' => $acceptedStudentId])
            ->contain(['Students' => ['GraduateLists']])
            ->first();

        if (!$studentDetail) {
            return [];
        }

        $academicYear = substr($studentDetail->academicyear, 0, 4);
        $conditions = ['Universities.academic_year <=' => $academicYear];

        if (!empty($studentDetail->student) && !empty($studentDetail->student->graduate_list)) {
            $graduateYear = substr($studentDetail->student->graduate_list->graduate_date, 0, 4);
            $conditions = [
                'OR' => [
                    ['Universities.academic_year <=' => $academicYear],
                    [
                        'Universities.applicable_for_current_student' => 1,
                        'Universities.academic_year <=' => $graduateYear
                    ]
                ]
            ];
        }

        return $universitiesTable->find()
            ->where($conditions)
            ->contain(['Attachments' => function ($q) {
                return $q->order(['Attachments.created' => 'DESC']);
            }])
            ->order(['Universities.academic_year' => 'DESC'])
            ->first() ?: [];
    }
}
