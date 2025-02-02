<?php
class DisciplinesController extends AppController {

	 var $name = 'Disciplines';
         var $menuOptions = array(
            'parent' => 'policy',
             'alias' => array(
                    'index'=>'View Discipline',
                    'add'=>'Add Discipline Case',
                )
         );
         
         
           function __init_search() {
                        // We create a search_data session variable when we fill any criteria 
                        // in the search form.
                        if(!empty($this->request->data['Discipline'])){
                               
                                    $search_session = $this->request->data['Discipline'];
                                   // Session variable 'search_data'
                                    $this->Session->write('search_data', $search_session);
                                
                        } else {

                        	$search_session = $this->Session->read('search_data');
                        	$this->request->data['Discipline'] = $search_session;
                        } 

        }
	
	function index() {
	        /*
		$this->Discipline->recursive = 0;
		$this->set('disciplines', $this->paginate());
	        */
	        
	        $this->paginate = array('order'=>array('Discipline.created DESC '));
		$this->__init_search();
		if ($this->Session->read('search_data')) {
		  $this->request->data['viewDiscipline']=true;
		}
		if (!empty($this->request->data) && isset($this->request->data['viewDiscipline'])) { 
	           
	            $options = array();
	            if (!empty($this->request->data['Discipline']['department_id'])) {
	               $options [] = array(
	                    'Student.department_id'=>$this->request->data['Discipline']['department_id']
	               
	                 );
	            }
	            
	            if (!empty($this->request->data['Discipline']['college_id'])) {
	                 $options [] = array(
	                    'Student.college_id'=>$this->request->data['Discipline']['college_id']
	               
	                 );
	                
	            }
	            if (!empty($this->request->data['Discipline']['discipline_date_to'])) {
	               $options [] = array(
	                    'Discipline.discipline_taken_date >= \''.
	                    $this->request->data['Discipline']['discipline_date_from']['year'].'-'.
	                    $this->request->data['Discipline']['discipline_date_from']['month'].'-'.
	                    $this->request->data['Discipline']['discipline_date_from']['day'].'\'',
	                    
	                     'Discipline.discipline_taken_date <= \''.
	                    $this->request->data['Discipline']['discipline_date_to']['year'].'-'.
	                    $this->request->data['Discipline']['discipline_date_to']['month'].'-'.
	                    $this->request->data['Discipline']['discipline_date_to']['day'].'\'',
	               
	               );
			
	            }
	           
	            if (!empty($this->request->data['Discipline']['studentID'])) {
	                unset($options);
	                $checkIDValid=$this->Discipline->Student->
			            find('first',
			            array('conditions'=>array('Student.studentnumber'=>
			            trim($this->request->data['Discipline']['studentID']))));
			            
			if (count($checkIDValid)>0) {
	                       $options [] = array(
	                            'Discipline.student_id'=>$checkIDValid['Student']['id']
	                       );
	               } else {
	                    $this->Session->setFlash('<span></span> '.__('The provided student number is not valid.'),'default',array('class'=>'error-box error-message'));      
	               }
			
	            }
	            
	         
	          $disciplines=$this->paginate($options);
	          
	          if (empty($disciplines)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system that have discipline case in the given criteria.'),
				    'default',array('class'=>'info-box info-message'));
		  }
	     } 
	    if (!empty($this->request->data['Discipline']['college_id'])) {
		      if (!empty($this->department_ids)) {
		          $departments = $this->Discipline->Student->Department->find('list',
		        array('conditions'=>array('Department.college_id'=>
		        $this->request->data['Discipline']['college_id'],'Department.id'=>$this->department_ids
		        )));
		      }
		       
		      $this->set(compact('departments'));
		        
	     }
	    $colleges=$this->Discipline->Student->College->find('list');
	    $this->set(compact('disciplines'));
	    $this->set(compact('colleges'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid discipline'));
			return $this->redirect(array('action' => 'index'));
		}
		$discipline=$this->Discipline->find('first', array('conditions'=>
		array('Discipline.id'=>$id),'recursive'=>-1));
		$student_section_exam_status=$this->Discipline->Student->
	                    get_student_section($discipline['Discipline']['student_id']);
		$this->set(compact('student_section_exam_status','discipline'));
	}

