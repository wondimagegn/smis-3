<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class EheeceResultsTable extends Table
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

        $this->setTable('eheece_results');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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

        $validator
            ->scalar('subject')
            ->maxLength('subject', 20)
            ->requirePresence('subject', 'create')
            ->notEmptyString('subject');

        $validator
            ->numeric('mark')
            ->requirePresence('mark', 'create')
            ->notEmptyString('mark');

        $validator
            ->date('exam_year')
            ->requirePresence('exam_year', 'create')
            ->notEmptyDate('exam_year');

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

        return $rules;
    }

    function deleteEheeceResultList($student_id = null, $data = null)
    {
        $dontdeleteids = array();
        $deleteids = array();

        $deleteids = $this->find('list', array(
            'conditions' => array('EheeceResult.student_id' => $student_id),
            'fields' => 'id'
        ));

        if (!empty($data['EheeceResult'])) {
            foreach ($data['EheeceResult'] as $in => $va) {
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
            $this->deleteAll(array('EheeceResult.id' => $deleteids), false);
        }
    }

    function updateExamTakenDate($college_id, $admissionYear)
    {
        $updateExamTakenDate = array();

        if (empty($college_id) && empty($takenDate)) {
            return 0;
        }

        $studenLists = ClassRegistry::init('Student')->find('all', array(
            'conditions' => array(
                'Student.college_id' => $college_id,
                'Student.admissionyear' => $admissionYear,
                'Student.graduated' => 0
            ),
            'contain' => array('EheeceResult')
        ));

        $takenDate = explode('-', $admissionYear);
        $count = 0;

        if (!empty($studenLists)) {
            foreach ($studenLists as $vList) {
                foreach ($vList['EheeceResult'] as $eheeResult) {
                    $updateExamTakenDate['EheeceResult'][$count]['id'] = $eheeResult['id'];
                    $updateExamTakenDate['EheeceResult'][$count]['exam_year'] = $takenDate[0] . '-07-01';
                    $count++;
                }
            }
        }

        if (!empty($updateExamTakenDate['EheeceResult'])) {
            if ($this->saveAll($updateExamTakenDate['EheeceResult'], array('validate' => false))) {
            }
        }
    }
}
