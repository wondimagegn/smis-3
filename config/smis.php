<?php
namespace app\Config_app;

namespace app\Config_app;

use Cake\Core\Configure;
use Composer\Config;

// This file contains configuration parameters of the smis application that  are common to all installations.  */

//----------------------------- BACKUP PATH -------------------------------------------------
//Wonde
Configure::write('Utility.backupPath', '/home/wonde/code/sis/smis-3-migration/smis-3/backup/');
Configure::write('Utility.cache', '/home/wonde/code/sis/smis-3-migration/smis-3/tmp/cache');
Configure::write('Utility.command',"/home/wonde/code/sis/smis-3-migration/smis-3/bin/cake -app /home/wonde/code/sis/smis-3-migration/smis-3/ backup");


/*
	The following equivalentACL is used to automatically give privilege for false controllers index or other action if any of sub menu privilege is enabled.
	Even if it is primarily designed for false controllers, it can be used for other controllers.
	The checking is taken place @ cake/libs/controller/components/acl.php line 268 - 310 (DbAcl class check() function).
	Make sure that you use capitalized controller name.
*/

// To give privilege if any of the given controller action is enabled, use
// 		Controller/*    syntax
// To give privilege if only specific action is granted, use
// 		Controller/action     syntax

$equivalentACL =  array(
	'Graduation/index' => array(
		'GraduationStatuses/*',
		'GraduateLists/*',
		'SenateLists/*',
		'Certificates/*',
		'GraduationLetters/*',
		'GraduationCertificates/*',
		'GraduationWorks/*',
		'ExamGrades/student_copy'
	),
	'Certificates/index' => array(
		'ExamGrades/student_copy'
	),
	'Placement/index' => array(
		//'AcceptedStudents/*',
		'ReservedPlaces/*',
		'PlacementsResultsCriterias/*',
		/* 'Students/index',
		'Students/admit_all',
		'Students/admit', */
		'Preferences/*',
		'PreferenceDeadlines/*',
		'Sections/*',
		'ParticipatingDepartments/*',
		'Quotas/*',
		'acceptedStudents/index',
		'acceptedStudents/add',
		'acceptedStudents/generate',
		'acceptedStudents/export_print_students_number',
		'acceptedStudents/import_newly_students',
		'Students/index',
		'Students/admit_all',
		'Students/admit'
	),
	'AcceptedStudents/index' => array(
		'Students/index',
		'Students/admit_all',
		'Students/admit'
	),
	'Dormitory/index' => array(
		'DormitoryBlocks/*',
		'DormitoryAssignments/*'
	),
	'MealService/index' => array(
		'MealHalls/*',
		'MealHallAssignments/*',
		'MealAttendances/*',
		'MealTypes/*'
	),
	'Transfers/index' => array(
		'ProgramTypeTransfers/*',
		'DepartmentTransfers/*'
	),
	/// false controller of schedule
	'Schedule/index' => array(
		'CourseSchedule/*',
		'ExamSchedule/*',
		'ClassRoomBlocks/*',
		'ScheduleSetting/*'
	),
	'Evalution/index' => array(
		'InstructorEvalutionQuestions/*',
		'InstructorEvalutionSettings/*',
		'ColleagueEvalutionRates/*',
		'StudentEvalutionRates/*',
		'ContinuousAssessment/*'
	),
	'CourseSchedule/index' => array(
		'CourseConstraint/*',
		'PublishedCourses/add_course_session'
	),
	'CourseConstraint/index' => array(
		'ClassPeriodCourseConstraints/*',
		'ClassRoomClassPeriodConstraints/*',
		'ClassRoomCourseConstraints/*',
		'InstructorClassPeriodCourseConstraints/*'
	),
	'ExamSchedule/index' => array(
	    'ExamSchedules/*',
	    'ExamPeriods/*',
	    'MergedSectionsExams/*',
	    'ExamExcludedDateAndSessions/*',
	    'ExcludedPublishedCourseExams/*',
	    'ExamRoomNumberOfInvigilators/*',
	    'StaffForExams/*',
	    'SectionSplitForExams/*',
	    'ExamConstraint/*'
	),
	'ExamConstraint/index' => array(
	    'CourseExamGapConstraints/*',
	    'CourseExamConstraints/*',
	    'ExamRoomConstraints/*',
	    'ExamRoomCourseConstraints/*',
	    'InstructorExamExcludeDateConstraints/*',
	    'InstructorNumberOfExamConstraints/*',
	),
	'ScheduleSetting/index' => array(
	    'ClassPeriods/*',
	    'PeriodSettings/*',
	),
	'Registrations/index' => array(
		'CourseAdds/*',
		'CourseExemptions/*',
		'CourseDrops/*',
		'CourseSubstitutionRequests/*',
		'CourseRegistrations/*'
	),
	'Security/index' => array(
		'Users/*',
		'Logs/*',
		'Securitysettings/*'
	),
	'MainDatas/index' => array(
		'Universities/*',
		'Titles/*',
		'Positions/*',
		'Countries/*',
		'Cities/*'
	),
	'Grades/index' => array(
		'ExamGrades/*',
		'ExamTypes/*',
		'GradeSettings/*',
		'Attendances/*',
		'ExamResults/*',
		'MakeupExams/*',
		'ExamGradeChanges/department_makeup_exam_result',
		'ExamGradeChanges/manage_department_grade_change',
		'ExamGradeChanges/manage_college_grade_change',
		'ExamGradeChanges/freshman_makeup_exam_result',
		'ExamGradeChanges/manage_freshman_grade_change',
	),
	'GradeSettings/index' => array(
		'GradeScales/*',
		'Colleges/delegate_scale'
	),
	'Curriculums/index' => array(
		'PublishedCourses/*',
		'Courses/*',
		'CourseInstructorAssignments/*',
		'EquivalentCourses/*',
		'DepartmentStudyPrograms/*',
		'StudyPrograms/*',
	),
	'Clearances/index' => array(
		'TakenProperties/*'
	),
	'CostShares/index' => array(
		'Payments/*',
		'ApplicablePayments/*',
		'CostSharingPayments/*',
	),
	'HealthService/index' => array(
		'MedicalHistories/*',
		'Students/manage_student_medical_card_number',
	),
);

Configure::write('ACL.equivalentACL', $equivalentACL);

/*
Excluded controllers from the ACL management.
	It will be used when:
		1. Controller is a false controller with out any action
		2. Controller on which any user should not has any access right. E.G. Role management
*/

//	To exclude one action from one controller,
//		use     Controller/action     syntax
//	To exclude controller,
//		use     Controller    syntax
//	To exclude the given action from all controllers,
// 		use     */action    syntax

