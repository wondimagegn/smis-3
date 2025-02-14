<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExcludedPublishedCourseExamsTable extends Table
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

        $this->setTable('excluded_published_course_exams');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
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
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));

        return $rules;
    }


    function beforeDeleteCheckEligibility($id=null,$college_id=null){
        $department_ids = $this->PublishedCourse->Department->find('list',array('fields'=>array('Department.id','Department.id'),'conditions'=>array('Department.college_id'=>$college_id)));
        $published_course_ids = $this->PublishedCourse->find('list', array('fields'=>array('PublishedCourse.id','PublishedCourse.id'), 'conditions'=>array("OR"=>array('PublishedCourse.college_id'=>$college_id,'PublishedCourse.department_id'=>$department_ids))));
        $count = $this->find('count',array('conditions'=>array('ExcludedPublishedCourseExam.published_course_id'=>$published_course_ids, 'ExcludedPublishedCourseExam.id'=>$id)));
        if($count >0){
            return true;
        } else{
            return false;
        }
    }
}
