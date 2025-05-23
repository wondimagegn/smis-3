<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StudentsCourseSplitSections Model
 *
 * @property \App\Model\Table\CourseSplitSectionsTable&\Cake\ORM\Association\BelongsTo $CourseSplitSections
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 *
 * @method \App\Model\Entity\StudentsCourseSplitSection get($primaryKey, $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\StudentsCourseSplitSection findOrCreate($search, callable $callback = null, $options = [])
 */
class StudentsCourseSplitSectionsTable extends Table
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

        $this->setTable('students_course_split_sections');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('CourseSplitSections', [
            'foreignKey' => 'course_split_section_id',
            'joinType' => 'INNER',
        ]);
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
        $rules->add($rules->existsIn(['course_split_section_id'], 'CourseSplitSections'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }
}