$excludedACL = array(
    'Dashboard',
    'Security',
    'Acls',
    'PlacementSettings',
    'Schedule',
    'Evalution',
    //'ExamSchedule',
    'CourseSchedule',
    'Transfers',
    'MealService',
    'Dormitory',
    'Placement',
    'Notes',
    'Departments/get_department_combo',
	'Departments/index',
    'CourseDrops/list_students',
    'CourseDrops/edit',
    'CourseDrops/view',
    'CourseDrops/delete',
    'CourseAdds/edit',
    'CourseAdds/view',
    'CourseAdds/delete',
    'CourseAdds/invalid',
    'CourseAdds/search',
    'CourseAdds/get_published_add_courses',
    'TypeCredits',
    'Registrations',
    'PublishedCourses/print_published_pdf',
    'PublishedCourses/export_published_xls',
    'PublishedCourses/get_year_level',
    'PublishedCourses/get_course_type_session',
    'PublishedCourses/getPublishedCoursesForSplit',
    'PublishedCourses/getPublishedCoursesForExam',
    'PublishedCourses/get_course_grade_scale',
    'PublishedCourses/getPublishedCourses',
    'PublishedCourses/selectedPublishedCourses',
    'PublishedCourses/selectedPublishedCourses',
    'PublishedCourses/get_course_published_for_section',
    'PublishedCourses/publisheForUnassigned',
    'PublishedCourses/getPublishedCoursesForExamForSplit',
    'PublishedCourses/delete',
    'PublishedCourses/view',
    'PublishedCourses/edit',
	'Curriculums/get_courses',
    'Curriculums/get_curriculums',
    'Curriculums/get_curriculum_combo',
    'Curriculums/search',
	'Curriculums/approve',
	'Curriculums/lock',
	'Curriculums/activate',
	'Curriculums/add_departmernt_study_program_for_curriculum',
	'Sections/export',
	'Sections/edit',
	'Certificates/*',
	'Sections/section_move_update',
	'Sections/section_move_update',
	'Sections/view_pdf',
	'Sections/deleteStudentforThisSection',
	'Sections/move',
	'Sections/upgrade_selected_student_section',
	'AcademicCalendars/index',
	'AcademicStands/index',
	'CourseDrops/index',
	'SenateLists/index',
	'SenateLists/search',
	'GraduateLists/index',
	'GraduateLists/search',
	'GraduateLists/edit',
	'AcademicRules/index',
	'StudentStatusPatterns/index',
	'Reports/index',
	'Titles/index',
	'UnschedulePublishedCourses/*',
	'ClassRoomBlocks/get_class_room_block_exam_rooms',
	'Sections/add_student_section',
	'Sections/add_student_section_update',
	'Sections/get_sections_by_program',
	'Sections/get_sections_by_dept',
	'Sections/get_sections_by_academic_year',
	'Sections/get_sections_of_college',
	'Sections/get_modal_box',
	'Sections/get_sections_by_program_and_dept',
	'Sections/get_year_level',
	'Sections/get_section_students',
	'Media',
   	'Roles/edit',
    'Weblinks',
    'Books',
    'Journals',
    'Attachments',
    'Contacts',
    'XmlRpc',
    'Pages',
    'Offers',
    'Students/search',
    'Students/student_lists',
    'Students/get_course_registered_and_add',
    'Students/get_modal_box',
    'Students/ajax_get_department',
    'Students/delete',
    'Students/get_regions',
    'Students/get_cities',
    'Students/ajax_update',
    'Students/add',
    'GradesRegistrationsDates',
    'Departments/get_department_combo',
    'Courses/search',
    'StudentsDepartments',
    'PasswordChanageVotes', // is used by you (haile)?
    'Programs',
    'HighSchoolEducationBackgrounds',
    'HigherEducationBackgrounds',
    'Prerequisites',
    'GradeScalePublishedCourses',
    'Dismissals',
    'Withdrawals',
    'Webservices',
    'AutoMessages',
    'MainDatas',
    'ProgramTypes/view',
    'ProgramTypes/edit',
    'ProgramTypes/delete',
    'ProgramTypes/add',
    'ProgramTypes/get_program_types',
    'Staffs/get_instructor_combo',
    'CourseInstructorAssignments/get_department',
    'CourseInstructorAssignments/assign_instructor_update',
    'CourseInstructorAssignments/reset_department',
    'CourseInstructorAssignments/get_assigned_courses_of_instructor_by_section_for_combo',
    'CourseInstructorAssignments/assign_instructor',
    'CourseInstructorAssignments/get_course_instructor_detail',
    'CourseInstructorAssignments/edit',
    'CourseInstructorAssignments/add',
    'CourseSubstitutionRequests/edit',
    'GradeScaleDetails',
    'Mailers/add',
    'Mailers/edit',
    'Mailers/delete',
    'AcademicStatuses/add',
    'AcademicStatuses/edit',
    'AcademicStatuses/delete',
    'AcademicStands/search',
    'CourseGroupedSections',
    'MergedSectionsForCourses',
    'MergedSectionsCourses',
    'CourseSplitSections',
    'ExamConstraintView',
    'ExamSplitSections',
    'GradeSettings',
    'ScheduleSetting',
    'CourseConstraint',
    'ExamConstraint',
    'ClassRooms/add',
    'ClassRooms/view',
    'ClassRooms/edit',
    'CourseSchedules/edit',
    'CourseSchedules/add',
    'CourseSchedules/delete',
    'CourseSchedules/edit',
    'CourseSchedules/unschedule_courses_possible_causes',
    'CourseSchedules/manual_schedule_unscheduled',
    'CourseSchedules/change_schedule',
    'CourseSchedules/manual_schedule_unscheduled',
	'Graduation', //Exclude "Graduation" controller from permission management
	'Users/get_department',
	//'ExamResults/get_exam_result_entry_form', //Remove "get_exam_result_entry_form" action from "ExamResults" controller
	'ExamResults/edit',
	'ExamResults/delete',
	'ExamResults/view',
	'AcceptedStudents/search',
	'AcceptedStudents/view',
	'AcceptedStudents/auto_fill_preference',
	'AcceptedStudents/summery',
	'AcceptedStudents/count_result',
	'AcceptedStudents/manual_placement',
	'AcceptedStudents/print_autoplaced_pdf',
	'AcceptedStudents/export_autoplaced_xls',
	'AcceptedStudents/download',
	'AcceptedStudents/print_students_number_pdf',
	'AcceptedStudents/export_students_number_xls',
	'Preferences/get_preference',
	'Preferences/view',
	'users/login',
	'users/logout',
	'users/delete',
	'users/editprofile',
	'users/useticket',
	'users/add',
	'users/edit',
	'user/forget',
	'users/newpassword',
	'users/confirm_task',
	'users/changePwd',
	'users/forget',
	'Votes',
	'ExamResults/index',
	'ExamGrades/auto_ng_and_do_to_f',
	'HealthService',
	'MedicalHistories/index',
	'MedicalHistories/view',
	'MedicalHistories/delete',
	'Dormitories',
	'DormitoryBlocks/index',
	'DormitoryAssignments/index',
	'MealHalls/index',
	'MealAttendances/index',
	'MealHallAssignments/get_colleges',
	'MealHallAssignments/get_departments',
	'MealHallAssignments/get_year_levels',
	'MealHallAssignments/get_department_year_levels',
	'MealHallAssignments/add_student_meal_hall',
	'MealHallAssignments/add_student_meal_hall_update',
	'DepartmentStudyPrograms/get_department_study_programs_combo',
	'DepartmentStudyPrograms/get_selected_department_department_study_programs',
	'*/autocomplete', //Remove "autocomplete" from all controllers
);
Configure::write('ACL.excludedACL', $excludedACL);

