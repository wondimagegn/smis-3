<?php
class PreferencesController extends AppController {

	public $name = 'Preferences';
    public $menuOptions = array(
             'parent' => 'placement',
             'exclude' => array('edit_preference'),
             'alias' => array(
                    'index'=>'List Preference',
                    'add' => 'Add Preference',
                    // 'add_student_preference'=>'Record Preference',
                    'student_record_preference'=>'Record Preference'
            )
    );
	public $paginate = array();
	
    public $components = array('AcademicYear','RequestHandler');
    
     function beforeRender() {
		$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y')-12,date('Y'));
		
		$this->set(compact('acyear_array_data','selected'));
	}
	/**********************************************************************/
    function __init_search() {
        // We create a search_data session variable when we fill any criteria 
        // in the search form.
        if(!empty($this->request->data['Preference'])){
               
                    $search_session = $this->request->data['Preference'];
                   // Session variable 'search_data'
                    $this->Session->write('search_data', $search_session);
                
        } else {

        	$search_session = $this->Session->read('search_data');
        	$this->request->data['Preference'] = $search_session;
        } 

    }

   function __init_search_feed() {
	
		 if(!empty($this->request->data['Search'])){
               
                    $search_session = $this->request->data['Search'];
                   // Session variable 'search_data'
                    $this->Session->write('search_data_feed', $search_session);
                
        } else {

        	$search_session = $this->Session->read('search_data_feed');
        	$this->request->data['Search'] = $search_session;
        } 
   }
    /***************************************************************************/
    
