<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlumniResponses Model
 *
 * @property \App\Model\Table\AlumnisTable&\Cake\ORM\Association\BelongsTo $Alumnis
 * @property \App\Model\Table\SurveyQuestionsTable&\Cake\ORM\Association\BelongsTo $SurveyQuestions
 * @property \App\Model\Table\SurveyQuestionAnswersTable&\Cake\ORM\Association\BelongsTo $SurveyQuestionAnswers
 *
 * @method \App\Model\Entity\AlumniResponse get($primaryKey, $options = [])
 * @method \App\Model\Entity\AlumniResponse newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AlumniResponse[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AlumniResponse|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AlumniResponse saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AlumniResponse patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AlumniResponse[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AlumniResponse findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AlumniResponsesTable extends Table
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

        $this->setTable('alumni_responses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Alumnis', [
            'foreignKey' => 'alumni_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SurveyQuestions', [
            'foreignKey' => 'survey_question_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('SurveyQuestionAnswers', [
            'foreignKey' => 'survey_question_answer_id',
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
            ->scalar('specifiy')
            ->maxLength('specifiy', 200)
            ->allowEmptyString('specifiy');

        $validator
            ->integer('mother')
            ->notEmptyString('mother');

        $validator
            ->integer('father')
            ->notEmptyString('father');

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
        $rules->add($rules->existsIn(['alumni_id'], 'Alumnis'));
        $rules->add($rules->existsIn(['survey_question_id'], 'SurveyQuestions'));
        $rules->add($rules->existsIn(['survey_question_answer_id'], 'SurveyQuestionAnswers'));

        return $rules;
    }
}