//Login page background
//images in /webroot/img/login-background folder

$imgcnt = 10;
$login_page_background = array();

if($imgcnt) {
	for ($i= 0; $i < $imgcnt; $i++) {
		$login_page_background[$i]['1366_768'] = $i.'-1280-800.jpg';
		$login_page_background[$i]['1280_800'] = $i.'-1280-800.jpg';
		$login_page_background[$i]['1280_768'] = $i.'-1280-768.jpg';
		$login_page_background[$i]['1280_720'] = $i.'-1280-720.jpg';
		$login_page_background[$i]['1024_768'] = $i.'-1024-768.jpg';
		$login_page_background[$i]['800_600'] = $i.'-800-600.jpg';
	}
}

Configure::write('Image.login_background', $login_page_background);


//Rename ACL generated menu by humanized name suitable to the application
//$rename_menu_title['courseRegistrations']='Registration';
$rename_menu_title['registrations'] = 'Registration';
$rename_menu_title['costShares'] = 'Billing';
$rename_menu_title['securitysettings'] = 'Security Settings';
$rename_menu_title['students'] = 'Admitted Students';
$rename_menu_title['mainDatas'] = 'Main Data';
$rename_menu_title['examTypes'] = 'Exam Setup';
$rename_menu_title['examResults'] = 'Exam Result & Grade';
$rename_menu_title['courseInstructorAssignments'] = 'Assign Instructor for a Course';
$rename_menu_title['graduationLetters'] = 'Letter Template';
$rename_menu_title['graduationCertificates'] = 'Graduation Certificate Template';
$rename_menu_title['graduationWorks'] = 'Graduation Works';
$rename_menu_title['graduationStatuses'] = 'Graduation Statuses';
$rename_menu_title['graduationRequirements'] = 'Graduation Requirements';
$rename_menu_title['examGrades'] = 'Grades';
$rename_menu_title['examGradeChanges'] = 'Grade Change';
$rename_menu_title['makeupExams'] = 'Makeup Exam';
$rename_menu_title['gradeSettings'] = 'Grade Setting';
$rename_menu_title['makeupExams'] = 'Makeup & Supplmentary Exam';
$rename_menu_title['helps'] = 'Help';
$rename_menu_title['sections'] = 'Manage Sections';
$rename_menu_title['colleagueEvalutionRates'] = 'Colleague Evalution';
$rename_menu_title['studentStatusPatterns'] = 'Academic Status & Pattern';
$rename_menu_title['studentEvalutionRates'] = 'Evaluate Your Instructor';

Configure::write('Menu.title_rename', $rename_menu_title);


// When entering dates for objects like 'expected graduated date', show only a limited range of years:

Configure::write('Calendar.universityEstablishement', 1986);
Configure::write('Calendar.yearsAhead', 10);
Configure::write('Calendar.yearsInPast', 2);
Configure::write('Calendar.birthdayInPast', 60);
Configure::write('Calendar.birthdayAhead', 0);
Configure::write('Calendar.senateApprovalInPast', 5);
Configure::write('Calendar.senateApprovalAhead', 0);
Configure::write('Calendar.senateListStartYear', 2011);
Configure::write('Calendar.graduateListStartYear', 2011);
Configure::write('Calendar.applicationStartYear', 2012);
Configure::write('Calendar.graduateApprovalAhead', 0);
Configure::write('Calendar.expectedGraduationInFuture', 8);
Configure::write('Calendar.graduateApprovalInPast', 5);
Configure::write('Calendar.clearanceWithdrawInPast', 1);
Configure::write('Calendar.clearanceWithdrawInFuture', 0);
Configure::write('Calendar.daysAvaiableForGraduateDeletion', 60);

// added by neway for smis 4 update  ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

Configure::write('ExamGrade.Approval.yearsInPast', 2);
Configure::write('ExamGradeChange.SuppExam.yearsInPast', 5);
Configure::write('Users.AccountDeactivation.yearstoLookGivenLastLogin', 2);

Configure::write('FootableDataPageSizeXSmall', 20);
Configure::write('FootableDataPageSizeSmall', 50);
Configure::write('FootableDataPageSizeLarge', 100);
Configure::write('FootableDataPageSizeXLarge', 500);

// Decimal Places for Round Function to use System-wide
define('DECIMAL_PLACES', 2);

// Decimal Places for Round Function to use in Placement
define('DECIMAL_PLACES_PLACEMENT', 2);

// Credit to ECTS conversion
/* The study load of subjects is expressed in ECTS (European Credit Transfer System).
	** One ECTS is equal to 28 hours of study (can be 25 -30 hours in some countries)
	** course subjects have a study load of either 5 ECTS  equal to 140 (5 x 28 hours)
	** The total study load for a three-year Bachelor’s degree course is 180 ECTS (3 x 60 ECTS).

	** 3 Credit Hour course is equivalent to 5 ECTS in AMU, so, 1 credit  = 1.666666667 ECTS
*/

define('CREDIT_TO_ECTS', 1.666666667);

define('REQUIRE_FILE_UPLOAD_FOR_CLEARANCE', 0);
define('REQUIRE_FILE_UPLOAD_FOR_WITHDRAWAL', 1);

// for filtering clearance approal and profile not build lists
define('DAYS_BACK_CLEARANCE', 365);
define('DAYS_BACK_COURSE_SUBSTITUTION', 365);
define('DAYS_BACK_PROFILE', 365);
define('DAYS_BACK_DISPATCHED_NOTIFICATION', 365);

define('DAYS_BACK_READMISSION', 365);
define('DAYS_ALLOWED_TO_DELETE_PROFILE_PICTURE_FROM_LAST_UPLOAD', 1);

define('DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE', 2);

// checks admission date of the student and last student_section created especially speeds up for freshman
// sectionless/ adding students for freshman sections is taking long time to load given many students are dropping out
define('DAYS_BACK_FOR_SECTIONLESS_LOOKUP', 365);
// replaced by the following codes

define('ALLOW_STDENT_SECTION_MOVE_TO_NEXT_YEAR_LEVEL', 1);

define('ACY_BACK_FOR_SECTION_ADD', 2);

define('ACY_BACK_FOR_SECTION_LESS', 2);

define('ACY_BACK_FOR_READMISSION', 3);

define('ACY_BACK_FOR_SECTION_LIST_SUPPLEMENTARY_EXAM', 5);

define('ACY_BACK_FOR_ALL', 5);

define('COUNTRY_ID_OF_ETHIOPIA', 68);

define('REGION_ID_OF_ADDIS_ABABA', 1);
define('REGION_ID_OF_DIRE_DAWA', 5);

