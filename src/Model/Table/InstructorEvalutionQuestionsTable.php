<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InstructorEvalutionQuestionsTable extends Table
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

        $this->setTable('instructor_evalution_questions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('ColleagueEvalutionRates', [
            'foreignKey' => 'instructor_evalution_question_id',
        ]);
        $this->hasMany('StudentEvalutionComments', [
            'foreignKey' => 'instructor_evalution_question_id',
        ]);
        $this->hasMany('StudentEvalutionRates', [
            'foreignKey' => 'instructor_evalution_question_id',
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
            ->scalar('question')
            ->allowEmptyString('question');

        $validator
            ->scalar('question_amharic')
            ->allowEmptyString('question_amharic');

        $validator
            ->scalar('type')
            ->notEmptyString('type');

        $validator
            ->scalar('for')
            ->notEmptyString('for');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        return $validator;
    }


    public function totalObjectiveStudentQuestion($academic_year, $semester)
    {
        $publishedCourse = ClassRegistry::init('CourseInstructorAssignment')->find('first', array(
            'contain' => array('PublishedCourse'),
            'conditions' => array(
                'CourseInstructorAssignment.semester' => $semester,
                'CourseInstructorAssignment.academic_year' => $academic_year,
                //'PublishedCourse.id in (select published_course_id from student_evalution_rates where published_course_id is not null and student_id is not null )'
            )
        ));

        if (!empty($publishedCourse)) {

            $t = ClassRegistry::init('StudentEvalutionRate')->find('all', array(
                'conditions' => array('StudentEvalutionRate.published_course_id' => $publishedCourse['PublishedCourse']['id']),
                'fields' => array('DISTINCT StudentEvalutionRate.instructor_evalution_question_id'),
                'recursive' => -1
            ));

            $totalDistinctQuestion['notactive'] = array();
            $totalDistinctQuestion['active'] = array();

            if (!empty($t)) {
                foreach ($t as $eq => $ev) {
                    //check if the question is active
                    $active = ClassRegistry::init('InstructorEvalutionQuestion')->find('first', array(
                        'conditions' => array(
                            'InstructorEvalutionQuestion.id' => $ev['StudentEvalutionRate']['instructor_evalution_question_id'],
                            'InstructorEvalutionQuestion.type' => 'objective',
                            'InstructorEvalutionQuestion.for' => 'student'
                        ),
                        'recursive' => -1
                    ));

                    if ($active['InstructorEvalutionQuestion']['active'] == 1) {
                        $totalDistinctQuestion['active'][$ev['StudentEvalutionRate']['instructor_evalution_question_id']] = $ev['StudentEvalutionRate']['instructor_evalution_question_id'];
                    } else if ($active['InstructorEvalutionQuestion']['active'] == 0) {
                        $totalDistinctQuestion['notactive'][$ev['StudentEvalutionRate']['instructor_evalution_question_id']] = $ev['StudentEvalutionRate']['instructor_evalution_question_id'];
                    }
                }
            }

            //debug($totalDistinctQuestion);
            if (count($totalDistinctQuestion['active']) >= count($totalDistinctQuestion['notactive'])) {
                $totalObjectiveStudentQuestion = count($totalDistinctQuestion['active']);
            } else {
                $totalObjectiveStudentQuestion = count($totalDistinctQuestion['notactive']);
            }

        } else {
            $totalObjectiveStudentQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->find('count', array(
                'conditions' => array(
                    'InstructorEvalutionQuestion.type' => 'objective',
                    'InstructorEvalutionQuestion.for' => 'student',
                    'InstructorEvalutionQuestion.active' => 1
                ),
                'recursive' => -1
            ));
        }

        return $totalObjectiveStudentQuestion;
    }

    public function totalObjectiveStudentQuestionActive($active = 1)
    {
        $totalObjectiveStudentQuestion = ClassRegistry::init('InstructorEvalutionQuestion')->find('count', array(
            'conditions' => array(
                'InstructorEvalutionQuestion.type' => 'objective',
                'InstructorEvalutionQuestion.for' => 'student',
                'InstructorEvalutionQuestion.active' => $active
            ),
            'recursive' => -1
        ));

        return $totalObjectiveStudentQuestion;
    }

    public function totalObjectiveColleagueQuestion($academic_year, $semester, $staff_id)
    {
        $t = $this->find('list', array(
            'conditions' => array(
                'InstructorEvalutionQuestion.for' => "colleague",
                'InstructorEvalutionQuestion.type' => "objective",
                'InstructorEvalutionQuestion.active' => 1,
            ),
            'fields' => array(
                'InstructorEvalutionQuestion.id',
                'InstructorEvalutionQuestion.id'
            )
        ));

        $ct = ClassRegistry::init('ColleagueEvalutionRate')->find('all', array(
            'conditions' => array(
                'ColleagueEvalutionRate.academic_year' => $academic_year,
                'ColleagueEvalutionRate.semester' => $semester,
                'ColleagueEvalutionRate.dept_head' => 0,
                'ColleagueEvalutionRate.staff_id' => $staff_id,
            ),
            'fields' => array('DISTINCT ColleagueEvalutionRate.instructor_evalution_question_id'),
            'contain' => array('InstructorEvalutionQuestion' => array('id', 'active', 'for'))
        ));

        //debug($ct);
        $count = 0;

        if (!empty($ct)) {

            $arr = array();
            $firstElement = $ct[0]['InstructorEvalutionQuestion']['active'];

            foreach ($ct as $k => $v) {
                //$arr[$v['ColleagueEvalutionRate']['instructor_evalution_question_id']] = $v['ColleagueEvalutionRate']['instructor_evalution_question_id'];
                if ($firstElement == $v['InstructorEvalutionQuestion']['active'] && $v['InstructorEvalutionQuestion']['for'] == 'colleague') {
                    $arr[$v['ColleagueEvalutionRate']['instructor_evalution_question_id']] = $v['ColleagueEvalutionRate']['instructor_evalution_question_id'];
                }
                /* if ($v['ColleagueEvalutionRate']['instructor_evalution_question_id'] == $t[$v['ColleagueEvalutionRate']['instructor_evalution_question_id']]) {
                    $count++;
                } */
            }

            $count = count($arr);
        } else {
            $count = count($t);
        }

        debug($count);

        return $count;

        /* $t = ClassRegistry::init('ColleagueEvalutionRate')->find('all', array(
            'conditions' => array(
                'ColleagueEvalutionRate.academic_year' => $academic_year,
                'ColleagueEvalutionRate.semester' => $semester,
                'ColleagueEvalutionRate.dept_head' => 0,
                'ColleagueEvalutionRate.instructor_evalution_question_id in (select id from instructor_evalution_questions where  `type`="objective" )'
            ),
            'fields' => array('DISTINCT ColleagueEvalutionRate.instructor_evalution_question_id'),
        )); */

    }

    public function totalObjectiveHeadQuestion($academic_year, $semester, $staff_id)
    {
        $t = $this->find('list', array(
            'conditions' => array(
                'InstructorEvalutionQuestion.for' => 'dep-head',
                'InstructorEvalutionQuestion.type' => "objective",
                'InstructorEvalutionQuestion.active' => 1,
            ),
            'fields' => array(
                'InstructorEvalutionQuestion.id',
                'InstructorEvalutionQuestion.id'
            )
        ));

        //debug(count($t));

        //return count($t);
        $ct = ClassRegistry::init('ColleagueEvalutionRate')->find('all', array(
            'conditions' => array(
                'ColleagueEvalutionRate.academic_year' => $academic_year,
                'ColleagueEvalutionRate.semester' => $semester,
                'ColleagueEvalutionRate.dept_head' => 1,
                'ColleagueEvalutionRate.staff_id' => $staff_id
            ),
            'fields' => array('DISTINCT ColleagueEvalutionRate.instructor_evalution_question_id'),
            'contain' => array(
                'InstructorEvalutionQuestion' => array('id', 'active', 'for')
            )
        ));

        //debug($ct);

        $count = 0;

        if (!empty($ct)) {

            $arr = array();
            $firstElement = $ct[0]['InstructorEvalutionQuestion']['active'];

            foreach ($ct as $k => $v) {
                //$arr[$v['ColleagueEvalutionRate']['instructor_evalution_question_id']] = $v['ColleagueEvalutionRate']['instructor_evalution_question_id'];

                if ($firstElement == $v['InstructorEvalutionQuestion']['active'] && $v['InstructorEvalutionQuestion']['for'] == 'dep-head') {
                    $arr[$v['ColleagueEvalutionRate']['instructor_evalution_question_id']] = $v['ColleagueEvalutionRate']['instructor_evalution_question_id'];
                }

                /* if ($v['ColleagueEvalutionRate']['instructor_evalution_question_id'] == $t[$v['ColleagueEvalutionRate']['instructor_evalution_question_id']]) {
                    $count++;
                } */
            }

            $count = count($arr);
        } else {
            $count = count($t);
        }

        debug($count);

        return $count;
    }

}
