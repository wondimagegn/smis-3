<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController
{
	public $menuOptions = array(
		'parent' => 'dashboard',
		'exclude' => array(
			'academic_calender', 'announcement',
			'official_transcript_request',
			'official_request_tracking',
			'online_admission_tracking', 'admission',
			'check_graduate',
			'get_department_combo'
		)
	);

	var $helpers = array('DatePicker', 'Media.Media');

	public $uses = array('OfficialTranscriptRequest', 'OnlineApplicant');

	public $paginate = array();

	public $components = array('EthiopicDateTime', 'Email', 'Paginator', 'AcademicYear', 'MathCaptcha',);


	public function beforeRender()
	{
		$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y'));
		$defaultacademicyear = $this->AcademicYear->current_academicyear();
		$this->set(compact('acyear_array_data', 'defaultacademicyear'));
	}

	public function beforeFilter()
	{
		parent::beforeFilter();
		//$this->layout='page';
		$this->layout = "page-alternative";
		$this->Auth->allow(
			'academic_calender',
			'announcement',
			'official_transcript_request',
			'official_request_tracking',
			'online_admission_tracking',
			'admission',
			'check_graduate',
			'get_department_combo'
		);
	}

	public function display()
	{
		$this->layout = 'default-e';
		$path = func_get_args();
		$count = count($path);

		if (!$count) {
			return $this->redirect('/');
		}

		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}

		if (!empty($path[1])) {
			$subpage = $path[1];
		}

		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}


	public function academic_calender()
	{
		if (isset($this->request->data) && !empty($this->request->data['viewAcademicCalendar'])) {
			
			$options = array();

			if (!empty($this->request->data['Search']['program_id'])) {
				$options[] = array(
					'AcademicCalendar.program_id' => $this->request->data['Search']['program_id']
				);
			}

			if (!empty($this->request->data['Search']['program_type_id'])) {
				$options[] = array(
					'AcademicCalendar.program_type_id' => $this->request->data['Search']['program_type_id']
				);
			}

			if (!empty($this->request->data['Search']['department_id'])) {
				$options[] = array(
					'AcademicCalendar.department_id like ' => '%s:_:"' . $this->request->data['Search']['department_id'] . '"%',
				);
			}

			if (!empty($this->request->data['Search']['academic_year'])) {
				$options[] = array(
					'AcademicCalendar.academic_year' => $this->request->data['Search']['academic_year']
				);
			}

			if (!empty($this->request->data['Search']['semester'])) {
				$options[] = array(
					'AcademicCalendar.semester' => $this->request->data['Search']['semester']
				);
			}

			$academicCalendars = ClassRegistry::init('AcademicCalendar')->find('all', array(
				'conditions' => $options,
				'contain' => array('Program', 'ProgramType')
			));

			/* $academicCalendars = ClassRegistry::init('AcademicCalendar')->find('all', array(
				'conditions' => $options,
				'contain' => array('College', 'Department', 'YearLevel', 'Program', 'ProgramType')
			)); */

			if (empty($academicCalendars)) {
				$this->Flash->info('There is no academic calendar defined in the system in the given criteria.');
			} else {
				foreach ($academicCalendars as $ack => &$ackv) {
					$department_ids = unserialize($ackv['AcademicCalendar']['department_id']);
					$year_level_ids = unserialize($ackv['AcademicCalendar']['year_level_id']);
					$found = false;

					$college_ids_found = array();

					if(!empty($department_ids)){
						
						foreach ($department_ids as $dptkey => $dptvalue) {
							$college_ids = explode('pre_', $dptvalue);
							if (count($college_ids) > 1) {
								array_push($college_ids_found, $college_ids[1] );
							}
						}

						// debug(implode(", ", $college_ids_found));

						// this is  not the correct setting, pre selection in adding in acalendar affects how department and year level is being displayed, this fixes that temporarly
						// but it is not to see and correct duplicated calendar definitions for frehsnam(selecting check all for departments while adding calendar, pre is also selected) 

						//$ackv['AcademicCalendar']['department_name'] = implode(", ", ClassRegistry::init('AcademicCalendar')->Department->find('list', array('conditions' => array('Department.id' => $department_ids))));
						//$ackv['AcademicCalendar']['year_name'] = implode(", ", $year_level_ids);

						if(!empty($college_ids_found)){
							$ackv['AcademicCalendar']['department_name'] = implode(", ", ClassRegistry::init('AcademicCalendar')->College->find('list', array('conditions' => array('College.id' => $college_ids_found))));
							$ackv['AcademicCalendar']['year_name'] = 'Pre/Freshman';
						} else {

							// will show the calendar have duplicate definition or whether the added calendar is correct as desired.
							// although this is the correct setting, pre selection in adding in acalendar affects how department and year level is being displayed 

							$ackv['AcademicCalendar']['department_name'] = implode(", ", ClassRegistry::init('AcademicCalendar')->Department->find('list', array('conditions' => array('Department.id' => $department_ids))));
							$ackv['AcademicCalendar']['year_name'] = implode(", ", $year_level_ids);
						}
					}


					/* if (in_array("pre_", $department_ids, true)) {
						$ackv['AcademicCalendar']['department_name'] = implode(", ", ClassRegistry::init('AcademicCalendar')->College->find('list', array('conditions' => array('College.id' => $department_ids))));
						$ackv['AcademicCalendar']['year_name'] = 'Pre/1st';
					} else {
						$ackv['AcademicCalendar']['department_name'] = implode(", ", ClassRegistry::init('AcademicCalendar')->Department->find('list', array('conditions' => array('Department.id' => $department_ids))));
						$ackv['AcademicCalendar']['year_name'] = implode("\n", $year_level_ids);
					} */
				}
			}
		}

		$programs = ClassRegistry::init('Program')->find('list');
		$programTypes = ClassRegistry::init('ProgramType')->find('list');

		$this->set(compact('academicCalendars', 'programs', 'programTypes'));
		//$this->set(compact('departments', 'academicCalendars', 'programs', 'programTypes'));
	}


	public function announcement()
	{
		$announcements = ClassRegistry::init('Announcement')->getNotExpiredAnnouncements();
		$this->set(compact('announcements'));
	}

	public function official_transcript_request()
	{
		$this->layout = 'login';
		$trackingnumber = ClassRegistry::init('OfficialTranscriptRequest')->nextTrackingNumber();

		$this->OfficialTranscriptRequest->set($this->request->data);

		if ($this->OfficialTranscriptRequest->validates($this->request->data)) {
			debug($this->request->data);
		} else {
			$errors = $this->OfficialTranscriptRequest->validationErrors;
			$errors = $this->OfficialTranscriptRequest->invalidFields();
		}
		if ($this->request->is('post')) {
			$this->request->data['OfficialTranscriptRequest']['trackingnumber'] = $trackingnumber;
			$this->OfficialTranscriptRequest->create(); 
			$this->OfficialTranscriptRequest->set($this->request->data);
			if ($this->OfficialTranscriptRequest->saveAll($this->request->data)) {
				$this->Flash->success('The official transcript request has been forwared to designated personnel.Your tracking number is $trackingnumber.');
				return $this->redirect(array('action' => 'official_request_tracking'));
			} else {
				$error = $this->OfficialTranscriptRequest->invalidFields();
				debug($error);
				$this->Flash->error('The official transcript request could not be saved.');
			}
		}

		$admissiontypes = ClassRegistry::init('ProgramType')->find('list', array('fields' => array(
			'ProgramType.name',
			'ProgramType.name'
		)));

		$degreetypes['Bachelor of Arts'] = "Bachelor of Arts";
		$degreetypes['Bachelor of Science'] = "Bachelor of Science";
		$degreetypes['Doctor of Medicine'] = "Doctor of Medicine";
		$degreetypes['Master of Science'] = "Master of Science";
		$degreetypes['Master of Arts'] = "Master of Arts";
		$degreetypes['Doctor of Philosophy'] = 'Doctor of Philosophy';

		$this->set(compact('admissiontypes', 'degreetypes'));
	}

	public function official_request_tracking()
	{
		if (isset($this->request->data['OfficialTranscriptRequest']) && !empty($this->request->data['OfficialTranscriptRequest']['trackingnumber'])) {
			$request = $this->OfficialTranscriptRequest->find('first', array('conditions' => array('OfficialTranscriptRequest.trackingnumber' => trim($this->request->data['OfficialTranscriptRequest']['trackingnumber'])), 'contain' => array('OfficialRequestStatus')));
			if (empty($request)) {
				$this->Flash->warning('The tracking number provided is not valid or request cancelled.');
			}
			$this->set(compact('request'));
		}

		$statuses = array(
			'request_verified' => 'Request Verified', 
			'request_cancelled' => 'Request Cancelled',
			'document_sent' => 'Document Sent To Destination'
		);
		$this->set(compact('statuses'));
	}


	public function online_admission_tracking()
	{
		if (isset($this->request->data['OnlineApplicant']) && !empty($this->request->data['OnlineApplicant']['trackingnumber'])) {

			$request = $this->OnlineApplicant->find('first', array(
				'conditions' => array(
					'OnlineApplicant.applicationnumber' => trim($this->request->data['OnlineApplicant']['trackingnumber'])
				), 
				'contain' => array(
					'OnlineApplicantStatus', 
					'Program', 
					'ProgramType',
					'Department', 
					'College'
				)
			));

			if(isset($this->request->data['Attachment'][1]['file']['name']) && !empty($this->request->data['Attachment'][1]['file']['name']) && isset($request) && !empty($request)){
				//incase already submitted update attachment
                $this->request->data = $this->OnlineApplicant->preparedAttachment($this->request->data);
				$this->request->data['OnlineApplicant'] = $request['OnlineApplicant'];
			    debug($this->request->data);

			    if(isset($request['Attachment'][1]) && !empty($request['Attachment'][1])){

					$this->request->data['Attachment'][1]['id'] = $request['Attachment'][1]['id'];
					$this->request->data['Attachment'][1]['foreign_key'] = $request['Attachment'][1]['foreign_key'];

					if(isset($this->request->data['Attachment'][1])  && !empty($this->request->data['Attachment'][1])){
						if ($this->OnlineApplicant->saveAll($this->request->data)) {
							$this->Flash->success('Your online application paymentslip is updated successfully to application number '.$request['OnlineApplicant']['applicationnumber'].' and use it for tracking your application status.');
						}
					}
				} else {
					//upload the first time 
				   if(isset($this->request->data['Attachment'][1]['name']) && !empty($this->request->data['Attachment'][1]['name']) && isset($this->request->data['OnlineApplicant']['id']) && !empty($this->request->data['OnlineApplicant']['id'])){
						if ($this->OnlineApplicant->saveAll($this->request->data)) {
							$this->Flash->success('Your online application paymentslip is updated successfully to application number '.$request['OnlineApplicant']['applicationnumber'].' and use it for tracking your application status.');
						}
				    }
				}
			}
			debug($request);
			if (empty($request)) {
				$this->Flash->info('The application number  is invalid or request cancelled.');
			}
			$this->set(compact('request'));
		}
		//$statuses = array('pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected');
		$statuses = array('0' => 'Pending', '1' => 'Approved', '-1' => 'Rejected');

		$this->set(compact('statuses'));
	}

	public function admission()
	{
		// application form will be active based on the deadline
		$applicationnumber = ClassRegistry::init('OnlineApplicant')->nextTrackingNumber();
		$this->OnlineApplicant->set($this->request->data);
		$academicCalendars = ClassRegistry::init('AcademicCalendar')->find('first', array('conditions' => array('AcademicCalendar.online_admission_end_date >=' => date('Y-m-d'))));

		if (isset($academicCalendars) && !empty($academicCalendars)) {

			$departmentIds=array();
			$programIds=array();
			$programTypesIds=array();

		        foreach($academicCalendars as $k=>$v){
					$tmp = unserialize($v['AcademicCalendar']['department_id']);
					$departmentIds = array_merge($departmentIds, $tmp);
					$programIds[$v['AcademicCalendar']['program_id']] = $v['AcademicCalendar']['program_id'];
					$programTypesIds[$v['AcademicCalendar']['program_type_id']] = $v['AcademicCalendar']['program_type_id'];
				}
			
			$academicCalendar['AcademicCalendar']['department_id'] = $departmentIds;

			$departments = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $departmentIds)));
			$college_ids = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $departmentIds), 'fields' => array('Department.college_id', 'Department.college_id')));
			$colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.id' => $college_ids)));
			$programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('Program.id' =>$programIds )));
			$programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('ProgramType.id' => $programTypesIds)));
		}

		/*
        if($this->OnlineApplicant->validates($this->request->data)) {
        	debug($this->request->data);
        } else {
        	$errors=$this->OnlineApplicant->validationErrors;
        	$errors=$this->OnlineApplicant->invalidFields();
        	debug($errors);
        }
        */
		if ( $this->request->is('post') && isset($this->request->data['applyOnline']) && !empty($this->request->data['applyOnline'])) {

			$this->request->data['OnlineApplicant']['applicationnumber'] = $applicationnumber;
			$isAdmitted = $this->OnlineApplicant->isAppliedFordmittion($this->request->data);

			if ($isAdmitted == 0) {
				$this->request->data = $this->OnlineApplicant->preparedAttachment($this->request->data);

				if ($this->OnlineApplicant->saveAll($this->request->data, array('validate' => false))) {
					$message = "You made an online admission application request that has been forwared to designated personnel of the university for further processing. <br /> <strong>Your application  number is <u> $applicationnumber </u> and use it for tracking your application status.</strong> <br /> <br/> ";
					$departmentName = $this->OnlineApplicant->Department->field('Department.name', array('Department.id' => $this->request->data['OnlineApplicant']['department_id']));
					$collegeName = $this->OnlineApplicant->College->field('College.name', array('College.id' => $this->request->data['OnlineApplicant']['college_id']));
					$programName = $this->OnlineApplicant->Program->field('Program.name', array('Program.id' => $this->request->data['OnlineApplicant']['program_id']));
					$programTypeName = $this->OnlineApplicant->ProgramType->field('ProgramType.name', array('ProgramType.id' => $this->request->data['OnlineApplicant']['program_type_id']));
					
					$message .= '<table width="100%" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;border:0;margin:0;auto">';
					$message .= "<tr><td align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>Name:</td><td align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>" . $this->request->data['OnlineApplicant']['first_name'] . ' ' . $this->request->data['OnlineApplicant']['father_name'] . ' ' . $this->request->data['OnlineApplicant']['father_name'] . "</td></tr>";
					$message .= "<tr><td  align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>Study Level:</td><td  align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>" . $programName . "</td></tr>";
					$message .= "<tr><td align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>Admission Type:</td><td  align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>" . $programTypeName . "</td></tr>";
					$message .= "<tr><td align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>College:</td><td  align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>" . $collegeName . "</td></tr>";
					$message .= "<tr><td align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>Department:</td><td  align='left' valign='top' style='font-family:Tahoma,Arial,Helvetica,sans-serif;color:#000;font-size:16px;line-height:24px;'>" . $departmentName . "</td></tr>";
					$message .= '</table>';

					$Email = new CakeEmail('default');
					$Email->template('onlineapplication');
					$Email->emailFormat('html');
					$Email->to($this->request->data['OnlineApplicant']['email']);
					$Email->subject('Online Admission Summary: ' . $this->request->data['OnlineApplicant']['first_name'] . ' ' . $this->request->data['OnlineApplicant']['father_name'] . ' for ' . $this->request->data['OnlineApplicant']['academic_year'] . ' academic year');
					$Email->viewVars(array('message' => $message));

					try {
						if ($Email->send()) {
							$this->Flash->success("Your online admission application request has been forwarded to designated personnel of the university for further processing. Your application number is  $applicationnumber and sent to " . $this->request->data['OnlineApplicant']['email'] . " address for tracking your application status.");
						} else {
							$this->Flash->success("Your online application request has been forwared to designated personnel of the university for further processing. Your application  number is $applicationnumber and use it for tracking your application status.");
						}
					} catch (Exception $e) {
						$this->Flash->success("Your online application request has been forwared to designated personnel of the university for further processing. Your application  number is $applicationnumber and use it for tracking your application status.");
						return $this->redirect(array('action' => 'online_admission_tracking'));
					}
					return $this->redirect(array('action' => 'online_admission_tracking'));

				} else {
					$error = $this->OnlineApplicant->invalidFields();
					debug($error);
					$this->Flash->error('The online admission request could not be saved.');
				}
			} else {
				$this->Flash->success("Your online application request has been forwared to designated personnel of the university for further processing. Your application  number is $isAdmitted and use it for tracking your application status.");
				return $this->redirect(array('action' => 'online_admission_tracking'));
			}
		}

		//$departments = ClassRegistry::init('Department')->find('list');
		//$colleges = ClassRegistry::init('College')->find('list');

		if (isset($academicCalendars['AcademicCalendar']['academic_year']) && !empty($academicCalendars['AcademicCalendar']['academic_year'])) {
			$acyeardatas[$academicCalendars['AcademicCalendar']['academic_year']] = $academicCalendars['AcademicCalendar']['academic_year'];
			$semester[$academicCalendars['AcademicCalendar']['semester']] = $academicCalendars['AcademicCalendar']['semester'];
		}

		$this->set(compact('departments', 'academicCalendars', 'programs', 'programTypes', 'colleges', 'departments', 'semester', 'acyeardatas'));
	}

	public function check_graduate($studentID = null)
	{
		debug($this->request->data);
		if ((!empty($this->request->data) && isset($this->request->data['continue'])) || !empty($studentID)) {

			if ((isset($this->request->data['Page']['security_code']) && $this->MathCaptcha->validates($this->request->data['Page']['security_code'])) || isset($this->request->data['Page']['mathCaptcha']) ||!empty($studentID)) {
				
				if (empty($studentID)) {
					$studentID = trim($this->request->data['Page']['studentID']);
				} else {
					$studentID = str_replace('-','/', $studentID);
				}
				
				$isStudentValid = ClassRegistry::init('Student')->find('count', array('conditions' => array('Student.studentnumber' => $studentID), 'recursive' => -1));

				debug($this->request->data);
				debug($isStudentValid);

				if ($isStudentValid > 0) {

					$students = ClassRegistry::init('GraduateList')->Student->find('first', array(
						'conditions' => array(
							'Student.studentnumber' => $studentID
						),
						'contain' => array(
							'GraduateList', 
							'Attachment', 
							'Program', 
							'Department', 
							'College', 
							'ProgramType', 
							'Curriculum' => array(
								'fields' => array(
									'english_degree_nomenclature', 
									/* 'amharic_degree_nomenclature', 
									'certificate_name', 
									'specialization_amharic_degree_nomenclature', 
									'specialization_english_degree_nomenclature' */
								)
							),
							'StudentExamStatus' => array('order' => array('StudentExamStatus.created' => 'DESC')),
							'ExitExam' => array('order' => array('ExitExam.exam_date' => 'DESC'))
						)
					));

					$this->set(compact('students'));

				} else {
					$this->Flash->info('The student number provided is not found in our system. If you made typo error please try again else the given student number is not our student based on the admitted student data since 2012 G.C!. For Further verification of students graduated offline or not enrolled online, contact office of the university registrar via email, official letter or in person.');
				}
			} else {
				$this->Flash->error('Please enter the correct answer to the math question.');
			}
			if (!empty($this->request->data['Page']['studentID'])) {
				$this->set('studentID', trim($this->request->data['Page']['studentID']));
			} else {
				$this->set('studentID', $studentID);
			}
		}

		//debug($student_number);
		/* if (!empty($studentID) && !isset($this->request->data['continue'])) {
			$this->set('studentID', str_replace('-','/', $studentID));
			//$this->request->data['continue'] = 1;
			debug($this->request->data);
			debug($_POST);

		} */

		$this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
	}

	function get_department_combo($college_id)
	{
		$this->layout = 'ajax';
		$departments = array();
		$academicCalendars = ClassRegistry::init('AcademicCalendar')->find('first', array('conditions' => array('AcademicCalendar.online_admission_end_date >=' => date('Y-m-d'))));
		
		debug($academicCalendars);

		if (isset($academicCalendars) && !empty($academicCalendars)) {
			$academicCalendars['AcademicCalendar']['department_id'] = unserialize($academicCalendars['AcademicCalendar']['department_id']);
			debug($academicCalendars);

			$collegeIds = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.id' => $academicCalendars['AcademicCalendar']['department_id']), 'fields' => array('Department.college_id', 'Department.college_id')));
			//$collegeIds = $academicCalendar['AcademicCalendar']['college_id'];
			
			if (in_array($college_id, $collegeIds)) {

				$departments = ClassRegistry::init('Department')->find('list', array(
					'conditions' => array(
						'Department.college_id' => $college_id,
						'Department.id' => $academicCalendars['AcademicCalendar']['department_id']
					)
				));
			}
		}

		$this->set(compact('departments'));
	}
}
