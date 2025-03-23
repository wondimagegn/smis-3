<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StudentsExamSplitSections Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\ExamSplitSectionsTable&\Cake\ORM\Association\BelongsTo $ExamSplitSections
 *
 * @method \App\Model\Entity\StudentsExamSplitSection get($primaryKey, $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentsExamSplitSection findOrCreate($search, callable $callback = null, $options = [])
 */
class StudentsExamSplitSectionsTable extends Table
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

        $this->setTable('students_exam_split_sections');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ExamSplitSections', [
            'foreignKey' => 'exam_split_section_id',
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
        $rules->add($rules->existsIn(['exam_split_section_id'], 'ExamSplitSections'));

        return $rules;
    }
}
