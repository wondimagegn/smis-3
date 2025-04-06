<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class ContinuousAssessmentController extends AppController {
         public $name = "ContinuousAssessment";
         public $uses = array();


		 public $menuOptions = array(
		 	'parent' => 'evalution',
		 	'exclude'=>array('viewContinouseAssessementSetup'),
			 'alias' => array(
                    'index' => 'View All Continuous',
                    'viewContinouseAssessementDetail'=>'View Continuous Assessment Report',
            )

		 );
    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Highcharts.Highcharts');
    }

         public $layout = 'report';
		 public function beforeRender(Event $event)
         {
             parent::beforeRender($event);

            $acyear_array_data = $this->AcademicYear->acyearArray();
            //To diplay current academic year as default in drop down list
            $defaultacademicyear=$this->AcademicYear->currentAcademicYear();
            foreach($acyear_array_data as $k=>$v){
                    if($v==$defaultacademicyear){
                    $defaultacademicyear=$k;
                        break;
                    }
            }
            $this->set(compact('acyear_array_data','defaultacademicyear'));
            unset($this->request->data['User']['password']);
        }
        public function beforeFilter(Event $event){
            parent::beforeFilter($event);
            $this->Auth->Allow();

        }
		public function index() {

			   $this->layout='report';
			   $currentAcademicCalender=ClassRegistry::init('AcademicCalendar')->find('all',
			   array('conditions'=>array('AcademicCalendar.academic_year'=>
			   $this->AcademicYear->current_academicyear()
			   )));
			   $currentSemester=ClassRegistry::init('AcademicCalendar')->currentSemesterInTheDefinedAcademicCalender($this->AcademicYear->current_academicyear());
			   $currentAcademicYear=$this->AcademicYear->current_academicyear();

				$total_students= ClassRegistry::init('Student')->find('count',array('recursive'=>-1));
				$total_male_students= ClassRegistry::init('Student')->find('count',array('recursive'=>-1,'conditions'=>array('Student.gender'=>'male')));
				$total_female_students= ClassRegistry::init('Student')->find('count',array('recursive'=>-1,'conditions'=>array('Student.gender'=>'female')));

				$admissionYear =$this->AcademicYear->get_academicYearBegainingDate($this->AcademicYear->current_academicyear());
			  $total_new_students=ClassRegistry::init('Student')->find('count',
		array('conditions'=>array('Student.admissionyear >='=>$admissionYear),'recursive'=>-1));
			   $total_graduate_new =ClassRegistry::init('GraduateList')->find('count',
		array('conditions'=>array('GraduateList.graduate_date >='=>date('Y-m-d')),'recursive'=>-1));
			  	$total_graduate_students=ClassRegistry::init('GraduateList')->find('count',
		array('recursive'=>-1));

		     $total_course_instructor_assignment_current_ac=
		     ClassRegistry::init('CourseInstructorAssignment')->find('count',
		     array('recursive'=>-1,
		     	'conditions'=>array(
		     		'CourseInstructorAssignment.semester'=>$currentSemester,
'CourseInstructorAssignment.published_course_id in (select ps.id from published_courses as ps where ps.drop=0)',
'CourseInstructorAssignment.published_course_id in (select id from published_courses where id!=0 )',
'CourseInstructorAssignment.isprimary'=>1,

'CourseInstructorAssignment.academic_year'=> $this->AcademicYear->current_academicyear())));
			 $continousCreated= ClassRegistry::init('CourseInstructorAssignment')->find('all',
		     array('contain'=>array('PublishedCourse'=>array('GivenByDepartment','Course','CourseInstructorAssignment'=>array('Staff'=>array('Department')))),'conditions'=>array(
'CourseInstructorAssignment.semester'=>$currentSemester,
'CourseInstructorAssignment.published_course_id in (select ps.id from published_courses as ps where ps.drop=0)',
'CourseInstructorAssignment.isprimary'=>1,

'CourseInstructorAssignment.academic_year'=> $this->AcademicYear->current_academicyear())));
		    $instructors=array();

			$charterDatas=ClassRegistry::init('AcademicCalendar')->semesterStartAndEndMonth($currentSemester,$currentAcademicYear);

			foreach($continousCreated as $k=>$v) {
					 $r= ClassRegistry::init('ExamType')->getExamType(
	$v['CourseInstructorAssignment']['published_course_id']);
					 if(!empty($r)){
						$instructors[$v['Staff']['Department']['name'].'~'.$v['Staff']['full_name'].'~'.$v['PublishedCourse']['Course']['course_title']]=count($r);
						  foreach($r as $kk=>$vv){
                               $time=strtotime($vv['ExamType']['created']);
							   $month=date("M",$time);
							   $charterDatas[$month]+=1;
						  }
					  }
			  }

			 $total_instructors_created_cont_assessement=count($instructors);
			 $total_instructor_active=ClassRegistry::init('User')->find('count',
		     array('recursive'=>-1,'conditions'=>array('User.active'=>1,
'User.role_id'=>ROLE_INSTRUCTOR)));
                $chartData = array_values($charterDatas);

                $chartName = 'Column Chart';

                $mychart = $this->Highcharts->create($chartName, 'column');

                $this->Highcharts->setChartParams($chartName, array(
                    'renderTo' => 'columnwrapper', // div to display chart inside
                    'chartWidth' => 700,
                    'chartHeight' => 300,

                    'title' => 'Continous Assessement Summary of '.$currentAcademicYear.'-'.$currentSemester,
                    'subtitle' => 'Source: SMiS',
                    'xAxisLabelsEnabled' => TRUE,
                    'xAxisCategories' => array_keys($charterDatas),
                    'yAxisTitleText' => 'Units',
                    'enableAutoStep' => FALSE,
                    'creditsEnabled' => FALSE,
                    'chartTheme' => 'skies'
                        )
                );

                $series = $this->Highcharts->addChartSeries();
                $series->addName('SMiS')
                        ->addData($chartData);

                $mychart->addSeries($series);

                $this->set(compact('chartName'));


			$this->set(compact('total_students','total_graduate_new','total_new_students',
		'total_graduate_students','total_male_students',
'total_course_instructor_assignment_current_ac','currentAcademicYear',
'currentSemester','total_female_students','total_instructors_created_cont_assessement','total_instructor_active'));

      	}

    public function viewContinouseAssessementDetail() {

			if(isset($this->request->data['getReport'])) {
				$instructor_lists=ClassRegistry::init('ExamType')->getExamTypeReport(
				$this->request->data['ContinuousAssessment']['acadamic_year'],
				$this->request->data['ContinuousAssessment']['semester'],
				$this->request->data['ContinuousAssessment']['program_id'],
				$this->request->data['ContinuousAssessment']['program_type_id'],
				$this->request->data['ContinuousAssessment']['department_id'],
				$this->request->data['ContinuousAssessment']['gender'],
				$this->request->data['ContinuousAssessment']['year_level_id'],
				$this->request->data['ContinuousAssessment']['numberofassessement']
				);

				  $default_department_id = $this->request->data['ContinuousAssessment']['department_id'];
				  $default_program_id = $this->request->data['ContinuousAssessment']['program_id'];
				  $default_program_type_id = $this->request->data['ContinuousAssessment']['program_type_id'];
				  $academic_year_selected = $this->request->data['ContinuousAssessment']['acadamic_year'];
				  $program_id = $this->request->data['ContinuousAssessment']['program_id'];
				  $program_type_id = $this->request->data['ContinuousAssessment']['program_type_id'];
				$this->set(compact('instructor_lists'));
			  }

		/*
           $report_type_options = array('enroll_statistics' => 'Enroll Statistics', 'attrition_rate' => 'Attrition Rate', 'grade_change_statistics' => 'Grade Change Statistics', 'eligible_student_registration_list' => 'Eligible Student Registration List', 'dismissed_student_list' => 'Dismissed Student List','top_students'=>'Top Students');
	*/


	  $programs = ClassRegistry::init('Program')->find('list');
	  $program_types = ClassRegistry::init('ProgramType')->find('list');
	  if($this->role_id == ROLE_DEPARTMENT || $this->role_id == ROLE_COLLEGE) {
		   if (!empty($this->department_ids) ||
		!empty($this->college_ids)) {
				 $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1,
				$this->department_ids, $this->college_ids);
		   } else{
				$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1,
				$this->department_id, $this->college_id);
		   }
	  } else {
               $departments = ClassRegistry::init('Department')->allDepartmentsByCollege3(1);
	  }

		$yearLevels =  ClassRegistry::init('YearLevel')->distinct_year_level();
		$programs = array(0 => 'All Programs') + $programs;
		$program_types = array(0 => 'All Program Types') + $program_types;
		if($this->role_id != ROLE_DEPARTMENT || $this->role_id != ROLE_COLLEGE) {
			$departments = array(0 => 'All University Students') + $departments;
		}

		$yearLevels =   array(0 => 'All Year Level') + $yearLevels;

		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;
		    $default_year_level_id=null;
		$default_year_level_id=null;
		$default_region_id = null;
		$graph_type=array(
		'line_chart'=>'Line Chart',
		'bar_chart'=>'Bar Chart');

		 $this->set(compact('departments','regions','graph_type','default_region_id','program_types',
	'programs','default_program_type_id','graph_type','student_lists','default_program_id',
	'default_department_id','default_year_level_id','yearLevels'));

		}

		public function viewContinouseAssessementSetup($pid) {
			$continouseExamSetup=ClassRegistry::init('ExamType')->getExamType($pid);
			$total_registered=ClassRegistry::init('CourseRegistration')->find('count',
array('conditions'=>array('CourseRegistration.published_course_id'=>$pid)));
			$total_added=ClassRegistry::init('CourseAdd')->find('count',array('conditions'=>array('CourseAdd.published_course_id'=>$pid,
	'CourseAdd.registrar_confirmation'=>1,
	'CourseAdd.department_approval'=>1)));
			$total_registered+=$total_added;
			$this->set(compact('continouseExamSetup','total_registered'));
		}


    }
?>