    public function beforeFilter(){
             parent::beforeFilter();
             $this->Auth->allow('get_preference','getStudentPreference');
    }
	public function index($academic_year=null,$suffix=null) {
          
		if((isset($this->request->data['Preference']) && isset($this->request->data['viewPDF']))){
             $search_session = $this->Session->read('search_data');
             $this->request->data['Preference'] = $search_session;
         }

	    if(isset($this->passedArgs)) {
	      if(isset($this->passedArgs['page'])) {	
		 	 	$this->__init_search(); 
                $this->request->data['Preference']['page']=$this->passedArgs['page'];
                 $this->__init_search(); 
             } 
	     } 

        if((isset($this->request->data['Preference']) && isset($this->request->data['listStudentsPreference']))) {
	        $this->__init_search();
	    }

		 // academicYear 
		if (isset($this->request->data['Preference']['academicyear']) && !empty($this->request->data['Preference']['academicyear'])) {
			$this->paginate['conditions'][]['Preference.academicyear'] = $this->request->data['Preference']['academicyear'];
		}


		 // limit 
		if (isset($this->request->data['Preference']['limit']) && !empty($this->request->data['Preference']['limit'])) {
			$this->paginate['limit'] = $this->request->data['Preference']['limit'];
		}

       // filter by department or college
		if (isset($this->request->data['Preference']['preferences_order']) 
&& !empty($this->request->data['Preference']['preferences_order'])) {
			$this->paginate['conditions'][]['Preference.preferences_order'] = $this->request->data['Preference']['preferences_order'];
		
		}
		
		// filter by department or college
		if (isset($this->request->data['Preference']['department_id']) 
&& !empty($this->request->data['Preference']['department_id'])) {
			debug($this->request->data['Preference']['department_id']);
			$this->paginate['conditions'][]['Preference.department_id'] = $this->request->data['Preference']['department_id'];
		
		}

		// filter by program 

		if (isset($this->request->data['Preference']['program_id']) && !empty($this->request->data['Preference']['program_id'])) {
			//$this->paginate['conditions'][]['Student.program_id'] = $this->request->data['Preference']['program_id'];
		}

		// filter by program type
		if (isset($this->request->data['Preference']['program_type_id']) && !empty($this->request->data['Preference']['program_type_id'])){
			//$this->paginate['conditions'][]['Student.program_type_id'] = $this->request->data['Preference']['program_type_id'];
		}

            


			/*	        

			    $this->__init_search();
		        $this->Preference->recursive = 0;
		        $this->paginate=array('limit'=>3000);
		        if ( $this->role_id == ROLE_COLLEGE ) {
		              $conditions=null;
	                  $selected_academic_year=$this->request->data['Preference']['academicyear'];
	                  if($selected_academic_year) {
	                         $conditions = array('Preference.academicyear LIKE'=>
	                         $selected_academic_year.'%',
		                    'Preference.college_id'=>$this->college_id);
		                    
	                   } else {
	                       $conditions = array('Preference.academicyear LIKE'=>$this->AcademicYear->current_academicyear().'%',
		                        'Preference.college_id'=>$this->college_id);
	                   
	                   }
		         //$no_sort
		            $preferences=$this->paginate($conditions);
		            if(!empty($preferences)){
		                $this->set('preferences',$preferences);
		             } else {
		                  $this->Session->setFlash('<span></span>'.__('There is no result found within the selected academic year.'),'default',array('class'=>'info-box info-message'));
		             } 
		              
		        }
	            if($this->role_id == ROLE_STUDENT) {
	                          
	                             $acceptedStudentdetail= $this->Preference->AcceptedStudent->find('first',
				  array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id'))));
debug($this->Auth->user('id'));
		                        $conditions=array("OR"=>array(
		                        'Preference.accepted_student_id'=>$acceptedStudentdetail['AcceptedStudent']['id'],'Preference.user_id'=>$this->Auth->user('id')));
		                     $this->loadModel('PreferenceDeadline');
		                    
		                     $preference_deadline=$this->PreferenceDeadline->find('first',
				                     array('conditions'=>array('PreferenceDeadline.college_id'=>$acceptedStudentdetail['College']['id'],
				                     'PreferenceDeadline.academicyear LIKE'=>$this->AcademicYear->current_academicyear().'%')));
		                    $this->set(compact('preference_deadline'));
		                    $this->set('preferences', $this->paginate($conditions)); 
		                    return;                     
		        }	
		                
		      
	            if(!empty($this->request->data)){
	                  
	                  $conditions=null;
	                  $selected_academic_year=$this->request->data['Preference']['academicyear'];
	                  if($selected_academic_year) {
	                         $conditions = array('Preference.academicyear LIKE'=>
	                         $selected_academic_year.'%',
		                    'Preference.college_id'=>$this->college_id);
		                    
	                   } else {
	                       $conditions = array('Preference.academicyear LIKE'=>$this->AcademicYear->current_academicyear().'%',
		                        'Preference.college_id'=>$this->college_id);
	                   
	                   }
	                  
		            
		            $preferences=$this->paginate($conditions);
		            if(!empty($preferences)){
		                $this->set('preferences',$preferences);
		             } else {
		                  $this->Session->setFlash('<span></span>'.__('There is no result found within the selected academic year.'),'default',array('class'=>'info-box info-message'));
		                  unset($preferences);
		             }  
	            }
	            
	            if($academic_year){
	                   $academic_year=$academic_year.'/'.$suffix;
	                   $conditions=null;
		               if($this->role_id == ROLE_COLLEGE) {
		            
		             $acceptedStudents=$this->Preference->AcceptedStudent->find('all',array('conditions'=>array(
                    'AcceptedStudent.academicyear'=>$academic_year,'AcceptedStudent.college_id'=>$this->college_id,array("OR"=>array("AcceptedStudent.placementtype is null",
                    "AcceptedStudent.placementtype"=>CANCELLED_PLACEMENT)))));
                    
                    $preference_not_completed=array();
			        foreach($acceptedStudents as $k=>$value){
			            $count=count($value['Preference']);
			            if(!$count){
			              $preference_not_completed[]=$value;
			            }
			        }
			        unset($acceptedStudents);
			        $acceptedStudents=$preference_not_completed;
	                $this->set('selectedAcademicYear',$academic_year); 
		            $this->set('acceptedStudents',$acceptedStudents);
		            $this->render('add');  
		            
		           }
	            }
	      */ 

		if($this->role_id == ROLE_STUDENT) {

	            $acceptedStudentdetail= $this->Preference->AcceptedStudent->find('first',array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id')),'contain'=>array('Student')));
               $this->paginate['conditions'][]['Preference.accepted_student_id']=$acceptedStudentdetail['AcceptedStudent']['id'];
            
                  $preference_deadline=ClassRegistry::init('PreferenceDeadline')->find('first',array('conditions'=>array('PreferenceDeadline.college_id'=>$acceptedStudentdetail['AcceptedStudent']['college_id'],'PreferenceDeadline.academicyear LIKE'=>$acceptedStudentdetail['AcceptedStudent']['academicyear'].'%')));
                $pref=$this->Preference->find('count',array('conditions'=>array('Preference.user_id'=>$acceptedStudentdetail['AcceptedStudent']['user_id'])));
               
                   
		       $this->set(compact('preference_deadline'));

		}	  
        $this->Paginator->settings=$this->paginate;
	    debug($this->Paginator->settings);
	
       if(isset($this->Paginator->settings['conditions'])) {
       	
		     $preferences=$this->Paginator->paginate('Preference'); 
		     debug($preferences); 
		}
		else {
			$preferences= array();
		}

	   if (empty($preferences) && isset($this->request->data) && !empty($this->request->data)) {
			 $this->Session->setFlash('<span></span>'.__('No result found in a given criteria.'),'default',array('class'=>'info-box info-message'));

		}
		debug($this->request->data);
		$programs=$this->Preference->AcceptedStudent->Program->find('list');

		$program_types =$this->Preference->AcceptedStudent->ProgramType->find('list');
		if (isset($this->request->data['Preference']['academicyear']) && !empty($this->request->data['Preference']['academicyear'])) {
          $departments = $this->_getParticipatingDepartment($this->college_id,$this->request->data['Preference']['academicyear']);
          debug($departments);
		} else {
           $departments = $this->_getParticipatingDepartment($this->college_id,$this->AcademicYear->current_academicyear());
		}
		
  
	     $this->set(compact('programs', 'program_types', 'departments','preferences'));   
	}
	

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid preference'), 'default', array('class'=>'error-box error-message'));
			 $this->Session->setFlash('<span></span>'.__('Invalid preference.'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('preference', $this->Preference->read(null, $id));
	}

	function add($accepted_student_id=null) {
	  // Function to load/save search criteria.
         $logged_user_detail = ClassRegistry::init('User')->find('first',array('conditions'=>array('User.id'=>$this->Auth->user('id')),'contain' => array('Staff','Student')));  
		
        if ($this->Session->read('search_data')){	
               $this->request->data['searchacademicyear']=true;
			   $this->request->data['Search']=$this->Session->read('search_data');
        }

        if (isset($this->request->data['searchacademicyear']) && !empty($this->request->data['searchacademicyear'])){

            if(!empty($this->request->data['Search']['academicyear'])){
                    $this->__init_search_feed();    
					
			        $selectedAcademicYear = $this->request->data['Search']['academicyear'];
				 if(!empty($accepted_student_id)) {
					   $acceptedStudents=$this->Preference->AcceptedStudent->find('all',array('conditions'=>array(
					'AcceptedStudent.id !='=>$accepted_student_id,
                    'AcceptedStudent.academicyear'=>$selectedAcademicYear,
                    'AcceptedStudent.college_id'=>$this->college_id,
                    'AcceptedStudent.department_id is null',
                    'AcceptedStudent.program_type_id'=>PROGRAM_TYPE_REGULAR,
	        		 'AcceptedStudent.program_id'=>PROGRAM_UNDEGRADUATE,
                    array("OR"=>array(
                    "AcceptedStudent.placementtype is null",
                
                    "AcceptedStudent.placementtype"=>CANCELLED_PLACEMENT
                    ))),'order'=>'AcceptedStudent.full_name asc',
                    'contain'=>array('College','Program','ProgramType','Preference')));
					} else {
					    $acceptedStudents=$this->Preference->AcceptedStudent->find('all',array('conditions'=>array(
                    'AcceptedStudent.academicyear'=>$selectedAcademicYear,
                    'AcceptedStudent.college_id'=>$this->college_id,
                    'AcceptedStudent.department_id is null',
                    'AcceptedStudent.program_type_id'=>PROGRAM_TYPE_REGULAR,
	        		 'AcceptedStudent.program_id'=>PROGRAM_UNDEGRADUATE,
                    array("OR"=>array(
                    "AcceptedStudent.placementtype is null",
                
                    "AcceptedStudent.placementtype"=>CANCELLED_PLACEMENT
                    ))),'order'=>'AcceptedStudent.full_name asc',
                    'contain'=>array('College','Program','ProgramType','Preference')));

					    debug($acceptedStudents);

					}
					

				   
                    if(empty($acceptedStudents) && empty($accepted_student_id)){
                        $this->Session->setFlash('<span></span>'.__('There is no student in the selected academic year that needs preference feeding to the system.', true),'default',array('class'=>'error-box error-message'));
                    }
                    debug($acceptedStudents);
                    $preference_not_completed=array();
			        foreach($acceptedStudents as $k=>$value){
			            $count=count($value['Preference']);
			            if($count==0){
			              $preference_not_completed[]=$value;
			            }
			        }
			        unset($acceptedStudents);
			        $acceptedStudents=
			        $preference_not_completed;
			         if(empty($acceptedStudents)){
                        $this->Session->setFlash('<span></span>'.__('There is no student in the selected academic year that needs preference feeding to the system.', true),'default',array('class'=>'error-box error-message'));
                    }
				 $this->set(compact('selectedAcademicYear','acceptedStudents'));
			} else {
			  $this->Session->setFlash('<span></span>'.__('Please select academic year to add department preference to accepted students'),'default',array('class'=>'error-box error-message'));
			}
		}
		
		if(!empty($accepted_student_id)){
		   
		   if($this->role_id !=ROLE_STUDENT){
		         $valid=$this->Preference->AcceptedStudent->find('count',
		         array('conditions'=>array('AcceptedStudent.id'=>$accepted_student_id)));
		         $field_academic_year=$this->Preference->AcceptedStudent->field('academicyear',array('AcceptedStudent.id'=>$accepted_student_id));
		         if (!empty($accepted_student_id) && $valid) {
		         $elegible_user=0;
		         $elegible_user=$this->Preference->AcceptedStudent->find('count',array('conditions'=>array('AcceptedStudent.id'=>$accepted_student_id,'AcceptedStudent.college_id'=>$this->college_id,
'AcceptedStudent.department_id is null')));
				if ($elegible_user==0) {
					  $this->Session->setFlash('<span></span> You do not have the privilage to feed the selected student preference. Your action is loggged and reported to the system administrators.','default',array('class'=>'error-box error-message'));
				$details=null;
				if (isset ($logged_user_detail['Staff']) && !empty($logged_user_detail['Staff'])) {
				$details.=$logged_user_detail['Staff'][0]['first_name'].' '.
								  $logged_user_detail['Staff'][0]['middle_name'].' '.
								  $logged_user_detail['Staff'][0]['last_name'].' ('.
								  $logged_user_detail['User']['username'].')';
				} else if (isset ($logged_user_detail['Student']) && !empty($logged_user_detail['Student'])) {
								$details.=$logged_user_detail['Student'][0]['first_name'].' '.
								$logged_user_detail['Student'][0]['middle_name'].' '.
								$logged_user_detail['Student'][0]['last_name'].' ('.
								$logged_user_detail['User']['username'].')';
				}
				ClassRegistry::init('AutoMessage')->sendPermissionManagementBreakAttempt(Configure::read('User.user'), '<u>'.$details.'</u> is trying to feed student placement preference without assigned privilage. Please give appropriate warning.'); 
								//$this->redirect('add');
							 
				} else {
                     $accepted_student_detail=$this->Preference->AcceptedStudent->find('first',array('conditions'=>array('AcceptedStudent.id'=>$accepted_student_id)));
		            $this->set('accepted_student_id',$accepted_student_id);
					$this->set(compact('accepted_student_detail'));  
               }
      		  } 
   			}
			$departments=$this->_getParticipatingDepartment($this->college_id,$field_academic_year);
				if($departments){
				$departmentcount=count($departments);
				$this->set('departments',$departments);
				$this->set('departmentcount',$departmentcount);
				} else {
				$this->Session->setFlash('<span></span>'.__('Please first add placement participating departments'),'default',array('class'=>'error-box error-message'));
				$this->redirect(array('controller'=>'participatingDepartments','action' => 'add'));
				}
			}
	   if (isset($this->request->data['submitpreference'])
&& !empty($this->request->data['submitpreference'])) {
			
			/// reformat this->data to make it suitable for saveAll
			$academicyear = $this->request->data['Preference']['academicyear'];
			$accepted_student_id=$this->request->data['Preference']['accepted_student_id'];
			//check auto has already run 
			$this->loadModel('PlacementLock');
			$auto_run=$this->PlacementLock->find('count',
array('conditions'=>array('PlacementLock.college_id'=>$this->college_id,'PlacementLock.academic_year'=>$academicyear,'PlacementLock.process_start'=>1))); 
			if($auto_run){
				 $this->Session->setFlash(__('The auto placement is up and running you can not modify the preferences for students right now, please come back after you cancelled the auto placement.'),'default',array('class'=>'error-box error-message'));
				$this->redirect(array('action'=>'add'));
			}
			foreach( $this->request->data['Preference']  as &$value) {
				debug($value);
			    if(isset($academicyear) && isset($value['academicyear'])) {
					$value['academicyear']=$academicyear;		
				} else {
					$value['academicyear']=$this->AcademicYear->current_academicyear();
				}
				debug($this->college_id);
				if(isset($this->college_id)) {
				  $value['college_id']=$this->college_id;				
				} else {
					 //find college
					 $value['college_id']=ClassRegistry::init('ParticipatingDepartment')->field('ParticipatingDepartment.college_id', array('ParticipatingDepartment.department_id'=>$value['department_id']));

				}
				if($this->Auth->user('id')!=null && isset($value['user_id'])){
					$value['user_id']=$this->Auth->user('id');
				}	
			}
			

			unset($this->request->data['Preference']['accepted_student_id']);
			unset($this->request->data['Preference']['academicyear']);
			unset($this->request->data['Preference']['college_id']);
             
             $this->set($this->request->data);
			//check preference deadline
			$this->loadModel('PreferenceDeadline');
			$isPreferenceDeadlineRecorded=$this->PreferenceDeadline->find('count',array('conditions'=>array('PreferenceDeadline.college_id'=>$this->college_id,'PreferenceDeadline.academicyear LIKE'=>$academicyear.'%')));		
		  if($isPreferenceDeadlineRecorded)
          {
			// check preference recording deadline is not passed.
			$is_deadline_passed=$this->PreferenceDeadline->find('count',
			array('conditions'=>array('PreferenceDeadline.college_id'=>$this->college_id,'PreferenceDeadline.academicyear LIKE'=>$academicyear.'%',
			'PreferenceDeadline.deadline <'=>date("Y-m-d H:i:s"))));
			if(!$is_deadline_passed) {
			if(!$this->Preference->isAlreadyEnteredPreference($accepted_student_id) && $this->Preference->isAllPreferenceDepartmentSelectedDifferent($this->request->data['Preference'])){
				 if(!empty($this->request->data['Preference'])){
					if ($this->Preference->saveAll($this->request->data['Preference'],array('validate'=>'first'))) {
					$this->Session->setFlash(__('<span></span>The preference has been saved.'),'default',array('class'=>'success-box success-message'));
					} else {
					$this->Session->setFlash(__('<span></span>The preference could not be saved.Please, try again.', true),'default',array('class'=>'error-box error-message'));
					}
			      } 	
			} else {
					$error=$this->Preference->invalidFields(); 
					if(isset($error['preference']) && 
!empty($error['preference'])){
					$this->Session->setFlash(__('<span></span>'.$error['preference'][0]),'default',array('class'=>'error-box error-message'));
					} elseif(isset($error['alreadypreferencerecorded']) &&
!empty($error['alreadypreferencerecorded'])){
					$this->Session->setFlash(__('<span></span>'.$error['alreadypreferencerecorded'][0]),'default',array('class'=>'error-box error-message'));
					} elseif(isset($error['department']) && !empty($error['department'])){
					$this->Session->setFlash(__('<span></span>'.$error['department'][0]),'default',array('class'=>'error-box error-message'));
					} else {
					$this->Session->setFlash(__('<span></span>Please fill the input fields', true),'default',array('class'=>'error-box error-message'));
					}
                     /*  
					$this->redirect(array('controller'=>'preferences','action' => 'add',$accepted_student_id));
				*/
			 } 
			} else {
					$this->Session->setFlash('<span></span>'.__('Preference Deadline is passed. You can not recorded the selected student preference.Please ask the college admin for more information.', true),'default',array('class'=>'error-box error-message'));
		   } 
		  } else {
					$this->Session->setFlash('<span></span>'.__('Please set  preference deadline  first before recording students preferences', true),'default',array('class'=>'error-box error-message'));
					$this->redirect(array('controller'=>'preferenceDeadlines','action' => 'add'));
		 }	
		}
	    $user_id=$this->Auth->user('id');
		$this->set(compact('user_id')); 	
     }
//allow students to fill their own preference 
function student_record_preference() {
    if (!empty($this->request->data)) { 
     $this->loadModel('PreferenceDeadline');
$acceptedStudentdetail= $this->Preference->AcceptedStudent->find('first',array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id'))));
//check preference recording deadline is not passed.
$is_preference_deadline=$this->PreferenceDeadline->find('count',array('conditions'=>array('PreferenceDeadline.college_id'=>$acceptedStudentdetail['College']['id'],'PreferenceDeadline.academicyear LIKE'=>$this->AcademicYear->current_academicyear().'%','PreferenceDeadline.deadline > '=>date("Y-m-d H:i:s"))));
      if($is_preference_deadline) { 
	$this->set($this->request->data);
        if($this->Preference->validates($this->request->data)){
        if(!$this->Preference->isAlreadyEnteredPreference(
$this->request->data['Preference'][1]['accepted_student_id'])){
if($this->Preference->isAllPreferenceDepartmentSelectedDifferent($this->request->data['Preference'])){
	if ($this->Preference->saveAll($this->request->data['Preference'],array('validate'=>'first'))) {
$this->Session->setFlash('<span></span>'.__('The preference has been saved'),'default',array('class'=>'success-box success-message'));
	$this->redirect(array('action' => 'index'));
      } else {
$this->Session->setFlash('<span></span>'.__('The preference could not be saved. Please, try again.', true),'default',array('class'=>'error-box error-message'));
}
} else {
	$this->Session->setFlash('<span></span>'.__('Input Error.Please select different department preference for each preference order.', true),'default',array('class'=>'error-box error-message'));
}
} else {
$this->Session->setFlash('<span></span>'.__('You have already entered your preference. Please edit your preference before the deadline', true),'default',array('class'=>'error-box error-message'));
$this->redirect(array('controller'=>'preferences','action' => 'index'));
}} else {
$this->Session->setFlash('<span></span>'.__('Please enter the input correctly',true),'default',array('class'=>'error-box error-message'));
}
} else {
$this->Session->setFlash('<span></span>'.__('Preference Deadline is passed.You can not recorded your preference. Please ask the college dean for more information', true),
'default',array('class'=>'error-box error-message'));
$this->redirect(array('action' => 'index'));
}
}
		if(empty($this->request->data)){
		      $isAlreadyAdd=$this->Preference->isAlreadyEnteredPreference($this->Auth->user('id'));
				if($isAlreadyAdd){
	echo '<div class="error-box error-message"><span></span>'. __('You have already entered your preference.', true).'</div>';
		$this->redirect(array('action' => 'index'));
			} 	
		}
		$acceptedStudentdetail= $this->Preference->AcceptedStudent->find('first',array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id'))));
		$departments=$this->_participating_department_name($acceptedStudentdetail['College']['id'],null);
		
		if($departments){
$departmentcount=count($departments);
$this->set('departments',$departments);
$this->set('departmentcount',$departmentcount);
		} else {
		   $this->Session->setFlash('<span></span>'.__('Please come back when college announces to fill your preferences.', true),'default',array('class'=>'info-box info-message'));
$this->redirect(array('controller'=>'preferences','action' => 'index'));		       
		}
		$acceptedStudents = $this->Preference->AcceptedStudent->find('first',array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id'))));
	    if(!empty($acceptedStudents)){
		    
		   // foreach($acceptedStudents as $k=>$v){
		        $studentname=$acceptedStudents['AcceptedStudent']['full_name'];
		        $studentnumber=$acceptedStudents['AcceptedStudent']['studentnumber'];
		        $acyear=$acceptedStudents['AcceptedStudent']['academicyear'];
		        $collegename=$acceptedStudents['College']['name'];
		        $college_id=$acceptedStudents['College']['id'];
		        $accepted_student_id=$acceptedStudents['AcceptedStudent']['id'];
		        $this->set(compact('studentname','studentnumber','collegename','college_id','accepted_student_id',
		        'acyear'));
		      //  break;
		    //}
		}
		
	}
   
	function edit($id = null) {
	   
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid preference'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		
		if (empty($this->request->data)) {
			$this->request->data = $this->Preference->find('all',array('conditions'=>array('Preference.user_id'=>$this->Auth->user('id'))));	
		}
		
		$departments=$this->_participating_department_name($this->college_id,null);
		if($departments){
		       $departmentcount=count($departments);
		       $this->set('departments',$departments);
		       $this->set('departmentcount',$departmentcount);
		} else {
		    $this->Session->setFlash('<span></span>'.__('Please first add placement participating departments'),
		    'default',array('class'=>'info-box info-message'));
			        $this->redirect(array('controller'=>'participatingDepartments','action' => 'add'));
		}
		
		$user_id=$this->Auth->user('id');
		$this->set(compact('user_id'));
		$this->set(compact( 'departments'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for preference'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$check_auto_placement_runs=$this->Preference->AcceptedStudent->find('count',
		array('conditions'=>array(
		'AcceptedStudent.placementtype'=>AUTO_PLACEMENT,
		'AcceptedStudent.id IN ( SELECT accepted_student_id from preferences 
		where id='.$id.')')));
		
		//debug($check_auto_placement_runs);
		if ($check_auto_placement_runs>0) {
		   $this->Session->setFlash('<span></span>'.__('Preference can not be deleted. The student has been placed based on this preference.'),
		    'default',array('class'=>'error-box error-message'));
		} else {
		 
			$accepted_student_id=$this->Preference->field('accepted_student_id',
			array('Preference.id'=>$id));
			$preference_ids = $this->Preference->find('list',
			array('conditions'=>array('Preference.accepted_student_id'=>$accepted_student_id),'fields'=>array('Preference.id','Preference.id')));   
			
			if ($this->Preference->deleteAll(array('Preference.id'=>$preference_ids),false)) {
			    $this->Session->setFlash('<span></span>'.__('Preferences deleted'),
			    'default',array('class'=>'success-box success-message'));
			} 
			      
	   }
	   $this->redirect(array('action' => 'index'));
	}
	
	 /**
	 *Method to get the department of the participating department
	 @return array
	 */
	 function _participating_department_name($college_id=null,$department_id=null){
	     $this->loadModel('ParticipatingDepartment');
	     if($department_id){
		     $departments = $this->ParticipatingDepartment->find('all',
		     array('conditions'=>array('ParticipatingDepartment.college_id'=>$college_id,'ParticipatingDepartment.academic_year LIKE'=>$this->AcademicYear->current_academicyear().'%'
		    ,'ParticipatingDepartment.department_id'=>$department_id)));
		 } else {
		 
		  $departments = $this->ParticipatingDepartment->find('all',
		     array('conditions'=>array('ParticipatingDepartment.college_id'=>$college_id,'ParticipatingDepartment.academic_year LIKE'=>$this->AcademicYear->current_academicyear().'%'
		    )));
		 }
		if(!empty($departments)){
		    $participatingdepartmentname=array();
	        foreach($departments as $k=>$v){
	                if(!empty($v['Department'])){
	                   $participatingdepartmentname[$v['Department']['id']]=$v['Department']['name']; 
	                }    
	        }
		    return $participatingdepartmentname;
	    }
	    return false;
	 }
	 /**
	 *Method to edit the preference of the students 
	 @save
	 */
	 function edit_preference($accepted_student_id=null){
	   
	    
	    if(!empty($this->request->data)){
	        $this->set($this->request->data);
	        //input validation
	        if($this->Preference->validates()){  
	             $userCanEdit=null;
	             $college_id=null;
	             $preferenceDetails=$this->Preference->find('first',
	                array('conditions' =>array(
	                        'Preference.user_id'=>$this->Auth->user('id')),
	                        'recursive'=>-1));
	             if($this->role_id != ROLE_STUDENT){
	                $userCanEdit=$this->Preference->find('first',array('conditions'
	             =>array("OR"=>array('Preference.college_id'=>$this->college_id,
	                'Preference.user_id'=>$this->Auth->user('id')))));
	                $college_id=$this->college_id;
	             } else {
	             /*$userCanEdit=$this->Preference->find('first',array('conditions'
	             =>array(
	                "OR"=>array('Preference.user_id'=>$this->Auth->user('id'),
	                'Preference.accepted_student_id'=>$this->student_id))));
	                */
	                $accepted_student_college_id= $this->Preference->AcceptedStudent->find('first',
				  array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id'))));
				    $college_id=$accepted_student_college_id['College']['id'];
	                $userCanEdit=$this->Preference->find('first',
	                array('conditions' =>array(
	                        'Preference.user_id'=>$this->Auth->user('id'))));
	             }
	             
	            // debug($userCanEdit);
	             $accepted_student_id=!empty($userCanEdit)?$userCanEdit['AcceptedStudent']['id']:'';
	           
	            //block user from selecting the same department for each preference
	            if($this->Preference->isAllPreferenceDepartmentSelectedDifferent(
	                $this->request->data['Preference'])){
	               
	                if($userCanEdit){
	                   //check preference recording deadline is not passed.
	                    //check preference deadline
				  $this->loadModel('PreferenceDeadline');
				 
				  $is_preference_deadline=$this->PreferenceDeadline->find('count',
				 array('conditions'=>array('PreferenceDeadline.college_id'=>$college_id,
				 'PreferenceDeadline.academicyear LIKE'=>$this->AcademicYear->current_academicyear().'%',
				 'PreferenceDeadline.deadline > '=>date("Y-m-d H:i:s"))));
				 
				
		               if($is_preference_deadline) { 
	                      
	                        if($this->Preference->saveAll($this->request->data['Preference'])) {
	                             $this->Session->setFlash('<span></span>'.__('The preference has been updated'),
	                             'default',array('class'=>'success-box success-message'));
	                             
	                        } else {
	                             $this->Session->setFlash('<span></span>'.__('The preference could not be updated. Please, try again.'),'default',array('class'=>'error-box error-message'));
	                             $this->redirect(array('action' => 'edit_preference',$accepted_student_id));
	                        }
	                        
	                   } else {
	                        $this->Session->setFlash('<span></span>'.__('Preference Deadline is passed.
				     You can not edit your preference. Please ask the college 

				     dean for more information', true), 'default', array('class'=>'error-box error-message'));
	                     
	                   }
	                   
	                } else {
	                         $this->Session->setFlash("<span></span>".__('You are not allowed to 
	                         edit someone preference. This action will be reported.', true),
	                         'default',array('class'=>'warning-box warning-message'));
	                        
	                }
	                
	            } else {
	                 $this->Session->setFlash("<span></span>".__('Please select different department preference for each preference order.'), 'default', array('class'=>'error-box error-message'));
	                  $this->redirect(array('action' => 'edit_preference',$accepted_student_id));
	            }
	            //$this->redirect(array('action' => 'index'));
	        } else {
	                $this->Session->setFlash("<span></span>".__('Please select department'),'default',array('class'=>'error-box error-message'));
				      $this->redirect(array('action' => 'edit_preference',$accepted_student_id));
	        
	        }
	        $this->redirect(array('action' => 'index'));
			
	    } else {    
	           $college_id=null;
	           if($this->role_id != ROLE_STUDENT) {
	                $college_id=$this->college_id;
	           } else {
	                 $accepted_student_college_id= $this->Preference->AcceptedStudent->find('first',
				  array('conditions'=>array('AcceptedStudent.user_id'=>$this->Auth->user('id'))));
				    $college_id=$accepted_student_college_id['College']['id'];
	           }
	            $preferences=$this->Preference->find('all',array(
	            'conditions'=>array('Preference.accepted_student_id'=>$accepted_student_id,
	            'Preference.college_id'=>$college_id)));
	          
	           $this->request->data['Preference'] = Set::combine($this->Preference->find('all',
	            array('conditions'=>array('Preference.accepted_student_id'=>$accepted_student_id),
	            'order' => array('Preference.preferences_order' => 'ASC') )), '{n}.Preference.id', '{n}.Preference');
	           // debug($preferences);
	           foreach($preferences as $value){
                       foreach($this->request->data['Preference'] as &$data){
                            if($data['department_id']==$value['Preference']['department_id']){
                                    $data['department_name']=$value['Department']['name'];
                               break;
                             }
                             
                       }
                       if(!empty($value['College']['name'])){
                            $this->set('college_name',$value['College']['name']);
                            
                       }
                       if(!empty($value['AcceptedStudent']['full_name'])){
                            $this->set('student_full_name',$value['AcceptedStudent']['full_name']);
                       }
               }
              
                $departments=$this->_participating_department_name($college_id,null);
		        if($departments){
		               $departmentcount=count($departments);
		               $this->set('departments',$departments);
		               $this->set('departmentcount',$departmentcount);
		        } else {
		            $this->Session->setFlash('<span></span>'.__('Please first add placement participating departments'),'default',array('class'=>'info-box info-message'));
			        $this->redirect(array('controller'=>'participatingDepartments','action' => 'add'));
		        }
		
	    }
	 }
	 /**
	 *Get remaining participating department for user choice
	 *set variable
	 */
	 function get_preference($participationg_department_id = null) {
	 
		$this->layout = 'ajax';
		
		$this->set('remaining_departments',$this->_participating_department_name($this->college_id,$participationg_department_id));
	 }
	 
	 
	 function _getParticipatingDepartment($college_id=null,$academic_year=null) {
	     $this->loadModel('ParticipatingDepartment');
	     $departments = $this->ParticipatingDepartment->find('all',
		     array('conditions'=>array('ParticipatingDepartment.college_id'=>$college_id,'ParticipatingDepartment.academic_year LIKE'=>$academic_year.'%'
		    ),'contain'=>array('College','Department')));
		 $department_lists=array();   
		 foreach ($departments as $in=>$value) {
		   $department_lists[$value['ParticipatingDepartment']['department_id']]=$value['Department']['name'];
		 }
	     return  $department_lists;
	 }
	 /**
	 * Import student preferences from excel
	 */
	 /*
	 function import_preference () {
	      if (!empty($this->request->data)
             && is_uploaded_file($this->request->data['AcceptedStudent']['File']['tmp_name'])){
             
           }
	 
	 }
	 */

	 function getStudentPreference($acceptedStudentId) {
          $this->layout = 'ajax';
		  $studentBasic=$this->Preference->AcceptedStudent->find('first',array('conditions'=>array('AcceptedStudent.id'=>$acceptedStudentId),'recursive'=>-1));
		  $studentsPreference=$this->Preference->find('all',array('conditions'=>array('Preference.accepted_student_id'=>$acceptedStudentId),'contain'=>array('AcceptedStudent','Department')));
		  $this->set(compact('studentsPreference','studentBasic'));
	 }
}
?>
