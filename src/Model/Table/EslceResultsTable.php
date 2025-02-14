<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class EslceResultsTable extends Table
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

        $this->setTable('eslce_results');
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
            ->scalar('grade')
            ->maxLength('grade', 3)
            ->requirePresence('grade', 'create')
            ->notEmptyString('grade');

        $validator
            ->scalar('exam_year')
            ->requirePresence('exam_year', 'create')
            ->notEmptyString('exam_year');

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

    function deleteEslceResultList ($student_id=null,$data=null) {
        $dontdeleteids=array();
        $deleteids=array();
        $deleteids=$this->find('list',
            array('conditions'=>array('EslceResult.student_id'=>$student_id),
                'fields'=>'id'));

        if (!empty($data['EslceResult'])) {
            foreach ($data['EslceResult'] as $in=>$va) {
                if (!empty($va['id'])) {
                    if (in_array($va['id'],$deleteids)) {
                        $dontdeleteids[]=$va['id'];
                    }

                }
            }

        }
        if (!empty($dontdeleteids)) {
            foreach ($deleteids as $in=>&$va) {
                if (in_array($va,$dontdeleteids)) {
                    unset($deleteids[$in]);
                }
            }
        }
        if (!empty($deleteids)) {
            $this->deleteAll(array(
                'EslceResult.id'=>$deleteids), false);
        }


    }
}
