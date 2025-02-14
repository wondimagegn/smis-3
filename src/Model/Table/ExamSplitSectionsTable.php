<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ExamSplitSections Model
 *
 * @property \App\Model\Table\SectionSplitForExamsTable&\Cake\ORM\Association\BelongsTo $SectionSplitForExams
 * @property \App\Model\Table\ExamSchedulesTable&\Cake\ORM\Association\HasMany $ExamSchedules
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsToMany $Students
 *
 * @method \App\Model\Entity\ExamSplitSection get($primaryKey, $options = [])
 * @method \App\Model\Entity\ExamSplitSection newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ExamSplitSection[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ExamSplitSection|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ExamSplitSection saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ExamSplitSection patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ExamSplitSection[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ExamSplitSection findOrCreate($search, callable $callback = null, $options = [])
 */
class ExamSplitSectionsTable extends Table
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

        $this->setTable('exam_split_sections');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('SectionSplitForExams', [
            'foreignKey' => 'section_split_for_exam_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ExamSchedules', [
            'foreignKey' => 'exam_split_section_id',
        ]);
        $this->belongsToMany('Students', [
            'foreignKey' => 'exam_split_section_id',
            'targetForeignKey' => 'student_id',
            'joinTable' => 'students_exam_split_sections',
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
            ->scalar('section_name')
            ->maxLength('section_name', 30)
            ->requirePresence('section_name', 'create')
            ->notEmptyString('section_name');

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
        $rules->add($rules->existsIn(['section_split_for_exam_id'], 'SectionSplitForExams'));

        return $rules;
    }
}