	function add() {
	       
	         if (!empty($this->request->data) && isset($this->request->data['saveDisplinceCase'])) {
			     /*
			        $discipline_date_from=$this->request->data['Discipline']['discipline_taken_date']['year'].'-'.$this->request->data['Discipline']['discipline_taken_date']['month'].'-'.$this->request->data['Discipline']['discipline_taken_date']['day'];
		                unset( $this->request->data['Discipline']['discipline_taken_date']);
		                $this->request->data['Discipline']['discipline_taken_date']=$discipline_date_from;
		           */     
		  
		       
		        if ($this->Discipline->duplication($this->request->data)==0) {	
		           
		            $this->Discipline->create();
		           
			    if ($this->Discipline->save($this->request->data)) {
				    $this->Session->setFlash('<span></span>'.__('The discipline  has been saved'),
				    'default',array('class'=>'success-box success-message'));
				   $this->redirect(array('action' => 'index'));
				
			    } else {
			               
				    $this->Session->setFlash('<span></span>'.__('The discipline could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				
				    $this->request->data['continue']=true;
				    $student_number=$this->Discipline->Student->field('studentnumber',
			                    array('id'=>trim($this->request->data['Discipline']['student_id'])));
				    $this->request->data['Search']['studentID']=$student_number;
			    }
			 } else {
			      $this->Session->setFlash('<span></span>'.__('You have already recorded  discipline for the selected student.'),'default',array('class'=>'error-box error-message'));
			      $this->request->data['continue']=true;
				    $student_number=$this->Discipline->Student->field('studentnumber',
			                    array('id'=>trim($this->request->data['Discipline']['student_id'])));
				    $this->request->data['Discipline']['studentID']=$student_number;
			 }
		}
		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
		       
		         $everythingfine=false;
		        
			     if (!empty($this->request->data['Search']['studentID'])) {
			            $check_id_is_valid=$this->Discipline->Student->
			            find('count',
			            array('conditions'=>array('Student.studentnumber'=>
			            trim($this->request->data['Search']['studentID']))));
			            $studentIDs=1;
			            
			             if ($check_id_is_valid>0) {
			                 $everythingfine=true;
			                 
			                 $student_id=$this->Discipline->Student->field('id',
			                array('studentnumber'=>trim($this->request->data['Search']['studentID'])));
			                 $student_section_exam_status=$this->Discipline->Student->
	                    get_student_section($student_id);
		                        $this->set(compact('student_section_exam_status','studentIDs'));
		                   
			                   
			             } else {
			                $this->Session->setFlash('<span></span> '.__('The provided student number is not valid.'),'default',array('class'=>'error-box error-message'));      
			             }
			     } else {
			          $this->Session->setFlash('<span></span> '.__('Please provide student number to maintain student discipline case.'),'default',array('class'=>'error-box error-message'));  
			    
			     }
			
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid discipline'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->Discipline->save($this->request->data)) {
				$this->Session->setFlash(__('The discipline has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The discipline could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->Discipline->read(null, $id);
		}
		$students = $this->Discipline->Student->find('list');
		$this->set(compact('students'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for discipline'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		// read deletion is possible in 24 hours
		$today=strtotime(date('Y-m-d 03:19:14'));
		$record_created=strtotime ($this->Discipline->field('created',array('Discipline.id'=>$id)));
		$days =($today-$record_created)/86400; // in day
		if ($days<30) {
		        if ($this->Discipline->delete($id)) {
			        $this->Session->setFlash(__('Discipline deleted'),
			        'default',array('class'=>'success-box success-message'));
			        $this->redirect(array('action'=>'index'));
		        } else {
		                $this->Session->setFlash(__('Discipline was not deleted'),
		                'default',array('class'=>'error-box error-message'));
		        }
		} else {
		        $this->Session->setFlash('<span></span>'.__('Discipline case can not be  deleted. Deletion time  expired.'),
		    'default',array('class'=>'error-box error-message')); 
		}
		
		return $this->redirect(array('action' => 'index'));
	}
}
