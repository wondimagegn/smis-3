<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Alumni Table
 */
class AlumniTable extends Table
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

        $this->setTable('alumni');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('AlumniResponses', [
            'foreignKey' => 'alumni_id',
            'dependent' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('full_name')
            ->requirePresence('full_name', 'create')
            ->notEmptyString('full_name', 'Please provide your full name.')
            ->scalar('father_name')
            ->requirePresence('father_name', 'create')
            ->notEmptyString('father_name', 'Please provide your father name.')
            ->scalar('region')
            ->requirePresence('region', 'create')
            ->notEmptyString('region', 'Please provide your region.')
            ->scalar('woreda')
            ->requirePresence('woreda', 'create')
            ->notEmptyString('woreda', 'Please provide your woreda.')
            ->scalar('housenumber')
            ->requirePresence('housenumber', 'create')
            ->notEmptyString('housenumber', 'Please provide your house number.')
            ->email('email', false, 'Please enter a valid email address.')
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Please enter a valid email address.')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The email address is used by someone. Please provide a unique different email.'
            ])
            ->scalar('mobile')
            ->requirePresence('mobile', 'create')
            ->notEmptyString('mobile', 'Please enter a valid mobile.')
            ->scalar('sex')
            ->requirePresence('sex', 'create')
            ->notEmptyString('sex', 'Please select your gender.')
            ->scalar('placeofbirthregion')
            ->requirePresence('placeofbirthregion', 'create')
            ->notEmptyString('placeofbirthregion', 'Please provide place of birth region.')
            ->scalar('fieldofstudy')
            ->requirePresence('fieldofstudy', 'create')
            ->notEmptyString('fieldofstudy', 'Please provide field of study.')
            ->numeric('age')
            ->requirePresence('age', 'create')
            ->notEmptyString('age', 'Please provide your current age.');

        return $validator;
    }

    /**
     * Formats alumni response data
     *
     * @param array $data Alumni response data
     * @return array Formatted data
     */
    public function formatResponse(array $data)
    {
        $formattedData = [
            'Alumnus' => $data['Alumnus'],
            'AlumniResponse' => []
        ];

        $count = 0;
        foreach ($data['AlumniResponse'] as $response) {
            $answer = $response['answer'] ?? [];
            if (
                !empty($answer['mother']) &&
                !empty($answer['father'])
            ) {
                $formattedData['AlumniResponse'][$count] = [
                    'survey_question_id' => $response['survey_question_id'],
                    'mother' => 1,
                    'survey_question_answer_id' => $answer['mother']
                ];
                $count++;
                $formattedData['AlumniResponse'][$count] = [
                    'survey_question_id' => $response['survey_question_id'],
                    'father' => 1,
                    'survey_question_answer_id' => $answer['father']
                ];
            } else {
                if (empty($answer)) {
                    $formattedData['AlumniResponse'][$count] = [
                        'survey_question_id' => $response['survey_question_id'],
                        'specifiy' => $response['specifiy'] ?? null
                    ];
                } elseif (is_array($answer)) {
                    foreach ($answer as $answerKey => $answerValue) {
                        if ($answerValue == 1) {
                            $formattedData['AlumniResponse'][$count] = [
                                'survey_question_id' => $response['survey_question_id'],
                                'specifiy' => $response['specifiy'] ?? null,
                                'survey_question_answer_id' => $answerKey
                            ];
                            $count++;
                        }
                    }
                } elseif (!empty($answer) && !is_array($answer)) {
                    $formattedData['AlumniResponse'][$count] = [
                        'survey_question_id' => $response['survey_question_id'],
                        'specifiy' => $response['specifiy'] ?? null,
                        'survey_question_answer_id' => $answer
                    ];
                }
            }
            $count++;
        }

        return $formattedData;
    }

    /**
     * Checks if an alumni has completed all round one survey questions
     *
     * @param int|null $studentId Student ID
     * @return bool True if all questions answered, false otherwise
     */
    public function completedRoundOneQuestionnaire($studentId = null)
    {
        if (!$studentId) {
            return false;
        }

        $alumni = $this->find()
            ->select(['Alumni.id'])
            ->where(['Alumni.student_id' => $studentId])
            ->first();

        if (empty($alumni)) {
            return false;
        }

        $surveyQuestions = $this->SurveyQuestions->find()
            ->contain(['SurveyQuestionAnswers'])
            ->toArray();

        if (!empty($surveyQuestions)) {
            $alumniResponsesTable = TableRegistry::getTableLocator()->get('AlumniResponses');
            foreach ($surveyQuestions as $question) {
                $responseCount = $alumniResponsesTable->find()
                    ->where([
                        'AlumniResponses.alumni_id' => $alumni->id,
                        'AlumniResponses.survey_question_id' => $question->id
                    ])
                    ->count();

                if ($responseCount === 0) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Retrieves alumni survey responses for given student IDs
     *
     * @param array $studentIds Student IDs
     * @return array Alumni survey responses
     */
    public function getSelectedAlumniSurvey(array $studentIds)
    {
        return $this->find()
            ->where(['Alumni.student_id IN' => $studentIds])
            ->contain([
                'AlumniResponses' => [
                    'SurveyQuestions',
                    'SurveyQuestionAnswers'
                ]
            ])
            ->toArray();
    }

    /**
     * Retrieves completed survey responses for given student IDs
     *
     * @param array $studentIds Student IDs
     * @return array Completed survey responses
     */
    public function getCompletedSurvey(array $studentIds)
    {
        $alumniResponses = $this->find()
            ->where(['Alumni.student_id IN' => $studentIds])
            ->contain([
                'AlumniResponses' => [
                    'SurveyQuestions',
                    'SurveyQuestionAnswers'
                ]
            ])
            ->toArray();

        $students = [];
        foreach ($alumniResponses as $response) {
            $key = $response->full_name . '~' . $response->student_id;
            foreach ($response->alumni_responses as $alumniResponse) {
                if (!empty($alumniResponse->mother)) {
                    $students[$key][$alumniResponse->survey_question_id]['mother'] = $alumniResponse->toArray();
                } elseif (!empty($alumniResponse->father)) {
                    $students[$key][$alumniResponse->survey_question_id]['father'] = $alumniResponse->toArray();
                } else {
                    if ($alumniResponse->survey_question->allow_multiple_answers == 1) {
                        $students[$key][$alumniResponse->survey_question_id]['answer'][] = $alumniResponse->toArray();
                    } elseif (
                        $alumniResponse->survey_question->answer_required_yn == 1 &&
                        !empty($alumniResponse->survey_question_answer_id)
                    ) {
                        $students[$key][$alumniResponse->survey_question_id]['answer'] = $alumniResponse->toArray();
                    } elseif (empty($alumniResponse->survey_question_answer_id) && !empty($alumniResponse->specifiy)) {
                        $students[$key][$alumniResponse->survey_question_id]['answer'] = $alumniResponse->specifiy;
                    }
                }
            }
        }

        return $students;
    }

    /**
     * Checks if a student is part of a graduating class
     *
     * @param int|null $studentId Student ID
     * @return bool True if graduating, false otherwise
     */
    public function checkIfStudentGraduatingClass($studentId = null)
    {
        if (!$studentId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');

        $studentCurriculum = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain(['Curriculums'])
            ->first();

        if (!$studentCurriculum) {
            return false;
        }

        $registrations = $courseRegistrationsTable->find()
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses'])
            ->toArray();

        $sumRegistered = 0;
        $graduatingCourseTaken = 0;
        foreach ($registrations as $registration) {
            $sumRegistered += $registration->published_course->course->credit ?? 0;
            if ($registration->published_course->course->thesis) {
                $graduatingCourseTaken = 1;
                break;
            }
        }

        $exemptionSum = $courseExemptionsTable->find()
            ->select(['sumex' => 'SUM(course_taken_credit)'])
            ->where(['CourseExemptions.student_id' => $studentId])
            ->order(['sumex' => 'DESC'])
            ->first();

        $exemptionCredit = $exemptionSum->sumex ?? 0;

        if (
            ($sumRegistered + $exemptionCredit) >= $studentCurriculum->curriculum->minimum_credit_points ||
            $graduatingCourseTaken == 1
        ) {
            return true;
        }

        return false;
    }
}