define('DISMISSED_ACADEMIC_STATUS_ID', 4);

define('ACY_BACK_FOR_ROLLING_BACK_GRADE_SUBMISSION', 1); // Default: 0, Set 0 for currenct accademic year only

define('ACY_BACK_FOR_GRADE_CHANGE_APPROVAL', 7);

define('MAXIMUM_C_PLUS_GRADES_ALLOWED_FOR_POST_GRADUATE', 1);
define('MAXIMUM_C_GRADES_ALLOWED_FOR_POST_GRADUATE', 1);

define('USE_CALENDAR_GRADE_SUBMISSION_END_DATE_INSTEAD_OF_GRADE_SUBMITTED_DATE_FOR_GRADE_CHANGE_DEADLINE_CALCULATION', 0); // 0 = don't allow grade change before any approved grade.
// 0, is the default and will use the grade submitted date for the student and calculates the grade change deadline by adding days available for grade change from general settings.
// 1, will use the grade submission deadline set in academic calendar and calculates the grade change deadline by adding days available for grade change from general settings regardless of the date student grade is submitted.

// ACY Back for Grade Cancellation and Update Back Dated Data Entry
define('ACY_BACK_FOR_BACK_DATED_DATA_ENTRY', 10); // Default: 0, Set 0 for currenct accademic year only

// Allowed Grades to delete in Back Dated Data Entry/ Grade Cancelattion and update

$allowed_grades_for_deletion = array('NG' => 'NG', 'I' => 'I', 'W' => 'W', 'DO' => 'DO');
Configure::write('allowed_grades_for_deletion', $allowed_grades_for_deletion);

define('ALLOW_REGISTRAR_ADMIN_TO_DELETE_VALID_GRADES', 0);
define('ALLOW_REGISTRAR_ADMIN_TO_CHANGE_VALID_GRADES', 0);

define('ALLOW_NON_ADMIN_REGISTRAR_GRADE_ENTRY_ON_NOT_DEACTIVATED_INSTRUCTOR_ASSIGNMENT', 0);
define('ALLOW_ADMIN_REGISTRAR_GRADE_ENTRY_ON_NOT_DEACTIVATED_INSTRUCTOR_ASSIGNMENT', 1);


define('NATIONAL_ID_IMPORT_TEMPLATE_FILE', '/files/template/national_id_import_template.xls');
define('ACY_BACK_FOR_STUDENT_NATIONAL_ID_CHECK', 1);

define('OTP_IMPORT_TEMPLATE_FILE', '/files/template/otp_import_template.xls');


define('ACY_BACK_FOR_DEPARTMENT_TRANSFER_DROP_DOWN', 1); // Years abck to show in drop down for searching requests.
define('DEFAULT_DAYS_FOR_DEPARTMENT_TRANSFER_REQUEST_CHECK', 365); // Back Days to check to list department transfer requests approval and invalidate auto delete/reject old requests from current day.

// Academic Calendar Default Settings

$semesters = ['I' => 'I', 'II' => 'II', 'III' => 'III'];

Configure::write('semesters', $semesters);

define('DEFINED_SEMESTERS_COUNT', count($semesters));

$exemptedCourseGradesOptions = array(
	'A+' => 'A+', 'A' => 'A', 'A-' =>  'A-',
	'B+' => 'B+', 'B' => 'B', 'B-' =>  'B-',
	'C+' => 'C+', 'C' => 'C', 'C-' =>  'C-',
	'D' => 'D',
	'P' => 'P'
);

Configure::write('exemptedCourseGradesOptions', $exemptedCourseGradesOptions);

define('MAXIMUM_YEAR_LEVELS_ALLOWED', 10);

define('APPLICATION_START_YEAR', '2012');
define('UNIVERSITY_START_YEAR', '1986');


$exit_exam_types = array('Exit Exam' => 'Exit Exam');
Configure::write('exit_exam_types', $exit_exam_types);


$benefit_groups = array('Normal' => 'Normal', 'Pastoralist' => 'Pastoralist', 'Visual Impaired' => 'Visual Impaired', 'Deaf' => 'Deaf');
Configure::write('benefit_groups', $benefit_groups);


define('ENABLE_MOODLE_INTEGRATION', 1);
define('ACY_BACK_FOR_MOODLE_INTEGRATION', 2);
define('MOODLE_SITE_URL', 'https://online.amu.edu.et');
define('MOODLE_PASSWORD_ENCRYPRION_ALGORITHM', 'sha1');

define('ALLOW_MOODLE_INTEGRATION_FOR_SUBMITTED_GRADE', 1);

define('SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS', 1);
define('OTP_OFFICE_365_OUTLOOK_URL', 'https://outlook.office.com');
define('OTP_OFFICE_365_MAIN_URL', 'https://www.office.com');

$otp_services_option = array('Office365' => 'Office 365', 'Elearning' => 'E-Learning', 'ExitExam' => 'Exit Exam');
Configure::write('otp_services_option', $otp_services_option);


$allowed_grades_graduation_for_pg = array(
	'A+' => 'A+', 'A' => 'A', 'A-' =>  'A-',
	'B+' => 'B+', 'B' => 'B', 'B-' =>  'B-',
	'C+' => 'C+', 'C' => 'C',
	'P' => 'P', 'PASS' => 'PASS',
);

Configure::write('allowed_grades_graduation_for_pg', $allowed_grades_graduation_for_pg);

define('C_PLUS_GRADES_ALLOWED_FOR_GRADUATION_FOR_PG_PROGRAM', 1);
define('C_GRADES_ALLOWED_FOR_GRADUATION_FOR_PG_PROGRAM', 1);

define('ALLOW_STUDENTS_TO_UPLOAD_PROFILE_PICTURE', 0);
define('ALLOW_REGISTRAR_TO_UPLOAD_PROFILE_PICTURE', 0);

define('FORCE_REGISTRAR_TO_FILL_STUDENTS_PRIMARY_CONTACT_INFORMATION', 0);

define('REQUIRE_STUDENTS_TO_UPLOAD_PROFILE_PICTURE_WHEN_UPDATING_PROFILE', 0);

define('ALLOW_ESLCE_RESULTS_TO_BE_FILLED_FOR_UNDER_GRADUATE_STUDENTS', 0);


define('ALLOW_STAFFS_TO_UPLOAD_PROFILE_PICTURE', 0);

/*
	Possible Values for ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES:
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	'AUTO' = Determine from General Settings as set for each Program/Program Type,
	1 = ignore General Settings and only allow course adds for failed Grades system wide,
	0 = ignore general Settings and allow any course to be added system wide.
*/

define('ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES', 'AUTO');
//define('ONLY_ALLOW_COURSE_ADD_FOR_FAILED_GRADES', 1);

/*
	'AUTO' = Determine from General Settings as set for each Program/Program Type,
	1 = Allow system wide, ignore General Settings set for each Program/Program Type
	0 = Disable system wide, ignore General Settings set for each Program/Program Type
*/

