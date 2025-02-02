<?php
class ReportsController extends AppController
{
	public $name = 'Reports';
	public $uses = array();

	public $menuOptions = array(
		//'parent' => 'registrations',
		'alias' => array(
			'index' => 'Reports Dashboard',
			'general_report' => 'General Reports',
			'stakeholder_report' => 'Stakeholder Reports',
		),
		'exclude' => array(
			'index'
		),
	);

	public $helpers = array('Xls', 'Media.Media');
	public $components = array('EthiopicDateTime', 'AcademicYear', 'Email');

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow(
			//'stakeholder_report'
		);
	}

	function __init_search_report()
	{
		if (!empty($this->request->data['Report'])) {
			$this->Session->write('search_report', $this->request->data['Report']);
		} else if ($this->Session->check('search_report')) {
			$this->request->data['Report'] = $this->Session->read('search_report');
		}
	}

	function __init_clear_session_filters($data = null)
	{
		if ($this->Session->check('search_report')) {
			$this->Session->delete('search_report');
		}
		//return $this->redirect(array('action' => 'index', $data));
	}

	public function beforeRender()
	{
		parent::beforeRender();

		if ($this->Session->check('Auth.User')) {
			if ($this->Session->read('Auth.User')['id'] == $this->Session->read('users_relation')['User']['id'] && $this->role_id == $this->Session->read('Auth.User')['role_id'])  {
				// nothing
			} else {
				$this->Session->destroy();
				return $this->redirect($this->Auth->logout());
			}
		} else {
			$this->Session->destroy();
			return $this->redirect($this->Auth->logout());
		}

		//$acyear_array_data = $this->AcademicYear->acyear_array();
		//$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 8, date('Y'));

		$current_academicyear = $this->AcademicYear->current_academicyear();
		$acyear_array_data = $this->AcademicYear->academicYearInArray(((explode('/', $current_academicyear)[0]) - (ACY_BACK_FOR_ALL + 2)), (explode('/', $current_academicyear)[0]));
		
		$current_acy_and_semester = $this->AcademicYear->current_acy_and_semester(); 
		$defaultsemester = $current_acy_and_semester['semester'];
		$defaultacademicyear = $current_acy_and_semester['academic_year'];

		$programs =  ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
		$program_types = $programTypes =  ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1)));

		$yearLevels = $this->year_levels;

		if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_id, array(), 1);
			$yearLevels = array(0 => 'All Year Levels') + $yearLevels;
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
	
			if ($this->Session->read('Auth.User')['is_admin'] == 0 || $this->onlyPre) {
				$departments = ClassRegistry::init('Department')->onlyFreshmanInAllColleges($this->college_id, 1);
				$programs = array(0 => 'All Assigend Programs') + $programs;
				$programTypes = $program_types = array(0 => 'All Assigned Program Types') + $program_types;
				$yearLevels = array('' => 'Pre/Fresh/Remedial');
			} else {
				debug($this->department_ids);
				$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, $this->department_ids, $this->college_id, 1);
				$programs = array(0 => 'All Programs') + $programs;
				$programTypes = $program_types = array(0 => 'All Program Types') + $program_types;
				$yearLevels = array(0 => 'All Year Levels') /* + array('pre' => 'Pre/Fresh/Remedial') */ + $yearLevels;
			}
			
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {

			if ($this->onlyPre) {
				$departments = ClassRegistry::init('Department')->onlyFreshmanInAllColleges($this->college_ids, 1);
				$yearLevels = array('' => 'Pre/Fresh/Remedial');
			} else {

				if (isset($this->department_ids) && !empty($this->department_ids)) {
					//$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_ids, array(), 1);
					$coll_ids =  ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $this->department_ids), 'fields' => array('Department.college_id', 'Department.college_id'), 'group' => array('Department.college_id')));
					debug($coll_ids);
					$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, $this->department_ids, $coll_ids, 1);
					$yearLevels = array(0 => 'All Year Levels') + $yearLevels;
				} else if (isset($this->college_ids) && !empty($this->college_ids)) {
					$departments = ClassRegistry::init('Department')->onlyFreshmanInAllColleges($this->college_ids, 1);
					//$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, array(), $this->college_ids, 1);
					$yearLevels = array(0 => 'All Year Levels') /* + array('pre' => 'Pre/Fresh/Remedial') */ + $yearLevels;
				}

				$programs = array(0 => 'All Assigend Programs') + $programs;
				$programTypes = $program_types = array(0 => 'All Assigned Program Types') + $program_types;
				
			}
			
		} else {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, array(), array(), 1);
			$departments = array(0 => 'All ' . (Configure::read('CompanyName')). ' Students') + $departments;
			$programs = array(0 => 'All Programs') + $programs;
			$programTypes = $program_types = array(0 => 'All Program Types') + $program_types;
			$yearLevels =   array(0 => 'All Year Levels') /* + array('pre' => 'Pre/Fresh/Remedial') */ + $yearLevels;
		}

		$this->set(compact('acyear_array_data', 'defaultacademicyear', 'defaultsemester', 'program_types', 'programTypes', 'programs', 'yearLevels', 'departments'));

	}

	public function index()
	{
		$statArray = array();
		$currentSemester = ClassRegistry::init('AcademicCalendar')->currentSemesterInTheDefinedAcademicCalender($this->AcademicYear->current_academicyear());
		$currentAcademicYear = $this->AcademicYear->current_academicyear();
		$admissionYear = $this->AcademicYear->get_academicYearBegainingDate($this->AcademicYear->current_academicyear());

		//Total student status
		$statArray['Student']['total'] = ClassRegistry::init('Student')->find('count', array('recursive' => -1));
		$statArray['Student']['total_male'] = ClassRegistry::init('Student')->find('count', array('recursive' => -1, 'conditions' => array('Student.gender' => 'male')));
		$statArray['Student']['total_female'] = ClassRegistry::init('Student')->find('count', array('recursive' => -1, 'conditions' => array('Student.gender' => 'female')));
		$statArray['Student']['total_new_female'] = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.admissionyear >=' => $admissionYear, 'Student.gender' => 'female'), 'recursive' => -1));
		$statArray['Student']['total_new_male'] = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.admissionyear >=' => $admissionYear, 'Student.gender' => 'male'), 'recursive' => -1));
		$statArray['Student']['total_new'] = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.admissionyear >=' => $admissionYear),'recursive' => -1));
		$statArray['Student']['total_graduate_overall'] = ClassRegistry::init('GraduateList')->find('count', array('recursive' => -1));
		$statArray['Student']['total_graduate_new'] = ClassRegistry::init('GraduateList')->find('count', array('conditions' => array('GraduateList.graduate_date >=' => $admissionYear), 'recursive' => -1));
		$statArray['Student']['total_graduate_new_female'] = ClassRegistry::init('GraduateList')->find('count', array('conditions' => array('GraduateList.graduate_date >=' => date('Y-m-d'), 'GraduateList.student_id in (select id from students where gender="female") '), 'recursive' => -1));;
		$statArray['Student']['total_graduate_new_male'] = ClassRegistry::init('GraduateList')->find('count', array('conditions' => array('GraduateList.graduate_date >=' => date('Y-m-d'), 'GraduateList.student_id in (select id from students where gender="male") '), 'recursive' => -1));

		//Registration  and dismissal stat
		$statArray['Registration']['total_registration'] = ClassRegistry::init('CourseRegistration')->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $currentAcademicYear, 
				'CourseRegistration.semester' => $currentSemester
			),
			'group' => 'CourseRegistration.student_id',
			'recursive' => -1
		));

		$statArray['Registration']['total_registration_female'] = ClassRegistry::init('CourseRegistration')->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $currentAcademicYear, 
				'CourseRegistration.semester' => $currentSemester,
				'CourseRegistration.student_id in (select id from students where gender="female")'
			),
			'group' => 'CourseRegistration.student_id',
			'recursive' => -1
		));

		$statArray['Registration']['total_registration_male'] = ClassRegistry::init('CourseRegistration')->find('count', array(
			'conditions' => array(
				'CourseRegistration.academic_year' => $currentAcademicYear, 'CourseRegistration.semester' => $currentSemester,
				'CourseRegistration.student_id in (select id from students where gender="male")'
			), 
			'group' => 'CourseRegistration.student_id', 
			'recursive' => -1
		));

		$statArray['Registration']['total_active_student_in_section'] = ClassRegistry::init('StudentsSection')->find('count', array(
			'conditions' => array(
				'StudentsSection.archive' => 0,
				'StudentsSection.section_id in (select section_id from published_courses where semester="' . $currentSemester . '" and academic_year="' . $currentAcademicYear . '") ',
			),
			'recursive' => -1
		));

		$statArray['Registration']['total_active_female_in_section'] = ClassRegistry::init('StudentsSection')->find('count', array(
			'conditions' => array(
				'StudentsSection.archive' => 0,
				'StudentsSection.student_id not in (select id from students where gender="female")',
				'StudentsSection.section_id in (select section_id from published_courses where semester="' . $currentSemester . '" and academic_year="' . $currentAcademicYear . '")'
			), 
			'group' => 'StudentsSection.student_id',
			'recursive' => -1
		));

		$statArray['Registration']['total_active_male_in_section'] = ClassRegistry::init('StudentsSection')->find('count', array(
			'conditions' => array(
				'StudentsSection.archive' => 0,
				'StudentsSection.student_id not in (select id from students where gender="male")',
				'StudentsSection.section_id in (select section_id from published_courses where semester="' . $currentSemester . '" and academic_year="' . $currentAcademicYear . '") ',
			), 
			'group' => 'StudentsSection.student_id', 
			'recursive' => -1
		));

		$statArray['Registration']['dismissalStat'] = ClassRegistry::init('StudentExamStatus')->getNumberOfDismissedStudent($currentAcademicYear, $currentSemester);

		$gradeSubmissionDelay = ClassRegistry::init('CourseInstructorAssignment')->getGradeSubmissionStatNumber($currentAcademicYear, $currentSemester);
		debug($gradeSubmissionDelay);
		//debug($statArray);
		$this->set(compact('currentSemester', 'gradeSubmissionDelay', 'statArray', 'currentAcademicYear'));
	}

	public function general_report()
	{

		if (isset($this->request->data['getReport']) || isset($this->request->data['getReportExcel'])) {

			$this->__init_clear_session_filters();

			$programID = $this->request->data['Report']['program_id'];
			$programTypeID = $this->request->data['Report']['program_type_id'];
			$only_freshman = $this->request->data['Report']['freshman'];

			$program_ids = array();
			$program_type_ids = array();

			if (is_numeric($programID)) {
				if ($programID == 0) {
					$program_ids = $this->program_ids;
				} else {
					$program_ids = [$programID => $programID];
				}
			}

			if (is_numeric($programTypeID)) {
				if ($programTypeID == 0) {
					$program_type_ids = $this->program_type_ids;
				} else {
					$program_type_ids = [$programTypeID => $programTypeID];
				}
			}

			if ($this->request->data['Report']['freshman'] || $this->onlyPre) {
				$only_freshman = $this->request->data['Report']['freshman'] = 1;
				$this->request->data['Report']['year_level_id'] = '';
			} else if (!isset($this->request->data['Report']['year_level_id'])) {
				$this->request->data['Report']['year_level_id'] = '';
			}

			$this->__init_search_report();

			if ($this->request->data['Report']['report_type'] == 'attrition_rate') {
				
				$attrationRateAndYearLevel = ClassRegistry::init('Student')->findAttrationRate(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['freshman']
				);
				
				$years = $this->__years($this->request->data['Report']['department_id']);

				$attrationRate = $attrationRateAndYearLevel;

				$headerLabel = $this->__label('Attrition Rate', $this->request->data);
				//$showFromToBlock = true;

				$this->set(compact('attrationRate', 'years', 'headerLabel'));
				
				if ($this->request->data['Report']['report_type'] == 'attrition_rate' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Attrition Rate', $this->request->data);
					$this->set(compact('attrationRate', 'years', 'filename'));
					$this->render('/Elements/reports/xls/attration_rate_stats_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'admittedMoreThanOneProgram') {

				$admittedMoreThanOneProgram = ClassRegistry::init('Student')->admittedMoreThanOneProgram($this->request->data['Report']['department_id']);
				$showFromToBlock = true;
				$headerLabel = $this->__label('Students Admitted in Multiple Programs', $this->request->data);

				$this->set(compact('admittedMoreThanOneProgram', 'years', 'showFromToBlock', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'admittedMoreThanOneProgram' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Students Admitted in Multiple Programs', $this->request->data);
					$this->set(compact('admittedMoreThanOneProgram', 'filename'));
					$this->render('/Elements/reports/xls/admitted_in_multiple_program_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'top_students') {

				$top = ClassRegistry::init('StudentExamStatus')->getTopScorer(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['top'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['gpa'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);

				$top_prefix = (isset($this->request->data['Report']['top']) && $this->request->data['Report']['top'] ? 'Top ' . $this->request->data['Report']['top'] . ' Students List for ' :  'Top 10 Students List for ');

				$headerLabel = $this->__label($top_prefix, $this->request->data);
				$showFromToBlock = true;

				$this->set(compact('top', 'showFromToBlock', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'top_students' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name($top_prefix, $this->request->data);
					$this->set(compact('top', 'filename'));
					$this->render('/Elements/reports/xls/top_student_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'dismissed_student_list') {

				$dismissedList = ClassRegistry::init('StudentExamStatus')->getDismissedStudent(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Academically Dismissed Students List', $this->request->data);
				$showFromToBlock = true;
				$academicStatus = classRegistry::init('AcademicStatus')->find('list');

				$this->set(compact('dismissedList', 'showFromToBlock', 'headerLabel', 'academicStatus'));
				if ($this->request->data['Report']['report_type'] == 'dismissed_student_list' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Academically Dismissed Students List', $this->request->data);
					$this->set(compact('dismissedList', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/dismissed_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'notRegisteredList') {

				$notRegisteredList = ClassRegistry::init('Student')->getNotRegisteredList(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);

				$headerLabel = $this->__label('Not Registered Students List', $this->request->data);
				$showFromToBlock = true;

				$academicStatus = classRegistry::init('AcademicStatus')->find('list');
				$this->set(compact('notRegisteredList', 'showFromToBlock', 'headerLabel', 'academicStatus'));

				if ($this->request->data['Report']['report_type'] == 'notRegisteredList' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Not Registered Students List', $this->request->data);
					$this->set(compact('notRegisteredList', 'filename', 'showFromToBlock', 'headerLabel'));
					$this->render('/Elements/reports/xls/not_registered_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'registeredList') {

				$registeredList = ClassRegistry::init('StudentExamStatus')->getRegisteredStudentList(
				//$registeredList = ClassRegistry::init('Student')->getRegisteredStudentList(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);

				$headerLabel = $this->__label('Registered Student List', $this->request->data);
				$showFromToBlock = true;

				$this->set(compact('registeredList', 'showFromToBlock', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'registeredList' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Registered Student List', $this->request->data);
					$this->set(compact('registeredList', 'filename', 'showFromToBlock', 'headerLabel'));
					$this->render('/Elements/reports/xls/registered_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'active_student_list') {
				
				$activeList = ClassRegistry::init('StudentExamStatus')->getActiveStudent(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);

				$headerLabel = $this->__label('Active Students List', $this->request->data);

				$showFromToBlock = true;
				$academicStatus = classRegistry::init('AcademicStatus')->find('list');
				$this->set(compact('activeList', 'showFromToBlock', 'headerLabel', 'academicStatus'));

				if ($this->request->data['Report']['report_type'] == 'active_student_list' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Active Students List', $this->request->data);
					$this->set(compact('activeList', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/active_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'grade_change_statistics') {

				$gradeChangeStat = ClassRegistry::init('ExamGradeChange')->getInstGradeChangeStat(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id']
				);

				$headerLabel = $this->__label('Grade Change Statistics', $this->request->data);

				$showFromToBlock = true;
				$this->set(compact('gradeChangeStat', 'showFromToBlock', 'headerLabel'));

			} else if ($this->request->data['Report']['report_type'] == 'lateGradeSubmission') {
				
				$gradeSubmissionDelay = ClassRegistry::init('StudentExamStatus')->getNotGradeSubmittedList(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Not Grade Submitted Instructor List', $this->request->data);
				$showFromToBlock = true;

				$this->set(compact('gradeSubmissionDelay', 'headerLabel', 'showFromToBlock'));

				if ($this->request->data['Report']['report_type'] == 'lateGradeSubmission' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Not Grade Submitted Instructor List', $this->request->data);
					$this->set(compact('gradeSubmissionDelay', 'filename'));
					$this->render('/Elements/reports/xls/grade_submission_delay_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'delayedCountGradeSubmissionList') {

				$delayedGradeSubmissionReportList = ClassRegistry::init('StudentExamStatus')->getDelayedGradeSubmissionList(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Late Grade Submitted Instructor List', $this->request->data);
				$showFromToBlock = true;

				$this->set(compact('delayedGradeSubmissionReportList', 'headerLabel', 'showFromToBlock'));

				if ($this->request->data['Report']['report_type'] == 'lateGradeSubmission' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Late Grade Submitted Instructor List', $this->request->data);
					$this->set(compact('delayedGradeSubmissionReportList', 'headerLabel', 'filename'));
					$this->render('/Elements/reports/xls/grade_submission_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'gradeSubmittedInstructorList') {

				$gradeSubmissionDelay = ClassRegistry::init('StudentExamStatus')->getGradeSubmittedInstructorList(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['freshman']
				);
				//debug($gradeSubmissionDelay);

				$headerLabel = $this->__label('Grade Submitted Instructor List', $this->request->data);
				$showFromToBlock = true;

				$this->set(compact('gradeSubmissionDelay', 'headerLabel', 'showFromToBlock'));

				if ($this->request->data['Report']['report_type'] == 'gradeSubmittedInstructorList' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Grade Submitted Instructor List', $this->request->data);
					$this->set(compact('gradeSubmissionDelay', 'headerLabel', 'filename'));
					$this->render('/Elements/reports/xls/grade_submission_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'notAssignedCourseeList') {

				$notAssignedCourseeList = ClassRegistry::init('CourseInstructorAssignment')->getCourseNotAssigned(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Not Assigned Course List', $this->request->data);
				$showFromToBlock = true;

				$this->set(compact('notAssignedCourseeList', 'headerLabel', 'showFromToBlock'));

				if ($this->request->data['Report']['report_type'] == 'notAssignedCourseeList' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Not Assigned Course List', $this->request->data);
					$this->set(compact('notAssignedCourseeList', 'headerLabel', 'filename'));
					$this->render('/Elements/reports/xls/course_not_assigned_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'getGradeChangeList') {

				$gradeChangeLists = ClassRegistry::init('StudentExamStatus')->getGradeChangeList(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Grade Change List', $this->request->data);

				$showFromToBlock = true;
				$this->set(compact('gradeChangeLists', 'headerLabel', 'showFromToBlock'));

				if ($this->request->data['Report']['report_type'] == 'getGradeChangeList' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Grade Change List', $this->request->data);
					$this->set(compact('gradeChangeLists', 'headerLabel', 'filename'));
					$this->render('/Elements/reports/xls/grade_change_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'graduated') {
				
				$graduated = ClassRegistry::init('StudentExamStatus')->getGraduatingStudent(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['region_id']
				);

				$showFromToBlock = true;

				$headerLabel = $this->__label('Graduate List', $this->request->data);

				$this->set(compact('graduated', 'showFromToBlock', 'headerLabel'));

			} else if ($this->request->data['Report']['report_type'] == 'graduatedRateCompareToEntry') {

				$graduateRateToEntry = ClassRegistry::init('StudentExamStatus')->getGraduatingRateToEntryStudent(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['region_id']
				);

				$headerLabel = $this->__label('Graduate To Entry Rate', $this->request->data);

				$showFromToBlock = true;
				$this->set(compact('graduateRateToEntry', 'showFromToBlock', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'graduatedRateCompareToEntry' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Graduate To Entry Rate', $this->request->data);
					$this->set(compact('graduateRateToEntry', 'showFromToBlock', 'filename'));
					$this->render('/Elements/reports/xls/grade_change_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'academic_status_range') {
				
				$resultBy = ClassRegistry::init('StudentExamStatus')->getStudentByResult(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['from'],
					$this->request->data['Report']['to'],
					$this->request->data['Report']['academic_status_id'],
					$this->request->data['Report']['type'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Student List By Result Range', $this->request->data);

				$showFromToBlock = true;
				$this->set(compact('showFromToBlock', 'resultBy', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'academic_status_range' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student List By Result Range', $this->request->data);
					$this->set(compact('resultBy', 'filename'));
					$this->render('/Elements/reports/xls/result_range_list_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'distributionStatsGender') {

				$deptID = $this->request->data['Report']['department_id'];

				$department_ids = array();
				$college_ids = array();

				if (is_numeric($deptID)) {
					if ($deptID == 0) {
						$department_ids = $this->department_ids;
						$years = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role($this->Session->read('Auth.User')['role_id'], null, $department_ids , $this->program_ids);
					} else {
						$department_ids = [$deptID => $deptID];
						$years = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(ROLE_DEPARTMENT , null, $department_ids , $this->program_ids);
					}
				} else {
					$college_id = explode('~', $deptID);
					if (count($college_id) > 1) {
						$college_ids[$college_id[1]] = $college_id[1];
						$years = ClassRegistry::init('YearLevel')->distinct_year_level_based_on_role(ROLE_COLLEGE , $college_ids, null , $this->program_ids);
					} 
				}

				$distributionStatistics = ClassRegistry::init('Student')->getDistributionStats(
					$this->request->data['Report']['acadamic_year'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$only_freshman,
					1,
					$this->request->data['Report']['semester']
				);

				$showFromToBlock = true;

				$headerLabel = $this->__label('Distribution Statistics By Gender', $this->request->data);

				debug($distributionStatistics);
				$this->set(compact('distributionStatistics', 'showFromToBlock', 'years', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'distributionStatsGender' && isset($this->request->data['getReportExcel']) ) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Distribution Statistics By Gender', $this->request->data);
					$this->set(compact('distributionStatistics', 'filename', 'years', 'headerLabel'));
					$this->render('/Elements/reports/xls/distribution_gender_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'distributionStatsLetterGrade') {
				
				$distributionStatsLetterGrade = ClassRegistry::init('Student')->distributionStatsLetterGrade(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Distribution Statistics of Letter Grade', $this->request->data);

				$letterGrades = ClassRegistry::init('Grade')->find('list', array('fields' => array('Grade.grade', 'Grade.grade'), 'order' => array('Grade.grade ASC')));
				$this->set(compact('distributionStatsLetterGrade', 'letterGrades', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'distributionStatsLetterGrade' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Distribution Statistics of Letter Grade', $this->request->data);
					$this->set(compact('distributionStatsLetterGrade', 'letterGrades', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/distribution_stats_letter_grade_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'listFx') {

				$studentList = ClassRegistry::init('Student')->listStudentByLetterGrade(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					"Fx",
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Students List with Fx Grade', $this->request->data);

				$grade_selected = 'Fx';

				$this->set(compact('studentList', 'headerLabel', 'grade_selected'));

				if ($this->request->data['Report']['report_type'] == 'listFx' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Students List with Fx Grade', $this->request->data);
					$this->set(compact('studentList', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/list_bygrade_student_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'listF') {

				$studentList = ClassRegistry::init('Student')->listStudentByLetterGrade(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					"F",
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Students List with F Grade', $this->request->data);

				$grade_selected = 'F';

				$this->set(compact('studentList', 'headerLabel', 'grade_selected'));

				if ($this->request->data['Report']['report_type'] == 'listF' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Students List with F Grade', $this->request->data);
					$this->set(compact('studentList', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/list_bygrade_student_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'listNG') {

				$studentList = ClassRegistry::init('Student')->listStudentByLetterGrade(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					"NG",
					$this->request->data['Report']['freshman']
				);

				$headerLabel = $this->__label('Students with NG Grade', $this->request->data);

				$grade_selected = 'NG';

				$this->set(compact('studentList', 'headerLabel', 'grade_selected'));

				if ( $this->request->data['Report']['report_type'] == 'listNG' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Students with NG Grade', $this->request->data);
					$this->set(compact('studentList', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/list_bygrade_student_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'distributionStatsGenderAndRegion') {
				
				$distributionStatistics = ClassRegistry::init('Student')->getDistributionStatsOfRegion(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman']

				);

				debug($distributionStatistics);
				$showFromToBlock = true;

				$headerLabel = $this->__label('Distribution Statistics By Region', $this->request->data);

				$years = $this->__years($this->request->data['Report']['department_id']);
				$this->set(compact('distributionStatistics', 'showFromToBlock', 'years', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'distributionStatsGenderAndRegion' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Distribution Statistics By Region', $this->request->data);
					$this->set(compact('distributionStatistics', 'filename', 'years', 'headerLabel'));
					$this->render('/Elements/reports/xls/distribution_region_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'distributionStatsStatus') {
				
				$distributionStatisticsStatus = ClassRegistry::init('Student')->getDistributionStatsOfStatus(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman']
				);

				$years = $this->__years($this->request->data['Report']['department_id']);
				$academicStatus = ClassRegistry::init('AcademicStatus')->find('list');

				$headerLabel = $this->__label('Distribution Statistics By Academic Status', $this->request->data);

				$years = $this->__years($this->request->data['Report']['department_id']);
				$this->set(compact('distributionStatisticsStatus', 'showFromToBlock', 'academicStatus', 'years', 'headerLabel'));

				if ( $this->request->data['Report']['report_type'] == 'distributionStatsStatus' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Distribution Statistics By Academic Status', $this->request->data);
					$this->set(compact('distributionStatisticsStatus', 'academicStatus', 'years', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/distribution_status_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'distributionStatsGraduate') {
				
				$distributionStatsGraduate = ClassRegistry::init('Student')->getDistributionStatsOfGraduate(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['region_id']
				);

				$headerLabel = $this->__label('Distribution Statistics of Graduates', $this->request->data);

				$academicStatus = ClassRegistry::init('AcademicStatus')->find('list');
				$this->set(compact('distributionStatsGraduate', 'showFromToBlock', 'academicStatus', 'years', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'distributionStatsGraduate' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Distribution Statistics of Graduates', $this->request->data);
					$this->set(compact('distributionStatsGraduate', 'academicStatus', 'filename', 'headerLabel' ));
					$this->render('/Elements/reports/xls/distribution_graduate_xls');
					return;
				}

			}  else if ($this->request->data['Report']['report_type'] == 'get_student_results_for_hemis') {

				$studentResultsHEMIS = ClassRegistry::init('StudentExamStatus')->getStudentResultsForHemis(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated'],
					$this->request->data['Report']['only_with_complete_data']
				);

				$headerLabel = $this->__label('Student Results for HEMIS', $this->request->data);

				$this->set(compact('studentResultsHEMIS', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'get_student_results_for_hemis' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student Results for HEMIS', $this->request->data);
					$this->set(compact('studentResultsHEMIS', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/get_student_results_for_hemis_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'get_student_graduate_for_hemis') {

				$this->request->data['Report']['year_level'] = '';
				
				$studentGraduateHEMIS = ClassRegistry::init('StudentExamStatus')->getStudentGraduateForHemis(
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['only_with_complete_data']
				);

				$headerLabel = $this->__label('Student Graduate for HEMIS', $this->request->data);

				$this->set(compact('studentGraduateHEMIS', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'get_student_graduate_for_hemis' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student Graduate for HEMIS', $this->request->data);
					$this->set(compact('studentGraduateHEMIS', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/get_student_graduate_for_hemis_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'get_student_enrolment_for_hemis') {

				if (!empty($this->request->data['Report']['program_id'])) {
					$progIDs[$this->request->data['Report']['program_id']] = $this->request->data['Report']['program_id'];
				} else if (!empty($program_ids)) {
					$progIDs = $program_ids;
				} else {
					$progIDs = array();
					$progIDs[0] = '0';
				}

				if (!empty($this->request->data['Report']['program_type_id'])) {
					$progTypeIDs[$this->request->data['Report']['program_type_id']] = $this->request->data['Report']['program_type_id'];
				} else if (!empty($program_type_ids)) {
					$progTypeIDs = $program_type_ids;
				} else {
					$progTypeIDs = array();
					$progTypeIDs[0] = '0';
				}
				
				$studentEnrolmentHEMIS = ClassRegistry::init('StudentExamStatus')->getStudentEnrolmentForHemis(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$progIDs,
					$progTypeIDs,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated'],
					$this->request->data['Report']['only_with_complete_data']
				);

				$headerLabel = $this->__label('Student Enrollment for HEMIS', $this->request->data);
				$this->set(compact('studentEnrolmentHEMIS', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'get_student_enrolment_for_hemis' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student Enrollment for HEMIS', $this->request->data);
					$this->set(compact('studentEnrolmentHEMIS', 'filename', 'headerLabel'));
					$this->render('/Elements/reports/xls/get_student_enrolment_for_hemis_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'get_student_list_for_exit_exam') {

				$this->request->data['Report']['freshman'] = 0;
				$this->request->data['Report']['top'] = 5;
				$this->request->data['Report']['exclude_graduated'] = 1;

				$studentListForExitExam = ClassRegistry::init('StudentExamStatus')->getTopScorer(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['top'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['gpa'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);

				$headerLabel = $this->__label('Student List For Exit Exam', $this->request->data);
				$this->set(compact('studentListForExitExam', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'get_student_list_for_exit_exam' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student List For Exit Exam', $this->request->data);
					$this->set(compact('studentListForExitExam', 'filename'));
					$this->render('/Elements/reports/xls/get_student_list_for_exit_exam_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'studentListForOffice') {

				if (isset($this->request->data['Report']['freshman']) && $this->request->data['Report']['freshman'] == 1){
					$this->request->data['Report']['exclude_graduated'] = 0;
				}

				$studentListForOffice = ClassRegistry::init('StudentExamStatus')->getStudentListForOffice(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);
				
				//debug($studentListForOffice);

				$headerLabel = $this->__label('Student List for Office Import', $this->request->data);
				$showFromToBlock = true;
				$semester = $this->request->data['Report']['semester'];

				$this->set(compact('studentListForOffice', 'semester', 'showFromToBlock', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'studentListForOffice' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student List for Office Import', $this->request->data);
					$this->set(compact('studentListForOffice', 'filename', 'showFromToBlock', 'headerLabel'));
					$this->render('/Elements/reports/xls/student_list_for_office_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'studentListForElearning') {

				if (isset($this->request->data['Report']['freshman']) && $this->request->data['Report']['freshman'] == 1){
					$this->request->data['Report']['exclude_graduated'] = 0;
				}

				$studentListForElearning = ClassRegistry::init('StudentExamStatus')->getStudentListForOffice(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$program_ids,
					$program_type_ids,
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender'],
					$this->request->data['Report']['year_level_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['freshman'],
					$this->request->data['Report']['exclude_graduated']
				);

				debug($this->request->data);
				
				//debug($studentListForElearning);

				$headerLabel = $this->__label('Student List for Elearning Import', $this->request->data);
				$showFromToBlock = true;

				$semester = $this->request->data['Report']['semester'];
				$this->set(compact('studentListForElearning', 'semester', 'showFromToBlock', 'headerLabel'));

				if ($this->request->data['Report']['report_type'] == 'studentListForElearning' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Student List for Elearning Import', $this->request->data);
					$this->set(compact('studentListForElearning', 'filename', 'showFromToBlock', 'headerLabel'));
					$this->render('/Elements/reports/xls/student_list_for_elearning_xls');
					return;
				}
			}

			/* $default_department_id = $this->request->data['Report']['department_id'];
			$default_program_id = $this->request->data['Report']['program_id'];
			$default_program_type_id = $this->request->data['Report']['program_type_id'];
			$academic_year_selected = $this->request->data['Report']['acadamic_year'];
			$program_id = $this->request->data['Report']['program_id'];
			$program_type_id = $this->request->data['Report']['program_type_id'];
			$year_level_id = $this->request->data['Report']['year_level_id'];
			$freshman = $this->request->data['Report']['freshman'];
			$exclude_graduated = $this->request->data['Report']['exclude_graduated'];
			$only_with_complete_data = $this->request->data['Report']['only_with_complete_data'];
			
			if (isset($this->request->data['Report']['only_registered'])) {
				$only_registered = $this->request->data['Report']['only_registered'];
			}

			$this->__init_search_report(); */
			
		}

		$report_type_options = array(
			'Registration' => array(
				'registeredList' => 'Registered List',
				'notRegisteredList' => 'Not Registered List',
				'admittedMoreThanOneProgram' => 'Students Admitted in More Than One Program'
			),
			'Status' => array(
				'active_student_list' => 'Active Student List',
				'dismissed_student_list' => 'Dismissed Student List',
				'top_students' => 'Top Students List',
				'academic_status_range' => 'Student List By Result Range & Status',
				'attrition_rate' => 'Attrition Rate',
			),
			'Grade' => array(
				'gradeSubmittedInstructorList' => 'Submitted Grade List',
				'delayedCountGradeSubmissionList' => 'Late Grade Submitted Instructor List',
				'lateGradeSubmission' => 'Not Submitted Grade List',
				'notAssignedCourseeList' => 'Not Assigned Course List',
				'getGradeChangeList' => 'Grade Change List',
				'listFx' => 'List of Students with Fx Grade',
				'listF' => 'List of Students with F Grade',
				'listNG' => 'List of Students with NG Grade',
			),
			'HEMIS' => array(
				'get_student_enrolment_for_hemis' => 'Student Enrollment for HEMIS',
				'get_student_results_for_hemis' => 'Student Results for HEMIS',
				'get_student_graduate_for_hemis' => 'Student Graduate for HEMIS',
				'get_student_list_for_exit_exam' => 'Student List For Exit Exam',
			),
			'Distribution' => array(
				'distributionStatsGender' => 'Distribution Statistics By Gender',
				'distributionStatsGenderAndRegion' => 'Distribution Statistics By Region',
				//'distributionStatsGrade'=>'Distribution Statistics Grade',
				'distributionStatsStatus' => 'Distribution Statistics By Status',
				//'grade_change_statistics' => 'Distribution Statistics Grade Change',
				'distributionStatsGraduate' => 'Distribution Statistics Graduated',
				'distributionStatsLetterGrade' => 'Distribution Statistics Letter Grade',
				'graduatedRateCompareToEntry' => 'Distribution Statistics of Graduate With Entry',
				'enrollStatistics' => 'Enrollment Statistics',
			),
			'E-Learning and Office 365 Account' => array(
				'studentListForOffice' => 'Student List for Office 365 Import',
				'studentListForElearning' => 'Student List for Elearning Import',
			),
		);

		$regions = ClassRegistry::init('Region')->find('list');
		$academicStatuses = ClassRegistry::init('AcademicStatus')->find('list');

		$regions = array(0 => 'All') + $regions;

		//$default_department_id = null;
		$default_department_id = (isset($this->request->data['Report']['department_id']) ? $this->request->data['Report']['department_id'] : (isset($this->department_ids) && !empty($this->department_ids) && !$this->onlyPre ? array_values($this->department_ids)[0] : ''));
		$default_program_id = (isset($this->request->data['Report']['department_id']) ? $this->request->data['Report']['program_id'] : (isset($this->program_ids) && !empty($this->program_ids) ? array_values($this->program_ids)[0] : ''));
		$default_program_type_id = (isset($this->request->data['Report']['program_type_id']) ? $this->request->data['Report']['program_type_id'] : (isset($this->program_type_ids) && !empty($this->program_type_ids) ? array_values($this->program_type_ids)[0] : ''));
		$default_year_level_id =  (isset($this->request->data['Report']['year_level_id']) ? $this->request->data['Report']['year_level_id'] : (isset($this->year_levels) && !empty($this->year_levels) ? array_values($this->year_levels)[0] : ''));
		$default_region_id = (isset($this->request->data['Report']['region_id']) ? $this->request->data['Report']['region_id'] : 0);

		$exclude_graduated =  (isset($this->request->data['Report']['exclude_graduated']) ? $this->request->data['Report']['exclude_graduated'] : 0);
		$freshman = ($this->onlyPre == 1 ? 1 : (isset($this->request->data['Report']['freshman']) ? $this->request->data['Report']['freshman'] : 0));
		$only_with_complete_data = (isset($this->request->data['Report']['only_with_complete_data']) ? $this->request->data['Report']['only_with_complete_data'] : 0);
		$only_registered = (isset($this->request->data['Report']['only_registered']) ? $this->request->data['Report']['only_registered'] : 0);
		
		$graph_type = array('bar' => 'Bar Chart', 'line' => 'Line Chart'/* , 'pie' => 'Pie Chart' */);


		$only_pre_assigned_user = $this->onlyPre;

		if ($this->onlyPre) {
			$this->request->data['Report']['freshman'] = 1;
			$this->request->data['Report']['year_level_id'] = '';
		}

		$this->__init_clear_session_filters();
		$this->__init_search_report();
		debug($this->request->data);

		$this->set(compact(
			//'departments',
			//'program_types',
			//'programs',
			//'yearLevels',
			'regions',
			'academicStatuses',
			'graph_type',
			'default_region_id',
			'default_program_type_id',
			'student_lists',
			'default_program_id',
			'default_department_id',
			'report_type_options',
			'default_year_level_id',
			'exclude_graduated',
			'freshman',
			'only_with_complete_data',
			'only_registered',
			'only_pre_assigned_user'
		));
	}

	public function stakeholder_report()
	{

		if (isset($this->request->data['getReport']) || isset($this->request->data['getReportExcel'])) {

			$this->__init_clear_session_filters();

			$programID = $this->request->data['Report']['program_id'];
			$programTypeID = $this->request->data['Report']['program_type_id'];
			$only_freshman = (isset($this->request->data['Report']['freshman']) ? $this->request->data['Report']['freshman'] : 0);

			$program_ids = array();
			$program_type_ids = array();

			if (is_numeric($programID)) {
				if ($programID == 0) {
					$program_ids = $this->program_ids;
				} else {
					$program_ids = [$programID => $programID];
				}
			}

			if (is_numeric($programTypeID)) {
				if ($programTypeID == 0) {
					$program_type_ids = $this->program_type_ids;
				} else {
					$program_type_ids = [$programTypeID => $programTypeID];
				}
			}

			if ((isset($this->request->data['Report']['freshman']) && $this->request->data['Report']['freshman']) || $this->onlyPre) {
				$only_freshman = $this->request->data['Report']['freshman'] = 1;
				$this->request->data['Report']['year_level_id'] = '';
			} else if (!isset($this->request->data['Report']['year_level_id'])) {
				$this->request->data['Report']['year_level_id'] = '';
			}

			$this->__init_search_report();

			if ($this->request->data['Report']['report_type'] == 'currentlyActiveStudentStatistics') {
				
				$currentlyActiveStudentStatistics = ClassRegistry::init('Student')->getActiveStudentStatistics(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['gender']
				);

				$this->set(compact('currentlyActiveStudentStatistics', 'headerLabel'));

				$headerLabel = $this->__label('Active Student Statistics', $this->request->data);

		
				if ($this->request->data['Report']['report_type'] == 'currentlyActiveStudentStatistics' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = $this->__excel_file_name('Active Student Statistics', $this->request->data);
					$this->set(compact('currentlyActiveStudentStatistics', 'headerLabel', 'program_types', 'filename'));
					$this->render('/Elements/reports/xls/stakeholders/active_student_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'studentConstituencyByAgeGroup') {
				
				$studentConstituencyByAgeGroup = ClassRegistry::init('Student')->getStudentConsistencyByAgeRangeStatistics(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['semester'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['region_id'],
					$this->request->data['Report']['program_id'],
					$this->request->data['Report']['program_type_id'],
					$this->request->data['Report']['gender']
				);

				$this->set(compact('studentConstituencyByAgeGroup', 'headerLabel', 'program_types'));

				$headerLabel = $this->__label('Student Distribution By Age Group', $this->request->data);

				if ($this->request->data['Report']['report_type'] == 'studentConstituencyByAgeGroup' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Active Student Statistics -' . date('Ymd H:i:s');
					$this->set(compact('headerLabel', 'program_types', 'filename'));
					$this->render('/Elements/reports/xls/stakeholders/agegroup_student_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'activeTeachersByDegree') {

				$getActiveTeacherByDegree = ClassRegistry::init('Staff')->getActiveTeacherByDegree($this->request->data['Report']['department_id'], $this->request->data['Report']['gender']);

				//$headerLabel = 'Currently Teaching Teachers  Statistics' . $this->request->data['Report']['acadamic_year'];
				$headerLabel = $this->__label('Currently Teaching Teachers  Statistics', $this->request->data);
				
				$educations = array(
					'Doctorate' => 'PhD', 
					'Master' => 'Master',
					'Medical Doctor' => 'Medical Doctorate',
					'Degree' => 'Degree'
				);

				$this->set(compact('getActiveTeacherByDegree', 'headerLabel', 'educations'));

				if ($this->request->data['Report']['report_type'] == 'activeTeachersByDegree' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Currently Teaching Teachers  Statistics-' . date('Ymd H:i:s');
					$this->set(compact('headerLabel', 'filename', 'getActiveTeacherByDegree', 'educations'));
					$this->render('/Elements/reports/xls/stakeholders/active_teacher_degree_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'teachersOnStudyLeave') {

				$getTeachersOnStudyLeave = ClassRegistry::init('Staff')->getTeachersOnStudyLeave($this->request->data['Report']['department_id'],$this->request->data['Report']['gender']);

				//$headerLabel = 'Teachers  On Study Leave ' . $this->request->data['Report']['acadamic_year'];
				$headerLabel = $this->__label('Teachers On Study Leave', $this->request->data);

				$educations = array(
					'Doctorate' => 'PhD', 
					'Master' => 'Master',
					'Medical Doctor' => 'Medical Doctorate',
					'Degree' => 'Degree'
				);

				$this->set(compact('getTeachersOnStudyLeave', 'headerLabel', 'educations'));

				if ($this->request->data['Report']['report_type'] == 'teachersOnStudyLeave' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Teachers  On Study Leave-' . date('Ymd H:i:s');
					$this->set(compact('headerLabel', 'filename', 'getTeachersOnStudyLeave', 'educations'));
					$this->render('/Elements/reports/xls/stakeholders/teacher_on_study_leave_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'staffHDPCompletedTeachers') {

				$getStaffCompletedHDPStatistics = ClassRegistry::init('StaffStudy')->getStaffCompletedHDPStatistics(
					$this->request->data['Report']['acadamic_year'],
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender']
				);

				debug($getStaffCompletedHDPStatistics);

				//$headerLabel = 'Teachers  HDP Training Statistics' . $this->request->data['Report']['acadamic_year'];
				$headerLabel = $this->__label('Teachers HDP Training Statistics', $this->request->data);

				$completed = array('0' => 'Not Completed', '1' => 'Completed');

				$this->set(compact('getStaffCompletedHDPStatistics', 'headerLabel', 'completed'));

				if ($this->request->data['Report']['report_type'] == 'staffHDPCompletedTeachers' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Teachers  HDP Training -' . date('Ymd H:i:s');
					$this->set(compact('headerLabel', 'filename'));
					$this->render('/Elements/reports/xls/stakeholders/teacher_completed_hdp_stat_xls');
					return;
				}

			} else if ($this->request->data['Report']['report_type'] == 'activeTeachersByAcademicRank') {
				$getActiveTeacherByAcademicRank = ClassRegistry::init('Staff')->getActiveTeacherByAcademicRank(
					$this->request->data['Report']['department_id'],
					$this->request->data['Report']['gender']
				);

				//$headerLabel = 'Currently Teaching Teachers  By Academic Rank Statistics' . $this->request->data['Report']['acadamic_year'];
				$headerLabel = $this->__label('Currently Teaching Teachers By Academic Rank Statistics', $this->request->data);

				$educations = array(
					'Doctorate' => 'PhD', 
					'Master' => 'Master',
					'Medical Doctor' => 'Medical Doctorate',
					'Degree' => 'Degree'
				);

				//$positions = array('4' => 'Lecturer', '5' => 'Assistant Professor', '6' => 'Associate Professor', '7' => 'Professor');
				$positions = ClassRegistry::init('Position')->find('list', array('conditions' => array('Position.service_wing_id' => 1, 'Position.active' => 1), 'fields' => array('id', 'position')));
				$this->set(compact('getActiveTeacherByAcademicRank', 'headerLabel', 'educations', 'positions'));

				if ($this->request->data['Report']['report_type'] == 'activeTeachersByAcademicRank' && isset($this->request->data['getReportExcel'])) {
					$this->autoLayout = false;
					$filename = 'Currently Teaching Teachers  By Academic Rank Statistics-' . date('Ymd H:i:s');
					$this->set(compact('headerLabel', 'filename', 'getActiveTeacherByAcademicRank', 'positions', 'educations'));
					$this->render('/Elements/reports/xls/stakeholders/active_teacher_academicrank_stat_xls');
					return;
				}
			} else if ($this->request->data['Report']['report_type'] == 'specialNeedsStudentStatistics') {
				// to do
			}

			/* $default_department_id = $this->request->data['Report']['department_id'];
			$default_program_id = $this->request->data['Report']['program_id'];
			$default_program_type_id = $this->request->data['Report']['program_type_id'];
			$academic_year_selected = $this->request->data['Report']['acadamic_year'];
			$program_id = $this->request->data['Report']['program_id'];
			$program_type_id = $this->request->data['Report']['program_type_id']; */
		}

		$report_type_options = array(
			'Statistics' => array(
				'currentlyActiveStudentStatistics' => 'Currently Active Student Statistics',
				'studentConstituencyByAgeGroup' => 'Student Constituency By Age Group',
				'staffHDPCompletedTeachers' => 'HDP Training Completed Teachers',
				'activeTeachersByDegree' => 'Currently Active Teachers By Degree',
				'activeTeachersByAcademicRank' => 'Currently Active Teachers By Academic Rank',
				'teachersOnStudyLeave' => 'Teachers On Study Leave',
				//'specialNeedsStudentStatistics'=>'Special Needs Students Statistics',
				//todo
				//	'prospectiveGraduates'=>'Prospective graduates',
				// 'foreignStudents'=>'Foreign Students',

			),

		);

		$regions = ClassRegistry::init('Region')->find('list');
		// $programs = ClassRegistry::init('Program')->find('list');
		// $program_types = ClassRegistry::init('ProgramType')->find('list');
		$academicStatuses = ClassRegistry::init('AcademicStatus')->find('list');
		//debug($academicStatuses);
		/* if (!empty($this->department_ids) || !empty($this->college_ids)) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_ids,
				$this->college_ids
			);
		} else {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				1,
				$this->department_id,
				$this->college_id
			);
		}

		$yearLevels =  ClassRegistry::init('YearLevel')->distinct_year_level();
		$programs = array(0 => 'All Programs') + $programs;
		$program_types = array(0 => 'All Program Types') + $program_types;
		
		if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
				0,
				$this->department_id,
				array()
			);
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			$departments =  ClassRegistry::init('Department')->allDepartmentsByCollege2(1, array(), $this->college_id);
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
			
			$checkForAuth = ClassRegistry::init('User')->getUserDetails($this->Session->read('Auth.User')['id']);
			//debug($checkForAuth['StaffAssigne']);

			$allowedProgIDs = unserialize($checkForAuth['StaffAssigne']['program_id']);

			$programs = ClassRegistry::init('Program')->find('list', array(
				'conditions' => array(
					'Program.id' => $allowedProgIDs,
				),
			));

			$allowedProgTypeIDs = unserialize($checkForAuth['StaffAssigne']['program_type_id']);
			
			$program_types = ClassRegistry::init('ProgramType')->find('list', array(
				'conditions' => array(
					'ProgramType.id' => $allowedProgTypeIDs,
				),
			));

			$allowedDeptIDs = unserialize($checkForAuth['StaffAssigne']['department_id']);

			$departments = ClassRegistry::init('Department')->find('list', array(
				'conditions' => array(
					'Department.id' => $allowedDeptIDs,
				),
			));

		} else {
			$departments = array(0 => 'All University Students') + $departments;
		}

		$yearLevels = array(0 => 'All Year Level') + $yearLevels; */
		$regions = array(0 => 'All') + $regions;
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;
		$default_year_level_id = null;
		$default_year_level_id = null;
		$default_region_id = null;


		//$default_department_id = null;
		$default_department_id = (isset($this->request->data['Report']['department_id']) ? $this->request->data['Report']['department_id'] : (isset($this->department_ids) ? array_values($this->department_ids)[0] : ''));
		$default_program_id = (isset($this->request->data['Report']['department_id']) ? $this->request->data['Report']['program_id'] : (isset($this->program_ids) ? array_values($this->program_ids)[0] : ''));
		$default_program_type_id = (isset($this->request->data['Report']['program_type_id']) ? $this->request->data['Report']['program_type_id'] : (isset($this->program_type_ids) ? array_values($this->program_type_ids)[0] : ''));
		$default_year_level_id =  (isset($this->request->data['Report']['year_level_id']) ? $this->request->data['Report']['year_level_id'] : (isset($this->year_levels) ? array_values($this->year_levels)[0] : ''));
		$default_region_id = (isset($this->request->data['Report']['region_id']) ? $this->request->data['Report']['region_id'] : 0);

		$exclude_graduated =  (isset($this->request->data['Report']['exclude_graduated']) ? $this->request->data['Report']['exclude_graduated'] : 0);
		$freshman = ($this->onlyPre == 1 ? 1 : (isset($this->request->data['Report']['freshman']) ? $this->request->data['Report']['freshman'] : 0));
		$only_with_complete_data = (isset($this->request->data['Report']['only_with_complete_data']) ? $this->request->data['Report']['only_with_complete_data'] : 0);
		$only_registered = (isset($this->request->data['Report']['only_registered']) ? $this->request->data['Report']['only_registered'] : 0);
		
		$graph_type = array('bar' => 'Bar Chart', 'line' => 'Line Chart'/* , 'pie' => 'Pie Chart' */);


		if ($this->onlyPre) {
			$this->request->data['Report']['freshman'] = 1;
			$this->request->data['Report']['year_level_id'] = '';
		}

		$this->__init_clear_session_filters();
		$this->__init_search_report();
		debug($this->request->data);

		$graph_type = array('bar' => 'Bar Chart', 'pie' => 'Pie Chart', 'line' => 'Line Chart');

		if (isset($this->request->data['Report']['exclude_graduated'])) {
			$exclude_graduated = $this->request->data['Report']['exclude_graduated'];
		} else {
			$exclude_graduated = 0;
		}

		$this->set(compact(
			//'departments',
			//'program_types',
			//'programs',
			//'yearLevels',
			'regions',
			'academicStatuses',
			'graph_type',
			'default_region_id',
			'default_program_type_id',
			'graph_type',
			'student_lists',
			'default_program_id',
			'default_department_id',
			'report_type_options',
			'default_year_level_id',
			'exclude_graduated'
		));
	}

	private function __years($college_idds)
	{
		$college_id = explode('~', $college_idds);
		if (count($college_id) > 1) {
			$years =  ClassRegistry::init('YearLevel')->find('list', array(
				'conditions' => array(
					'YearLevel.department_id in (select id from departments where college_id=' . $college_id[1] . ' )'
				),
				'fields' => array(
					'YearLevel.name',
					'YearLevel.name'
				),
				'group'=> array('YearLevel.name')
			));
		} else if (!empty($college_idds)) {
			$years =  ClassRegistry::init('YearLevel')->find('list', array(
				'conditions' => array(
					'YearLevel.department_id' => $college_idds
				),
				'fields' => array(
					'YearLevel.name',
					'YearLevel.name'
				),
				'group'=> array('YearLevel.name')
			));
		} else {
			$years = ClassRegistry::init('YearLevel')->find('list', array(
				'fields' => array(
					'YearLevel.name', 
					'YearLevel.name'
				),
				'group'=> array('YearLevel.name')
			));
		}
		return $years;
	}

	private function __label($prefix, $data/* , $acadamic_year, $semester = null, $program_type_id, $program_id, $department_id, $gender = null, $year_level_id = null, $freshman = 0 */)
	{
		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');
		$label = '';
		$name = '';

		$program_type_id = $data['Report']['program_type_id'];
		$program_id = $data['Report']['program_id'];
		$acadamic_year = $data['Report']['acadamic_year'];
		$semester = $data['Report']['semester']; 
		$year_level_id = $data['Report']['year_level_id'];
		$freshman = (isset($data['Report']['freshman']) ? $data['Report']['freshman'] : 0);
		$department_id = $data['Report']['department_id'];

		$gender =  $data['Report']['gender'];
		
		$label .= $prefix . ' for ' .  $acadamic_year . ', ' . ($semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : ($semester == 'III' ? '3rd' : $semester))) . ' semester';
		
		if ($program_type_id == 0) {
			//$label .= ' for all Program Types ';
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' for all assigned Program Types ';
			} else {
				$label .= ' for all Program Types ';
			}
		} else {
			$label .= ' for ' .$programTypes[$program_type_id];
		}

		if ($program_id == 0) {
			//$label .= ', all Programs';
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' all assigned Programs';
			} else {
				$label .= ' all Programs';
			}
		} else {
			$label .= ', ' . $programs[$program_id];
		}

		if ($data['Report']['report_type'] == 'get_student_graduate_for_hemis') {
			//$label .= ' ';
		} else {
			if ($freshman) {
				$label .= ', Pre/Freshman';
			} else if (!empty($year_level_id)) {
				if ($year_level_id == 0) {
					$label .= ', all Year Level';
				} else if ($year_level_id == "pre") {
					$label .= ', Pre/Freshman';
				} else {
					$label .= ', ' . $year_level_id . ' year';
				}
			} else if ($year_level_id == 0) {
				$label .= ', all Year Level';
			} else {
				$label .= ', ';
			}
		}

		if (!empty($gender)) {
			if ($gender == "all") {
				$label .= ' students in';
			} else if ($gender == "female") {
				$label .= ' Program Female students in';
			} else if ($gender == "male") {
				$label .= ' Program Male students in';
			}
		} else {
			$label .= ' students in';
		}

		$college_id = explode('~', $department_id);

		if (count($college_id) > 1) {
			$namee = ClassRegistry::init('College')->find('first', array('conditions' => array('College.id' => $college_id[1]), 'recursive' => -1));
			$name .= (((strpos($namee['College']['name'], $namee['College']['type']) === false)/*  || !(strcasecmp($namee['College']['type'], 'institute') == 0) */) ?  ' '. $namee['College']['type'] . ' of ' : '') . ' ' . $namee['College']['name'];
		} else if (!empty($department_id)) {
			$namee = ClassRegistry::init('Department')->find('first', array('conditions' => array('Department.id' => $department_id), 'recursive' => -1));
			$name .=  ' ' . $namee['Department']['name'] .' ' . $namee['Department']['type'];
		} else if ($department_id == 0) {
			//$name .= ' All ' . Configure::read('CompanyName');
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' all assigned Departments';
			} else {
				$name .= ' all ' . Configure::read('CompanyName');
			}
		}

		$label .= $name;
		return $label;
	}

	private function __excel_file_name($prefix = '', $data)
	{
		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');
		
		$label = '';
		$name = '';

		$program_type_id = $data['Report']['program_type_id'];
		$program_id = $data['Report']['program_id'];
		$acadamic_year = $data['Report']['acadamic_year'];
		$semester = $data['Report']['semester']; 
		$year_level_id = $data['Report']['year_level_id'];
		$freshman = $data['Report']['freshman'];
		$department_id = $data['Report']['department_id'];
		
		$label .= $prefix . ' ' . (str_replace('/', '-', $acadamic_year)) .' '. ($semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : ($semester == 'III' ? '3rd' : $semester))) . ' Semester';
		
		if ($data['Report']['report_type'] == 'get_student_graduate_for_hemis') {
			//$label .= ' ';
		} else {
			if ($freshman) {
				$label .= ' Pre-Freshman';
			} else if (!empty($year_level_id)) {
				if ($year_level_id == 0) {
					$label .= ' All Year Levels';
				} else if ($year_level_id == "pre") {
					$label .= ' Pre-Freshman';
				} else {
					$label .= ' ' . $year_level_id . ' Year';
				}
			} else if ($year_level_id == 0) {
				$label .= ' All Year Levels';
			}
		}

		if ($program_id == 0) {
			//$label .= ' All Programs';
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' All Assigned Programs';
			} else {
				$label .= ' All Programs';
			}
		} else {
			$label .= ' ' . $programs[$program_id];
		}

		if ($program_type_id == 0) {
			//$label .= ' All Program Types';
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$label .= ' All Assigned Programs Types';
			} else {
				$label .= ' All Programs Types';
			}
		} else {
			$label .= ' ' .$programTypes[$program_type_id];
		}

		$college_id = explode('~', $department_id);

		if (count($college_id) > 1) {
			$namee = ClassRegistry::init('College')->field('College.shortname', array('College.id' => $college_id[1]));
			$name .= ' ' . $namee;
		} else if (!empty($department_id)) {
			$namee = ClassRegistry::init('Department')->field('Department.shortname', array('Department.id' => $department_id));
			$name .= ' ' . $namee;
		} else if ($department_id == 0) {
			//$name .= ' ' . Configure::read('CompanyShortName');
			if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
				$name .= ' all assigned Departments';
			} else {
				$name .= ' ' . Configure::read('CompanyShortName');
			}
		}

		$label .= $name .' '. date('Y-m-d'); //date('Y-m-d H-i-s');
		return $label;
	}
}