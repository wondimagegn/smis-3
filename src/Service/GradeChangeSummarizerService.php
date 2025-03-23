<?php

namespace App\Service;

use Cake\ORM\TableRegistry;

class GradeChangeSummarizerService
{
    public function summarizeGrade(array $results, array $options = []): array
    {
        $type = isset($options['type']) ? $options['type'] : 'regular';

        if ($type === 'makeup') {
            return $this->summarizeMakeup($results, $options);
        } else if($type=='College'){
            return $this->summarizeCollege($results, $options);
        }

        return $this->summarizeRegular($results, $options);
    }
    public function summarizeGradeChangeStat(array $results, array $options = []): int
    {
        $summary=0;
        foreach ($results as $row) {

            $change = isset($row['id']) ? $row['id'] : [];
            if($change){
                $summary+=1;
            }
        }
        return $summary;

    }

    protected function summarizeRegular(array $results, array $options): array
    {
        $summary = [];

        foreach ($results as $row) {
            $examGrade = isset($row['ExamGrade']) ? $row['ExamGrade'] : [];
            $change = isset($row['ExamGradeChange']) ? $row['ExamGradeChange'] : [];

            $reg = isset($examGrade['CourseRegistration']) ? $examGrade['CourseRegistration'] : null;
            $add = isset($examGrade['CourseAdd']) ? $examGrade['CourseAdd'] : null;

            $record = $reg ?: $add;
            if (!$record || !empty($record['Student']['graduated'])) {
                continue;
            }
            if($reg){
                $record['latest_grade']= TableRegistry::getTableLocator()->get('CourseRegistrations')->getCourseRegistrationLatestGrade(
                    $reg['CourseRegistration']['id']
                );
                $record['ExamGradeHistory'] = TableRegistry::getTableLocator()->get('CourseRegistrations')->getCourseRegistrationGradeHistory(
                    $reg['ExamGrade']['CourseRegistration']['id']
                );
                $record['ExamGrade'] = TableRegistry::getTableLocator()->get('ExamGrade')->find(
                    'all',
                    array(
                        'conditions' => array('ExamGrade.course_registration_id ' => $record['ExamGrade']['CourseRegistration']['id']),
                        'recursive' => -1,
                        'order' => array('ExamGrade.created DESC')
                    )
                );


            } else if($add){
                $record['latest_grade']= TableRegistry::getTableLocator()->get('CourseAdds')->getCourseRegistrationLatestGrade(
                    $add['CourseAdd']['id']
                );
              $record['ExamGradeHistory'] = TableRegistry::getTableLocator()->get('CourseAdds')->getCourseAddGradeHistory(
                    $add['CourseAdd']['id']);
               $record['ExamGrade'] =  TableRegistry::getTableLocator()->get('ExamGrades')->find(
                    'all',
                    array(
                        'conditions' => array('ExamGrade.course_add_id 	' => $add['CourseAdd']['id']),
                        'recursive' => -1,
                        'order' => array('ExamGrade.created DESC')
                    )
                );
            }

            $published = isset($record['PublishedCourse']) ? $record['PublishedCourse'] : [];

            $college = isset($published['Department']['College']['name'])
                ? $published['Department']['College']['name']
                : (isset($published['College']['name']) ? $published['College']['name'] : 'Unknown');

            $department = isset($published['Department']['name'])
                ? $published['Department']['name']
                : (isset($published['program_id']) && $published['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');

            $program = isset($published['Section']['Program']['name']) ? $published['Section']['Program']['name'] : 'Program';
            $programType = isset($published['Section']['ProgramType']['name']) ? $published['Section']['ProgramType']['name'] : 'ProgramType';

            $index = isset($summary[$college][$department][$program][$programType])
                ? count($summary[$college][$department][$program][$programType])
                : 0;

            $summary[$college][$department][$program][$programType][$index] = [
                'Student' => isset($record['Student']) ? $record['Student'] : [],
                'Course' => isset($published['Course']) ? $published['Course'] : [],
                'latest_grade' => isset($record['latest_grade']) ? $record['latest_grade'] : null,
                'ExamGradeChange' => $change,
                'Staff' => isset($published['CourseInstructorAssignment'][0]['Staff']) ? $published['CourseInstructorAssignment'][0]['Staff'] : [],
                'Section' => isset($published['Section']) ? $published['Section'] : [],
                'ExamGradeHistory' => isset($record['grade_history']) ? $record['grade_history'] : [],
                'ExamGrades' => isset($record['exam_grades']) ? $record['exam_grades'] : [],
            ];
        }


        return $summary;
    }

    protected function summarizeMakeup(array $results, array $options): array
    {
        $summary = [];

        foreach ($results as $row) {
            $examGrade = isset($row['ExamGrade']) ? $row['ExamGrade'] : [];
            $change = isset($row['ExamGradeChange']) ? $row['ExamGradeChange'] : [];
            $makeupExam = isset($row['MakeupExam']) ? $row['MakeupExam'] : [];

            $reg = isset($examGrade['CourseRegistration']) ? $examGrade['CourseRegistration'] : null;
            $add = isset($examGrade['CourseAdd']) ? $examGrade['CourseAdd'] : null;

            $record = $reg ?: $add;
            if (!$record || !empty($record['Student']['graduated'])) {
                continue;
            }

            if($reg){
                $record['latest_grade']= TableRegistry::getTableLocator()->get('CourseRegistrations')->getCourseRegistrationLatestGrade(
                    $reg['CourseRegistration']['id']
                );
                $record['grade_history'] = TableRegistry::getTableLocator()->get('CourseRegistrations')->getCourseRegistrationGradeHistory(
                    $reg['ExamGrade']['CourseRegistration']['id']
                );
                $record['exam_grades'] = TableRegistry::getTableLocator()->get('ExamGrade')->find(
                    'all',
                    array(
                        'conditions' => array('ExamGrade.course_registration_id ' => $record['ExamGrade']['CourseRegistration']['id']),
                        'recursive' => -1,
                        'order' => array('ExamGrade.created DESC')
                    )
                );


            } else if($add){
                $record['latest_grade']= TableRegistry::getTableLocator()->get('CourseAdds')->getCourseRegistrationLatestGrade(
                    $add['CourseAdd']['id']
                );
                $record['grade_history'] = TableRegistry::getTableLocator()->get('CourseAdds')->getCourseAddGradeHistory(
                    $add['CourseAdd']['id']);
                $record['exam_grades'] = TableRegistry::getTableLocator()->get('ExamGrades')->find(
                    'all',
                    array(
                        'conditions' => array('ExamGrade.course_add_id 	' => $add['CourseAdd']['id']),
                        'recursive' => -1,
                        'order' => array('ExamGrade.created DESC')
                    )
                );
            }

            $published = isset($record['PublishedCourse']) ? $record['PublishedCourse'] : [];

            $college = isset($published['Department']['College']['name'])
                ? $published['Department']['College']['name']
                : (isset($published['College']['name']) ?
                    $published['College']['name'] : 'Unknown');

            $department = isset($published['Department']['name'])
                ? $published['Department']['name']
                : (isset($published['program_id']) &&
                $published['program_id'] == PROGRAM_REMEDIAL ?
                    'Remedial Program' : 'Freshman Program');

            $program = isset($published['Section']['Program']['name']) ? $published['Section']['Program']['name'] : 'Program';
            $programType = isset($published['Section']['ProgramType']['name']) ? $published['Section']['ProgramType']['name'] : 'ProgramType';

            $index = isset($summary[$college][$department][$program][$programType])
                ? count($summary[$college][$department][$program][$programType])
                : 0;

            $summary[$college][$department][$program][$programType][$index] = [
                'Staff' => isset($makeupExam['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']) ? $makeupExam['PublishedCourse']['CourseInstructorAssignment'][0]['Staff'] : [],
                'ExamCourse' => isset($makeupExam['PublishedCourse']['Course']) ? $makeupExam['PublishedCourse']['Course'] : [],
                'ExamSection' => isset($makeupExam['PublishedCourse']['Section']) ? $makeupExam['PublishedCourse']['Section'] : [],
                'MakeupExam' => $makeupExam,
                'Student' => isset($record['Student']) ? $record['Student'] : [],
                'Course' => isset($published['Course']) ? $published['Course'] : [],
                'latest_grade' => isset($record['latest_grade']) ? $record['latest_grade'] : null,
                'ExamGradeChange' => $change,
                'Section' => isset($published['Section']) ? $published['Section'] : [],
                'ExamGradeHistory' => isset($record['grade_history']) ? $record['grade_history'] : [],
                'ExamGrades' => isset($record['exam_grades']) ? $record['exam_grades'] : [],
            ];
        }

        return $summary;
    }
    protected function summarizeCollege(array $results, array $options): array
    {
        $summary = [];

        foreach ($results as $row) {
            $examGrade = isset($row['ExamGrade']) ? $row['ExamGrade'] : [];
            $change = isset($row['ExamGradeChange']) ? $row['ExamGradeChange'] : [];

            $reg = isset($examGrade['CourseRegistration']) ? $examGrade['CourseRegistration'] : null;
            $add = isset($examGrade['CourseAdd']) ? $examGrade['CourseAdd'] : null;

            $record = $reg ?: $add;
            if (!$record || !empty($record['Student']['graduated'])) {
                continue;
            }

            $published = isset($record['PublishedCourse']) ? $record['PublishedCourse'] : [];

            if (empty($published)) {
                continue;
            }

            if (!empty($programIds) && !in_array($published['program_id'], $programIds)) {
                continue;
            }

            if (!empty($programTypeIds) && !in_array($published['program_type_id'],
                    $programTypeIds)) {
                continue;
            }
            if($reg){
                $record['latest_grade']= TableRegistry::getTableLocator()->get('CourseRegistrations')->getCourseRegistrationLatestGrade(
                    $reg['CourseRegistration']['id']
                );
                $record['grade_history'] = TableRegistry::getTableLocator()->get('CourseRegistrations')->getCourseRegistrationGradeHistory(
                    $reg['ExamGrade']['CourseRegistration']['id']
                );
                $record['exam_grades'] = TableRegistry::getTableLocator()->get('ExamGrades')->find(
                    'all',
                    array(
                        'conditions' => array('ExamGrade.course_registration_id ' => $record['ExamGrade']['CourseRegistration']['id']),
                        'recursive' => -1,
                        'order' => array('ExamGrade.created DESC')
                    )
                );


            } else if($add){
                $record['latest_grade']= TableRegistry::getTableLocator()->get('CourseAdds')->getCourseRegistrationLatestGrade(
                    $add['CourseAdd']['id']
                );
                $record['grade_history'] = TableRegistry::getTableLocator()->get('CourseAdds')->getCourseAddGradeHistory(
                    $add['CourseAdd']['id']);
                $record['exam_grades'] = TableRegistry::getTableLocator()->get('ExamGrades')->find(
                    'all',
                    array(
                        'conditions' => array('ExamGrade.course_add_id 	' => $add['CourseAdd']['id']),
                        'recursive' => -1,
                        'order' => array('ExamGrade.created DESC')
                    )
                );
            }
            // === GROUPING KEYS ===
            $college = $published['Department']['College']['name']
                ?? $published['GivenByDepartment']['College']['name']
                ?? $published['College']['name']
                ?? 'Unknown';

            $department = $published['Department']['name']
                ?? ($published['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Freshman Program');

            $program = $published['Section']['Program']['name'] ?? 'Program';
            $programType = $published['Section']['ProgramType']['name'] ?? 'ProgramType';

            $groupKey = &$summary[$department][$program][$programType];

            $index = count($groupKey);
            $groupKey[$index] = [
                'Student' => $record['Student'] ?? [],
                'Course' => $published['Course'] ?? [],
                'Section' => $published['Section'] ?? [],
                'latest_grade' => $record['latest_grade'] ?? null,
                'ExamGradeChange' => $change,
                'Staff' => $published['CourseInstructorAssignment'][0]['Staff'] ?? [],
                'ExamGradeHistory' => $record['grade_history'] ?? [],
                'ExamGrades' => $record['exam_grades'] ?? [],
            ];
        }

        return $summary;
    }
}