define('ALLOW_COURSE_ADD_FROM_HIGHER_YEAR_LEVEL_SECTIONS', 'AUTO');
define('ALLOW_GRADE_REPORT_PDF_DOWNLOAD_TO_STUDENTS', 'AUTO');
define('ALLOW_REGISTRATION_SLIP_PDF_DOWNLOAD_TO_STUDENTS', 'AUTO');
define('ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL', 'AUTO');

define('ALLOW_GRADE_REPORT_PDF_DOWNLOAD_CURRENT_SEMESTER_ONLY', 1);  // 1: allow to download current or last semester grade report only, 0: allow all registered semester grades to be dowloaded at any time( not recommended: increases server load to generate status for every semester up on request.)

define('ALLOW_STUDENTS_TO_USE_FORGOT_PASSWORD_BY_EMAIL', 'AUTO');

// Controlls the system to send or not send emails irrispective of general settings set per program and program type.

define('GRADE_NOTIFICATION_FOR_STUDENTES_SYSTEM_WIDE_ENABLED', 0); // 0: disabled system wide   1: Enabeled System Wide but checked against General Settings to send or not send depending on Program or Program Type.

// Not applicable in real world exept there are different Academic Year definition within the same year. We will consider in the future, not needed as such now, Neway
//define('ALLOW_COURSE_ADD_FROM_DIFFERENT_ACADEMIC_YEAR', 'AUTO');

// No of days should be a multiple if 7 (7 days a week)

define('DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE', 14);
define('DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F', 28);
define('DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F', 28);
define('DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F', 28);

// No of weeks should be a multiple if 4 (48 weeks in ACY)

define('DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER', 16);
define('DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR', 48);
define('DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR',  2/* count($semesters) */);


//Days Available for staff evaluation after grade submission end date.
define('ACY_BACK_FOR_STAFF_EVALUATION_LIST_PRINT_AND_ARCHIEVE', 4);

//Maximun staff evaluation rate
define('MAXIMUM_STAFF_EVALUATION_RATE', 5);

// an instructor should have the following no of evaluation from his/her colleagues before his head evaluates him/her and print his report.
define('MINIMUM_COLLEAGUE_EVALUATION_COUNT_FOR_HEAD_EVALUATION_AND_PRINT', 3);

// the minimum no of evaluation an instructor should fill before getting his evaluation printted.
define('REQURED_MINIMUM_COLLEAGUE_EVALUATION_TO_FILL_INSTRUCTOR', 3);

// Force the instructor to fill the defined no of colleage evaluations for the current active academic calendar after login/before allowing him/her ro do anything.
define('FORCE_INSTRUCTOR_TO_FILL_REQURED_MINIMUM_COLLEAGUE_EVALUATION_AFTER_LOGIN', 0);

// days available for students to fill evaluation starting from definfed days prior to grade submission,
// The system must not prevent students to see their continues assesment before some predefined weeks of defined grade submission deadline or must not allow/force students to fill evaluation just in the first weeks of classes or just after the head assigns instructors to courses
define('DEFAULT_WEEK_COUNT_FOR_STAFF_EVALUATION_FOR_STUDENTS', 2);

// weather to allow or deny students to fill evaluation after grade is submitted or after grade submission dead line.
define('ALLOW_STAFF_EVALUATION_AFTER_GRADE_SUBMISSION', 0);

// weather to allow or deny colleages to fill evaluation after an academic semester is passed
// This is mandatory as staffs are filling evaluation after evaluations are printed and claiming the evaluations are not correct or ask department to reprint evaluation for promotions in the case of department head change.
define('DEFAULT_DAYS_AVAILABLE_FOR_STAFF_EVALUATION', DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * 7);


define('DEFAULT_MINIMUM_CREDIT_FOR_STATUS', 6);
define('DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER', 24);

define('ACY_BACK_COURSE_ADD_DROP_APPROVAL', 3); //0= Current academic year only, 1= current acy and 1 year back, 2= current acy and 2 year back etc..
define('ACY_BACK_GRADE_APPROVAL_DASHBOARD', 2); //0= Current academic year only, 1= current acy and 1 year back, 2= current acy and 2 year back etc..
define('ACY_BACK_COURSE_REGISTRATION', 5); //0= Current academic year only, 1= current acy and 1 year back, 2= current acy and 2 year back etc..
define('RESTRICT_NON_ADMIN_REGISTRAR_TO_ACY_BACK_COURSE_REGISTRATION', 1); // 0:  allow to non admin registrar accounts (not the director) to maintaing missing reistration for a student from student admitted academic year to current academic year. //1 restrict to defined number of years(ACY_BACK_COURSE_REGISTRATION) from current academic year while maintaining missing course registrations and wrong NG cancellations.

define('YEARS_BACK_FOR_NG_F_FX_W_DO_I_CANCELATION', 8);

define('DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION', 0); // 0: Delete only Grade and Keep Assesment Data, 1: Delete All Data Grades, Assesments and Registrations, Adds, etc

define('YEARS_BACK_FOR_CURRICULUM_ATTACH_DETACH', 6); // some programs have specializations after 3 or 4 years that requires different curriculum. Eg. Electrical, Mechanical else, it shoudn't be that much big 3 years is enough.

define('AUTO_MESSAGE_LIMIT', 5); // limit to load auto messages from db for dashboard Auto Message Modal.

// owner password for TCPDF PDF Encryption for $owner_pass parameter
define('OWNER_PASSWORD', '1qazXSw23eDC@@');

// user password for TCPDF PDF Encryption for $user_pass parameter
define('USER_PASSWORD', '');

// the small logo file full path to be used in TCPPDF PDF Files
define('UNIVERSITY_LOGO_HEADER_FOR_TCPDF', '/app/webroot/img/amulogo-sc.gif');

// Transparent full page logo file full path to be used in TCPPDF PDF Files
define('UNIVERSITY_FULL_PAGE_TRANSPARENT_LOGO_FOR_TCPDF', '/app/webroot/img/amulogo-transparent.gif');

// Transparent registrar stamp/ seal  file full path to be used for grade report and registration slips TCPPDF PDF Files
define('REGISTRAR_TRANSPARENT_STAMP_FOR_TCPDF', '/app/webroot/img/seal.png');

// for QR Code don't ommit the last /
define ('BASE_URL_HTTPS','https://smis.amu.edu.et/');

// for student Copy
define ('UNIVERSITY_WEBSITE','https://www.amu.edu.et');
define ('REGISTRAR_EMAIL','our@amu.edu.et');
define ('PORTAL_URL_HTTPS','https://smis.amu.edu.et');

define ('UNIVERSITY_MOTTO_EN','The Center of Bright Future!');
define ('UNIVERSITY_MOTTO_AM','የብሩህ ተስፋ ማዕከል!');

define ('INSTITUTIONAL_EMAIL_SUFFIX','@amu.edu.et');

//To allow/deny to edit instructor profile for Department and College Admin Accounts (only Title Position and Education fields allowed to edit
//(Email & Phone number fields are disabled by default except the instructor itself or system Admin)

