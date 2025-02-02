<?php 
class PlacementController extends AppController {
            var $name = "Placement";
            var $uses = array();
            var $menuOptions = array(
                'weight'=>-1000,
                    'exclude'=>array('index')
            );
           
            var $components = array('AcademicYear');
    
            function beforeRender() {
               
                $current_acyear=$this->AcademicYear->current_academicyear();
                $this->set(compact('current_acyear'));
                
	        }
            function beforeFilter(){
                     parent::beforeFilter();
                    
            }
                   
            
            function index() {
                   
                          /* 
                           $this->set('schedule', ClassRegistry::init('Schedule')->getCourseScheduleOfTheDay());
                           $this->set('grades', ClassRegistry::init('Grade')->getRecentGrade());
                           $this->set('notice', ClassRegistry::init('Notice')->getNotices());
                           $this->set('attendaces', ClassRegistry::init('Attendance')->reportMissingAttendance()); 
                           
                           */   
                         //  $this->loadModel('AcceptedStudent');
                         //  $this->set('recentAcceptedStudents', $this->AcceptedStudent->paginate());
                          if($this->role_id == ROLE_COLLEGE){
                           $recentAcceptedStudents= ClassRegistry::init('AcceptedStudent')->getRecentAcceptedStudent($this->college_id,$this->AcademicYear->current_academicyear());
                           if(!empty($recentAcceptedStudents)){
                              $this->set('recentAcceptedStudents',$recentAcceptedStudents);
                           } else {
                                 $this->redirect(array('controller'=>'acceptedStudents',
                                 'action'=>'index'));
                           }
                           
                           
                          }
                          if($this->role_id == ROLE_REGISTRAR){
                             $this->set('recentAcceptedStudents', ClassRegistry::init('AcceptedStudent')->find('all',array("conditions"=>array("AcceptedStudent.academicyear"=>$this->AcademicYear->current_academicyear()),
                             'limit'=>500)));
                             $this->set('Current Academic Year',$this->AcademicYear->current_academicyear());
                          }  
                         
                          if($this->role_id == ROLE_STUDENT){
                              
                               $autoplacedresult=ClassRegistry::init('AcceptedStudent')->find('first',
                               array('conditions'=>array('AcceptedStudent.user_id'=> $this->Auth->user('id')),'contain'=>array('Preference')));
                               //debug($autoplacedresult);
                               //debug($autoplacedresult['AcceptedStudent']['department_id']);
                               if(empty($autoplacedresult['AcceptedStudent']['department_id'])){
                                if(!empty($autoplacedresult['Preference'])){
                                    $this->redirect(array('controller'=>'preferences','action'=>'index'));
                								} else {
                                                    $this->redirect(array('controller'=>'preferences','action'=>'student_record_preference'));
                								}
                                
							debug($autoplacedresult);
                                   
                               } else {
                                 $department_name=ClassRegistry::init('Department')->field('Department.name',
                                 array('id'=>$autoplacedresult['AcceptedStudent']['department_id']));
                                 $this->set('autoplacedresult',$autoplacedresult);
                                 $this->set('placed_department_name',$department_name);
                               
                               }
                          }
                          
                           if($this->role_id == ROLE_DEPARTMENT){
                           $recentAcceptedStudents= ClassRegistry::init('AcceptedStudent')->find('all',array('conditions'=>array('AcceptedStudent.college_id'=>$this->college_id,
                           'AcceptedStudent.academicyear'=>$this->AcademicYear->current_academicyear(),'AcceptedStudent.department_id'=>$this->department_id)));
                           if(!empty($recentAcceptedStudents)){
                              $this->set('recentAcceptedStudents',$recentAcceptedStudents);
                           } else {
                                 $this->redirect(array('controller'=>'acceptedStudents',
                                 'action'=>'index'));
                           }
                           
                           
                          }
                          
                          
                          
            }
    
    }
?>
