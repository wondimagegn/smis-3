<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SurveyQuestionAnswers Model
 *
 * @property \App\Model\Table\SurveyQuestionsTable&\Cake\ORM\Association\BelongsTo $SurveyQuestions
 * @property \App\Model\Table\AlumniResponsesTable&\Cake\ORM\Association\HasMany $AlumniResponses
 *
 * @method \App\Model\Entity\SurveyQuestionAnswer get($primaryKey, $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\SurveyQuestionAnswer findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SurveyQuestionAnswersTable extends Table
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

        $this->setTable('survey_question_answers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('SurveyQuestions', [
            'foreignKey' => 'survey_question_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('AlumniResponses', [
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
            ->scalar('answer_english')
            ->maxLength('answer_english', 200)
            ->requirePresence('answer_english', 'create')
            ->notEmptyString('answer_english');

        $validator
            ->scalar('answer_amharic')
            ->maxLength('answer_amharic', 200)
            ->allowEmptyString('answer_amharic');

        $validator
            ->integer('order')
            ->requirePresence('order', 'create')
            ->notEmptyString('order');

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
        $rules->add($rules->existsIn(['survey_question_id'], 'SurveyQuestions'));

        return $rules;
    }
}