define('ENABLE_INSTRUCTOR_USER_EDIT_COLLEGE_DEPARTMENT', 1);

//define('STUDENT_IMPORT_TEMPLATE_FILE', '/files/template/template.xls');
define('INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE', 1);

define('STUDENT_IMPORT_TEMPLATE_FILE', '/files/template/accepted_students_import_template.xls');
define('STUDENT_IMPORT_TEMPLATE_FILE_WITHOUT_STUDENT_NUMBER', '/files/template/accepted_students_import_template_new.xls');

define('EXIT_EXAM_IMPORT_TEMPLATE_FILE', '/files/template/exit_exam_import_template.xls');

// for formating words like curriculum names, course titles, Thesis titles and others to Title Case
$prepositions_ucf = [' And ',' The ',' About ',' Like ',' Above ',' Near ',' Across ',' Of ',' After ',' Off ',' Against ',' On ',' Along ',' Onto ',' Among ',' Opposite ',' Around ',' Out ',' As ',' Outside ',' At ',' Over ',' Before ',' Past ',' Behind ',' Round ',' Below ',' Since ',' Beneath ',' Than ',' Beside ',' Through ',' Between ',' To ',' Beyond ',' Towards ',' By ',' Under ',' Despite ',' Underneath ',' Down ',' Unlike ',' During ',' Until ',' Except ',' Up ',' For ',' Upon ',' From ',' Via ',' In ',' With ',' Inside ',' Within ',' Into ',' Without '];
$prepositions_lc = [' and ',' the ', ' about ',' like ',' above ',' near ',' across ',' of ',' after ',' off ',' against ',' on ',' along ',' onto ',' among ',' opposite ',' around ',' out ',' as ',' outside ',' at ',' over ',' before ',' past ',' behind ',' round ',' below ',' since ',' beneath ',' than ',' beside ',' through ',' between ',' to ',' beyond ',' towards ',' by ',' under ',' despite ',' underneath ',' down ',' unlike ',' during ',' until ',' except ',' up ',' for ',' upon ',' from ',' via ',' in ',' with ',' inside ',' within ',' into ',' without '];

Configure::write('prepositions_ucf', $prepositions_ucf);
Configure::write('prepositions_lc', $prepositions_lc);


$department_types = ['Department' => 'Department', 'Faculty' => 'Faculty', 'School' => 'School'];
Configure::write('department_types', $department_types);

define('DEPARTMENT_TYPE_DEPARTMENT', 'Department');
define('DEPARTMENT_TYPE_FACULTY', 'Faculty');
define('DEPARTMENT_TYPE_SCHOOL', 'School');
define('DEPARTMENT_TYPE_AMHARIC_DEPARTMENT', 'ትምህርት ክፍል');
define('DEPARTMENT_TYPE_AMHARIC_FACULTY', 'ፋኩልቲ');
define('DEPARTMENT_TYPE_AMHARIC_SCHOOL', 'ትምህርት ቤት');

// All possible Course Categoties, many departments create course categories as many as courses and it's my impact performance when generating senate lists
// SELECT `name` FROM `course_categories` WHERE 1 GROUP BY `name` HAVING count(`name`) > 20 ORDER BY `name`;
$course_category_options = ['Core(Major)' => 'Core(Major)', 'Common' => 'Common', 'Elective' => 'Elective', 'Supportive' => 'Supportive', 'Optional' => 'Optional', 'General' => 'General', 'Thesis' => 'Thesis'];
Configure::write('course_category_options', $course_category_options);


$streams = ['1' => 'Natural', '2' => 'Social'];
Configure::write('streams', $streams);

define('STREAM_NATURAL', 1);
define('STREAM_SOCIAL', 2);


$preengineering_college_ids = ['1' => '1', '11' => '11', '16' => '16'];
Configure::write('preengineering_college_ids', $preengineering_college_ids);

$social_stream_college_ids = ['6' => '6', '15' => '15'];
Configure::write('social_stream_college_ids', $social_stream_college_ids);

$natural_stream_college_ids = ['2' => '2', '14' => '14'];
Configure::write('natural_stream_college_ids', $natural_stream_college_ids);

$all_pre_freshman_remedial_college_ids = $preengineering_college_ids + $social_stream_college_ids + $natural_stream_college_ids;
Configure::write('all_pre_freshman_remedial_college_ids', $all_pre_freshman_remedial_college_ids);

$curriculum_types = ['1' => 'Semester Based', '2' => 'Year Based'];
Configure::write('curriculum_types', $curriculum_types);

define('SEMESTER_BASED_CURRICULUM', 1);
define('YEAR_BASED_CURRICULUM', 2);

$foriegn_students_region_ids = ['12' => '12', '13' => '13', '14' => '14', '15' => '15', '17' => '17'];
Configure::write('foriegn_students_region_ids', $foriegn_students_region_ids);


//// ++++++++++++++++++++++++++++++++ TEMPORARY HEMIS VARIABLES ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// Missing institution_codes(colleges and departments not exist in SMiS)  ================ FOR HEMIS REPORTS USE ONLY ===================

// for non regular students UG and PG Ptogram Types and Except Regular, Part-Time and Advance-Standing Program Types
define('CCDE_INISTITUTION_CODE', 'AMU-CCDE');

// for all PG and PhD programs & for Regular, Part-Time Students
define('PG_SCHOOL_INISTITUTION_CODE', 'AMU-SPGS');

define('FRESHMAN_COORDINATION_INISTITUTION_CODE', 'AMU-CFMC');

// Non Deparment Assigned Students for both semesters (under College of Social Sciences)
define('SOCIAL_SCIENCE_FRESHMAN_INISTITUTION_CODE', 'AMU-CFMC-DSSF');

// Non Deparment Assigned Students for first semester only (under College of Natural Sciences)
define('NATURAL_SCIENCE_FRESHMAN_INISTITUTION_CODE', 'AMU-CFMC-DNSF');

// Non Deparment Assigned Students for second semester only (under College of Natural Sciences)
define('OTHER_NATURAL_SCIENCE_FRESHMAN_INISTITUTION_CODE', 'AMU-CFMC-ONSF');

// Non Deparment Assigned Students for second semester only (under AMiT)
define('PRE_ENGINEERING_FRESHMAN_INISTITUTION_CODE', 'AMU-AMiT-PREF');

// END Missing institution_codes(colleges and departments not exist in SMiS) for HEMIS  ================ FOR HEMIS REPORTS USE ONLY ===================

/*
	Sponsor Types

	1	Regional Government => PG Regular
	2	Federal Government => UG: Primarly Regular except Scholarships; Exceptions: Advance Standing, Parttime,
	3	Private/Self => PG => All Program Types, UG: non Regular programs Except Advance Standing, Parttime, Summer
	4	Employer => UG: Advance Standing, Part-time, Summer; PG => Regualr, Part-time, PHD => All Program Types
	5	Other: all scholarship students in all programs

*/

