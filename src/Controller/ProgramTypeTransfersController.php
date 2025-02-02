<?php
class ProgramTypeTransfersController extends AppController {

	var $name = 'ProgramTypeTransfers';
    var $menuOptions = array(
             'parent' => 'transfers',
	    
	      'exclude' => array('notify_program_transfer_to_department'),
             'alias' => array(
                    'index'=>'View transfer',
                    'add'=>'Transfer',
            )
    );
     var $components =array('AcademicYear');   
    
    function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();
        $this->set(compact('acyear_array_data','defaultacademicyear'));
	}
	
	function index() {
		//$this->ProgramTypeTransfer->recursive = 0;
		$this->paginate = array('contain'=>array('ProgramType','Student' => array('Department','College', 'ProgramType', 'Program')));
	    if (!empty($this->request->data) && isset($this->request->data['viewProgramTransfer'])) { 
	           
	            $options = array();
			  
	            if (!empty($this->request->data['ProgramTypeTransfer']['department_id'])) {
	               $options [] = array(
	                    'Student.department_id'=>$this->request->data['ProgramTypeTransfer']['department_id']
	               
	                 );
	            }
	            
	            if (!empty($this->request->data['ProgramTypeTransfer']['college_id'])) {
	                 $options [] = array(
	                    'Student.college_id'=>$this->request->data['ProgramTypeTransfer']['college_id']
	               
	                 );
	                
	            }
	          
	           
	            if (!empty($this->request->data['ProgramTypeTransfer']['studentnumber'])) {
	               unset($options);
	               $options [] = array(
	                    'Student.studentnumber like '=>$this->request->data['ProgramTypeTransfer']['studentnumber'].'%'
	               );
			
	            }
	            
	         
	          $programTypeTransfers=$this->paginate($options);
	          
	          if (empty($programTypeTransfers)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system in the given criteria.'),
				    'default',array('class'=>'info-box info-message'));
			  }
	     } else {
	        $programTypeTransfers=$this->paginate();
	     }
		$colleges=$this->ProgramTypeTransfer->Student->College->find('list');
		//$this->set('programTypeTransfers', $this->paginate());
		$programTypes=$this->ProgramTypeTransfer->Student->ProgramType->find('list');
		$this->set(compact('colleges','programTypeTransfers','programTypes'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid program type transfer'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('programTypeTransfer', $this->ProgramTypeTransfer->read(null, $id));
	}

	function add() {
		/*if (!empty($this->request->data)) {
			$this->ProgramTypeTransfer->create();
			if ($this->ProgramTypeTransfer->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The program type transfer has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The program type transfer could not be saved. Please, try again.'));
			}
		}
		$students = $this->ProgramTypeTransfer->Student->find('list');
		*/
		 if (!empty($this->request->data) && isset($this->request->data['saveTransfer'])) {
			$this->ProgramTypeTransfer->create();
			if ($this->ProgramTypeTransfer->getProgramTransferDate($this->request->data) && 
			isset($this->request->data['ProgramTypeTransfer']['student_id']) ) {
			    debug($this->request->data);
			    if ($this->ProgramTypeTransfer->save($this->request->data['ProgramTypeTransfer'])) {
			       	ClassRegistry::init('Student')->id=$this->request->data['ProgramTypeTransfer']['student_id'];
			        ClassRegistry::init('Student')->saveField('program_type_id',
			        $this->request->data['ProgramTypeTransfer']['program_type_id']);
			        
			        // make the student section less and allow department to put him 
			        // in the new section.
				    $this->Session->setFlash('<span></span>'.
				    __('The program type transfer has been saved'),
				    'default',array('class'=>'success-box success-message'));
				    $this->redirect(array('action' => 'index'));
				
			    } else {
				    $this->Session->setFlash('<span></span>'.__('The program type transfer could not be saved. Please, try again..'),'default',array('class'=>'error-box error-message'));
				
				    $this->request->data['continue']=true;
				    $student_number=$this->ProgramTypeTransfer->Student->field('studentnumber',
			                    array('id'=>trim($this->request->data['ProgramTypeTransfer']['student_id'])));
				    $this->request->data['ProgramTypeTransfer']['studentID']=$student_number;
			    }
	        } else {
			         $error=$this->ItemOrderStatus->invalidFields();
			        if (isset($error['error'])) {
			            $this->Session->setFlash('<span></span>'.__($error['error'][0]),'default',array('class'=>'error-box error-message'));
			        }
			        
			        $status_summery=$error['already_recorded_status'];
			        $this->set(compact('status_summery'));   
			 }
			
		}
		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
		       
		         $everythingfine=false;
			     if (!empty($this->request->data['ProgramTypeTransfer']['studentID'])) {
			            $check_id_is_valid=$this->ProgramTypeTransfer->Student->
			            find('count',
			            array('conditions'=>array('Student.studentnumber'=>
			            trim($this->request->data['ProgramTypeTransfer']['studentID']))));
			            $studentIDs=1;
			            
			             if ($check_id_is_valid>0) {
			                 $everythingfine=true;
			                $student_id=$this->ProgramTypeTransfer->Student->field('id',
			                array('studentnumber'=>trim($this->request->data['ProgramTypeTransfer']['studentID'])));
			                $student_section_exam_status=$this->ProgramTypeTransfer->Student->
	                get_student_section($student_id);
	               
		                    $this->set(compact('student_section_exam_status'));
		
			                $this->set(compact('studentIDs','student_id','student_section_exam_status'));
			             } else {
			                $this->Session->setFlash('<span></span> '.__('The provided student number is not valid.'),'default',array('class'=>'error-box error-message'));      
			             }
			     } else {
			          $this->Session->setFlash('<span></span> '.__('Please provide student number to maintain student program transfer.'),'default',array('class'=>'error-box error-message'));  
			    
			     }
			
		}
		
		
		$programTypes = $this->ProgramTypeTransfer->ProgramType->find('list');
		$this->set(compact('programTypes'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid program type transfer'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ProgramTypeTransfer->save($this->request->data)) {
				$this->Session->setFlash(__('The program type transfer has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The program type transfer could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ProgramTypeTransfer->read(null, $id);
		}
		$students = $this->ProgramTypeTransfer->Student->find('list');
		$programTypes = $this->ProgramTypeTransfer->ProgramType->find('list');
		$this->set(compact('students', 'programTypes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for program type transfer'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->ProgramTypeTransfer->delete($id)) {
			$this->Session->setFlash('<span></span>'.__('Program type transfer deleted'),
			'default',array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Program type transfer was not deleted'),
		'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
	
	function notify_program_transfer_to_department () {
	                $options = array();
			 
	                $options['conditions'][] = array(
	                    'Student.department_id'=>$this->department_id,
	                    
	               
	               );
	               $options['conditions'][] = array(
	                    'ProgramTypeTransfer.academic_year'=> $this->AcademicYear->current_academicyear(),
	                    
	               
	               );
	               
	              
	 }
}