define('SPONSORED_BY_REGIONAL_GOVERNMENT', 1);
define('SPONSORED_BY_FEDERAL_GOVERNMENT', 2);
define('SPONSORED_BY_SELF_PRIVATE', 3);
define('SPONSORED_BY_EMPLOYER', 4);
define('SPONSORED_BY_OTHER', 5);

$sponsor_types = ['1' => 'Regional Government', '2' => 'Federal Government', '3' => 'Private(Self Sponsored)' , '4' => 'Employer', '5' => 'Other'];

Configure::write('sponsor_types', $sponsor_types);

define('NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC', 8692.75);


// end added by neway ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// Application Deployed Country and City
Configure::write('ApplicationName', 'Student Management Information System');
Configure::write('ApplicationShortName', 'SMiS');
Configure::write('ApplicationVersion', '2.0');
Configure::write('ApplicationVersionShort', '2');

Configure::write('ApplicationMetaDescription', 'Arba Minch University Student Management Information System portal for teachers and students for academic transparency');
Configure::write('ApplicationMetaKeywords', 'AMU, SMiS, Arba, Minch, University, Grade, Report, Registration, Acadamic, Calendar, online, Admission, Official, Transcript,');
Configure::write('ApplicationMetaAuthor', 'Arba Minch University');
Configure::write('ApplicationTitleExtra', 'Arba Minch University Student Management Information System');

Configure::write('CompanyName', 'Arba Minch University');
Configure::write('CompanyShortName', 'AMU');

Configure::write('CompanyAmharicName', 'አርባ ምንጭ ዩኒቨርሲቲ');

Configure::write('logo','amu.png');
Configure::write('ApplicationDeployedCountryAmharic', 'ኢትዮጵያ');
Configure::write('ApplicationDeployedCountryEnglish', 'Ethiopia');
Configure::write('ApplicationDeployedCityAmharic', 'አርባ ምንጭ');
Configure::write('ApplicationDeployedCityEnglish', 'Arba Minch');
Configure::write('CopyRightCompany', 'Arba Minch University');
Configure::write('POBOX', 21);


Configure::write('Tel', '+251-468-810772');
Configure::write('Fax', '+251-468-810729/0820');

Configure::write('RegistrarName', 'Registrar and Alumni Directorate');
//Configure::write('RegistrarAmharicName', 'ሬጅስትራርና የቀድሞ ተማሪዎች ዳይሬክቶሬት');
Configure::write('RegistrarAmharicName', 'ሬጅስትራርና አሉምናይ ዳይሬክቶሬት');

// Standard date format, currently Year - month - day
Configure::write('Calendar.dateFormat', 'DMY');
Configure::write('Calendar.yearFormat', 'Y');

// SMIS date format, used instead of the above
Configure::write('SMISdateFormat', 'd-M-y');

// SMIS currency format
Configure::write('SMIScurrency','&ETB;');

// SMISunit like %
Configure::write('SMISunit','&#37;');

// Graduation work names

$graduation_work['thesis'] = 'Thesis';
$graduation_work['project'] = 'Project';

Configure::write('Graduation.graduation_work', $graduation_work);

/** Disable ACL with a flag. */

// Configure::write('ACL.disabled', false);
// Configure::write('Developer', false);
Configure::write('Developer', false);
Configure::write('NumberProcessAllowedToRunProfile', 3);

#Wonde Web service url for accessing wimis from smis
define ('WIMIS_URL','http://wmis.dev/xml_rpc');
define ('BASE_URL','http://smis.amu.edu.et/');
#define ('WIMIS_URL','http://wimis.amu.edu.et/xml_rpc');

//for forget password url construction
Configure::write('SMIS.url', 'smis.amu.edu.et');

/** Default email headers */

$email_default_from = 'SMiS <noreply@amu.edu.et>';
$email_default_reply_to = 'smis@amu.edu.et';
$email_default_return_path = 'smis@amu.edu.et';
$email_default_to = 'smis@amu.edu.et';
$email_test_to = 'wonde74@gmail.com';

Configure::write('Email.default.from', $email_default_from );
Configure::write('Email.default.replyTo', $email_default_reply_to);
Configure::write('Email.default.returnPath', $email_default_return_path);
Configure::write('Email.default.to', $email_default_to);
Configure::write('Email.test.to', $email_test_to);

define( 'EMAIL_DEFAULT_FROM', $email_default_from);
define( 'EMAIL_DEFAULT_REPLY_TO', $email_default_reply_to);
define( 'EMAIL_DEFAULT_RETURN_PATH', $email_default_return_path);
define( 'EMAIL_DEFAULT_TO', $email_default_to);
define( 'EMAIL_TEST_TO', $email_test_to);

/** End Default email headers */

/** Statuses for the request communications and system modules. */
define( 'STATUS_CREATED', 'STATUS_CREATED');
define( 'STATUS_UPDATED', 'STATUS_UPDATED');
define( 'STATUS_SENT', 'STATUS_SENT' );

/**Roles ID can be used for quick reference in the code ***/
/** Main Role IDs, for quick reference in the code: */
define('ROLE_SYSADMIN', 1);
define('ROLE_INSTRUCTOR', 2);
define('ROLE_STUDENT', 3);
define('ROLE_REGISTRAR', 4);
define('ROLE_COLLEGE', 5);
define('ROLE_DEPARTMENT', 6);
define('ROLE_MEAL', 7);
define('ROLE_HEALTH', 8);
define('ROLE_ACCOMODATION', 9);
define('ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM', 10);
define('ROLE_GENERAL', 11);
define('ROLE_CLEARANCE', 12);
define('ROLE_MANAGEMENT', 14);


Configure::write('ROLE_SYSADMIN', 1 );
Configure::write('ROLE_INSTRUCTOR', 2);
Configure::write('ROLE_STUDENT', 3);
Configure::write('ROLE_REGISTRAR', 4);
Configure::write('ROLE_COLLEGE', 5);
Configure::write('ROLE_DEPARTMENT', 6);
Configure::write('ROLE_MEAL', 7);
Configure::write('ROLE_HEALTH', 8);
Configure::write('ROLE_ACCOMODATION', 9);
Configure::write('ROLE_CONTINUINGANDDISTANCEEDUCTIONPROGRAM', 10);
Configure::write('ROLE_GENERAL', 11);
Configure::write('ROLE_CLEARANCE', 12);
Configure::write('ROLE_MANAGEMENT', 14);


/**Program Types ***/
define('PROGRAM_TYPE_REGULAR', 1);
define('PROGRAM_TYPE_EVENING', 2);
define('PROGRAM_TYPE_SUMMER', 3);
define('PROGRAM_TYPE_ADVANCE_STANDING', 4);
define('PROGRAM_TYPE_PART_TIME', 5);
define('PROGRAM_TYPE_DISTANCE', 6);
define('PROGRAM_TYPE_ON_LINE', 7);
define('PROGRAM_TYPE_WEEKEND', 8);


/**Program  ***/
define('PROGRAM_UNDEGRADUATE',1);
define('PROGRAM_POST_GRADUATE',2);
define('PROGRAM_PhD',3);
define('PROGRAM_PGDT',4);
define('PROGRAM_REMEDIAL',5);

/**PLACEMENT ASSIGMENT VARIABLES*/
define('AUTO_PLACEMENT','AUTO PLACED');
define('DIRECT_PLACEMENT','DIRECT PLACED');
define('MANUAL_PLACEMENT','MANUAL PLACED');
define('REGISTRAR_ASSIGNED','REGISTRAR PLACED');
define('CANCELLED_PLACEMENT','CANCELLED PLACEMENT');

// include(APP.'Plugin/media/config/core.php');
//Configure::write('e', '2016-10-28');

//// PLACEMENT RELATED SETTINGS

define('ACY_BACK_FOR_PLACEMENT', 1); // Default: 1, Set 0 or empty for currenct accademic year only

define('DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT', 1.67);
define('DEFAULT_MAXIMUM_CGPA_FOR_PLACEMENT', 4.00);


//conversion constant
define('PREPARATORYMAXIMUM', 700);
define('FRESHMANMAXIMUM', 4);
define('ENTRANCEMAXIMUM', 30);

// can be changed accourdingly for each batch maximums set by MoE
define('SOCIAL_STREAM_PREPARATORY_MAXIMUM', 600);
define('NATURAL_STREAM_PREPARATORY_MAXIMUM', 700);


define('INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT', 1); // Default: 1, Set 0 or empty to not use Female weight unless explicity defined in placement additional points.
define('DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT', 5);

define('DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT', 50);
define('DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT', 20);
define('DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT', 30);

// check curriculums under departments for program/program type with program modality and expect curriculums to be associated to DEPARTMENT STUDY PROGRAMS
// firter outs departments that have the selected program type by comparing program modality with program type.
define('CHECK_STUDY_PROGRAMS_FOR_ACADEMIC_CALENDAR_DEFINITION', 1);

define('REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_DEFINITION', 1); // Make Selection of Study Programs Mandatory while adding and editing curriculums

define('REQUIRE_STUDY_PROGRAMS_SELECTED_FOR_CURRICULUM_APPROVAL', 1); // Make Study Programs Mandatory while approving curriculums and make appropraite error message

define('REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_DEFINITION', 1);  // Make curriculum PDF upload mandatory while adding or editing curriculums
define('REQUIRE_CURRICULUM_PDF_UPLOAD_FOR_CURRICULUM_APPROVAL', 1);  // Make curriculum PDF upload mandatory while approving curriculums and make appropraite error message


$status_types_for_seach_approvals = ['' => 'All Statuses', '0' => 'Not Processed', '1' => 'Accepted', '-1' => 'Regected'];
Configure::write('status_types_for_seach_approvals', $status_types_for_seach_approvals);


define('REMEDIAL_PROGRAM_NATURAL_COLLEGE_ID', 14);
define('REMEDIAL_PROGRAM_SOCIAL_COLLEGE_ID', 15);

/// Mass Add or Mass Drop Switches

define('ALLOW_PUBLISH_AS_DROP_COURSE_FOR_COLLEGE_ROLE', 1);
define('ALLOW_PUBLISH_AS_DROP_COURSE_FOR_DEPARTMENT_ROLE', 1);

define('ALLOW_PUBLISH_AS_ADD_COURSE_FOR_COLLEGE_ROLE', 1);
define('ALLOW_PUBLISH_AS_ADD_COURSE_FOR_DEPARTMENT_ROLE', 1);

/// END Mass Add or Mass Drop Switches

define('MAXIMUM_ALLOWED_ATTENDED_SEMESTERS_FOR_TRANSFER', 10); // students that attended more than this value will not have the ability to request department transfer;

$only_stream_based_colleges_pre_social_natural = ['14' => '14', '15' => '15', '16' => '16'];
Configure::write('only_stream_based_colleges_pre_social_natural', $only_stream_based_colleges_pre_social_natural);

$placement_rounds = ['1' => '1', '2' => '2', '3' => '3'];
Configure::write('placement_rounds', $placement_rounds);

$programs_available_for_registrar_college_level_permissions = [ PROGRAM_UNDEGRADUATE => PROGRAM_UNDEGRADUATE, PROGRAM_REMEDIAL => PROGRAM_REMEDIAL];
Configure::write('programs_available_for_registrar_college_level_permissions', $programs_available_for_registrar_college_level_permissions);

$program_types_available_for_registrar_college_level_permissions = [ PROGRAM_TYPE_REGULAR => PROGRAM_TYPE_REGULAR, PROGRAM_TYPE_ADVANCE_STANDING => PROGRAM_TYPE_ADVANCE_STANDING ];
Configure::write('program_types_available_for_registrar_college_level_permissions', $program_types_available_for_registrar_college_level_permissions);

$programs_available_for_placement_preference = [ PROGRAM_UNDEGRADUATE => PROGRAM_UNDEGRADUATE];
Configure::write('programs_available_for_placement_preference', $programs_available_for_placement_preference);

$program_types_available_for_placement_preference = [ PROGRAM_TYPE_REGULAR => PROGRAM_TYPE_REGULAR, PROGRAM_TYPE_ADVANCE_STANDING => PROGRAM_TYPE_ADVANCE_STANDING, PROGRAM_TYPE_PART_TIME => PROGRAM_TYPE_PART_TIME ];
Configure::write('program_types_available_for_placement_preference', $program_types_available_for_placement_preference);

define('FORCE_EMAIL_VERIFICATION', 1);
define('FORCE_EMAIL_VERIFICATION_ON_UPDATE', 0);
define('FORCE_EMAIL_VERIFICATION_AFTER_LOGIN', 0);
define('FORCE_EMAIL_VERIFICATION_FOR_ALL_ROLES', 0);

define('FORCE_EMAIL_REVALIDATION', 1);
define('DAYS_TO_ENFORCE_EMAIL_REVALIDATION', (DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * 7));

$roles_for_email_verification = [ ROLE_INSTRUCTOR => ROLE_INSTRUCTOR, ROLE_STUDENT => ROLE_STUDENT, ROLE_REGISTRAR => ROLE_REGISTRAR, ROLE_COLLEGE => ROLE_COLLEGE, ROLE_DEPARTMENT =>ROLE_DEPARTMENT];
Configure::write('roles_for_email_verification', $roles_for_email_verification);


/**Service Wings  ***/
define('SERVICE_WING_ACADEMICIAN', 1);
define('SERVICE_WING_LIBRARIAN', 2);
define('SERVICE_WING_REGISTRAR', 3);
define('SERVICE_WING_TECHNICAL_SUPPORT', 4);

/**Educations  ***/
define('EDUCATION_DOCTRATE', 1);
define('EDUCATION_MASTERS', 2);
define('EDUCATION_MEDICAL_DOCTOR', 3);
define('EDUCATION_DEGREE', 4);
define('EDUCATION_DIPLOMA', 5);
define('EDUCATION_CERTIFICATE', 6);
