<?php
class TakenPropertiesController extends AppController {

	var $name = 'TakenProperties';

	 var $menuOptions = array(
	         'parent'=>'clearances',
             
             'alias' => array(
                    'index'=>'View properties taken',
                    'add'=>'Maintain Student Taken Properties',
                    'returned_property'=>'Maintain Student Returned Properties',
            )
    );
	
    function beforeFilter () {
        parent::beforeFilter();
        $this->Auth->allow('get_student_combo','get_department_section_combo');
    }
	function index() {
		   // search returned items and display in the form of checkbox with returned date 
	    // save all the check options and display success message
	    //
	    $this->TakenProperty->recursive = 0;
		$this->paginate=array('order'=>'TakenProperty.created DESC');
		
		if ($this->role_id == ROLE_COLLEGE) {
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.college_id'=>$this->college_id)));    
	       
		} else if ($this->role_id == ROLE_DEPARTMENT)  {
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.id'=>$this->department_id)));    
		} else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
		  $colleges = $this->TakenProperty->Student->College->find('list');
		  $departments = $this->TakenProperty->Student->Department->find('list');
		  $offices = $this->TakenProperty->Office->find('list',array('conditions'=>array(
		  'Office.staff_id'=>$this->staff_id
		  )));
		
		}
	
	    $this->__init_search();
		if ($this->Session->read('search_data')) {
		     
		      $this->request->data['Search']=$this->Session->read('search_data');
		       $this->request->data['search']=true;
		}
	      // search 
	     if (!empty($this->request->data) && isset($this->request->data['search'])) { 
	            
	            $options = array();
			   // $options[] = array('Search.returned'=>0);
	            if (!empty($this->request->data['Search']['department_id'])) {
	            
	                 if ($this->role_id == ROLE_COLLEGE) {
	                    /*
	                    $options[] = array('Student.department_id' =>$this->request->data['Search']['department_id'],
	                    'Student.college_id'=>$this->college_id,
	                    'Student.department_id is null '); 
	                     */
	                     $options[] = array(
	                          
	                            "Student.college_id"=>$this->college_id,
	                            "Student.department_id"=>$this->request->data['Search']['department_id']);
	                     
	                                   
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {

	                      $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id)); 
	                        $options[] = array(
	                          
	                            "Student.department_id"=>$this->request->data['Search']['department_id']
	                            ,"TakenProperty.office_id"=> $find_office_id
	                            );
	                 }   
	                 
	             }
	            
	            if (!empty($this->request->data['Search']['college_id'])) {
	          
	                if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                     $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id)); 
	                        $options[] = array(
	                           
	                            "Student.college_id"=>$this->request->data['Search']['college_id'],
	                            "TakenProperty.office_id"=> $find_office_id
	                            );
	                 }
	            
	            }
	            
	            if (!empty($this->request->data['Search']['studentnumber'])) {
	                 
	                 if ($this->role_id == ROLE_COLLEGE) {
	                    $options['OR'][] = array(
	                           
	                            "Student.college_id"=>$this->college_id,
	                            "Student.studentnumber like "=> trim($this->request->data['Search']['studentnumber']).'%'
	                            );
	                 } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                    $options['OR'][] = array(
	                           
	                            "Student.department_id"=>$this->department_id,
	                            "Student.studentnumber like "=> trim($this->request->data['Search']['studentnumber']).'%'
	                            );
	   
	                    
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                     
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                      $options['OR'][] = array(
	                           
	                            'TakenProperty.office_id' =>$find_office_id,
	                            "Student.studentnumber like "=> trim($this->request->data['Search']['studentnumber']).'%'
	                            );
	                     
	                 }   
	  
	              
	              
	              
	              $this->request->data['Search']['studentnumber'] = $this->request->data['Search']['studentnumber'];
	            }
	           
	            if (!empty($this->request->data['Search']['taken_date'])) {
	                  $year=$this->request->data['Search']['taken_date']['year'];
	                  $month = $this->request->data['Search']['taken_date']['month'];
	                  $day = $this->request->data['Search']['taken_date']['day'];
	                  $taken_date = $year.'-'.$month.'-'.$day;
	                 
	                 if ($this->role_id == ROLE_COLLEGE) {
	                     
	                       $options[] = array(
	                           
	                            "Student.college_id"=>$this->college_id,
	                            'Student.department_id is null',
	                            'TakenProperty.taken_date >=' =>$taken_date
	                            );
	                                   
	                 } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                       $options[] = array(
	                           
	                            'Student.department_id'=>$this->department_id,
	                             "Student.college_id is null ",
	                            'TakenProperty.taken_date >= ' =>$taken_date
	                            );
	                     
	                    
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                       
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                
	                       $options[] = array(
	                           
	                            'TakenProperty.office_id' =>$find_office_id,
	                            'TakenProperty.taken_date >= ' =>$taken_date
	                            );
	                 }   
	  
	              
	                  
	                  
	            }
	            
	           if (isset($options['OR']) && !empty($options)) {
	             $options=$options['OR'];
	           }
	           
	           $takenProperties=$this->paginate($options);
	          
	          if (empty($takenProperties)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system that takes properties in the given criteria.'),
				    'default',array('class'=>'info-box info-message'));
	                if ($this->role_id == ROLE_COLLEGE) {
	                    
	                      $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.college_id"=>$this->college_id,
	                            "TakenProperty.department_id is null ");
	                     
	                                   
	                 } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                    
	                      $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.department_id"=>$this->department_id,
	                            "TakenProperty.college_id is null ");
	                     
	                    
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                      
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                      
	                        $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.office_id"=> $find_office_id
	                            );
	                     
	                 }   
		         
		         
		         $takenProperties=$this->paginate($conditions);
	          }
	          
	    } else {
           // $conditions = array();
            if ($this->role_id == ROLE_COLLEGE) {
	                  
	                      $conditions = array(
	                           
	                            "TakenProperty.college_id"=>$this->college_id,
	                            "TakenProperty.department_id is null ");
	                     
	                     
                              
	         } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                     
	                       
	                      $conditions = array(
	                           
	                            "TakenProperty.department_id"=>$this->department_id,
	                            "TakenProperty.college_id is null ");
	                     
	                     
	                    
	         } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                   
	                     $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                     debug($find_office_id);
	                      if (!empty($find_office_id)) {
	                      
	                      	   $conditions = array(
	                           
	                             "TakenProperty.office_id"=> $find_office_id
	                            );
	                      }
	                     
	         }   
		     if (!empty($conditions)) {
		        $takenProperties=$this->paginate($conditions);
		     } else {
		        $takenProperties=array();
		     }
		     
		     if (empty($takenProperties)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system that takes properties .'),
				    'default',array('class'=>'info-box info-message'));
				  //  $this->redirect(array('action' => 'index'));
	           
	          }
			    
	    }
	    
		//debug($takenProperties);
		$this->set(compact('offices','colleges','departments'));
		$this->set('takenProperties',$takenProperties);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid taken property'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('takenProperty', $this->TakenProperty->read(null, $id));
	}

	function add() {
	      if (!empty($this->request->data) && isset($this->request->data['saveTakenProperties'])) {
			$this->TakenProperty->create();
			$student_id=null;
			foreach ($this->request->data['TakenProperty'] as $in=>&$v) {
			        if (isset($v['student_id']) && !empty($v['student_id'])) {
			          $student_id=$v['student_id']; 
			        } else {
			            $v['student_id']=$student_id;
			        }
			       
			 }
			if ($this->role_id == ROLE_DEPARTMENT) {
			   //unset($this->request->data['TakenProperty']['college_id']);
			   //unset($this->request->data['TakenProperty']['office_id']);
			   foreach ($this->request->data['TakenProperty'] as $in=>&$v) {
			        $v['department_id']=$this->department_id;
			       
			   }
			  // $this->request->data['TakenProperty']['department_id']=$this->department_id;
			} else if ($this->role_id == ROLE_COLLEGE) {
			   /*unset($this->request->data['TakenProperty']['department_id']);
			   unset($this->request->data['TakenProperty']['office_id']);
			   $this->request->data['TakenProperty']['college_id']=$this->college_id;
			   */
			   foreach ($this->request->data['TakenProperty'] as $in=>&$v) {
			        $v['college_id']=$this->college_id;
			       
			   }
			} else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
			   /*  unset($this->request->data['TakenProperty']['college_id']);
			     unset($this->request->data['TakenProperty']['department_id']);
			    */
			     $office_id = $this->TakenProperty->Office->field('id',array(
			     'Office.staff_id'=>$this->staff_id));
			     
			     foreach ($this->request->data['TakenProperty'] as $in=>&$v) {
			        $v['office_id']=$office_id;
			       
			     }
			}
			
			if ($this->TakenProperty->saveAll($this->request->data['TakenProperty'],array('validate'=>'first'))) {
				$this->Session->setFlash('<span></span>'.__('The taken property has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The taken property could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				
				$this->request->data['continue']=true;
				$student_number=$this->TakenProperty->Student->field('studentnumber',
			                array('id'=>trim($student_id)));
				$this->request->data['Search']['studentID']=$student_number;
			}
		}
		
		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
		       
		         $everythingfine=false;
			     if (!empty($this->request->data['Search']['studentID'])) {
			            $check_id_is_valid=$this->TakenProperty->Student->
			            find('count',
			            array('conditions'=>array('Student.studentnumber'=>
			            trim($this->request->data['Search']['studentID']))));
			            $studentIDs=1;
			            
			             if ($check_id_is_valid>0) {
			                 $everythingfine=true;
			                $student_id=$this->TakenProperty->Student->field('id',
			                array('studentnumber'=>trim($this->request->data['Search']['studentID'])));
			                $student_section_exam_status=$this->TakenProperty->Student->
	                get_student_section($student_id);
		                    $this->set(compact('student_section_exam_status'));
		
			                $this->set(compact('studentIDs'));
			             } else {
			                $this->Session->setFlash('<span></span> '.__('The provided student number is not valid.'),'default',array('class'=>'error-box error-message'));      
			             }
			             
			     } else {
			          $this->Session->setFlash('<span></span> '.__('Please provide student number to maintain taken properties.'),'default',array('class'=>'error-box error-message'));  
			    
			     }
			
		}
		
	
	    /*
		if (!empty($this->request->data)) {
			$this->TakenProperty->create();
			if ($this->role_id == ROLE_DEPARTMENT) {
			   unset($this->request->data['TakenProperty']['college_id']);
			   unset($this->request->data['TakenProperty']['office_id']);
			   $this->request->data['TakenProperty']['department_id']=$this->department_id;
			} else if ($this->role_id == ROLE_COLLEGE) {
			   unset($this->request->data['TakenProperty']['department_id']);
			   unset($this->request->data['TakenProperty']['office_id']);
			   $this->request->data['TakenProperty']['college_id']=$this->college_id;
			} else if ($this->role_id == ROLE_CLEARANCE) {
			     unset($this->request->data['TakenProperty']['college_id']);
			     unset($this->request->data['TakenProperty']['department_id']);
			    
			}
			
			if ($this->TakenProperty->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The taken property has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The taken property could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		
		
		$students = $this->TakenProperty->Student->find('list',array('fields'=>array('id','full_name')));
		
		if ($this->role_id == ROLE_DEPARTMENT) {
		   
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.id'=>$this->department_id)));
		   $sections_detail = $this->TakenProperty->Student->Section->find('all',
			        array('conditions'=>array('Section.department_id'=>$this->department_id,
			        'Section.archive'=>0),'contain'=>array('Program'=>array('id','name'),'YearLevel'=>array('id','name'),'ProgramType'=>array('id',
			        'name')),'fields'=>array('id','name','program_id','year_level_id')));
	
		    foreach ($sections_detail as $seindex=>$secvalue ) {
		       //if ($student_section_id != $secvalue['Section']['id'] ) {
		        $sections[$secvalue['Program']['name']][$secvalue['Section']['id']]=$secvalue['Section']['name'].'('.$secvalue['YearLevel']['name'].')';
		        //}
		     }
		    
		    $this->set(compact('sections'));
		    
		} else if ($this->role_id == ROLE_COLLEGE) {
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.college_id'=>$this->college_id)));
		    
	       
		} else if ($this->role_id == ROLE_CLEARANCE) {
		  $colleges = $this->TakenProperty->Student->College->find('list');
		  $offices = $this->TakenProperty->Office->find('list',array('conditions'=>array(
		  'Office.staff_id'=>$this->staff_id
		  )));
		
		}
		
		$this->set(compact('offices','colleges','departments'));
	    */
	    if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
		 
		  $offices = $this->TakenProperty->Office->find('list',array('conditions'=>array(
		  'Office.staff_id'=>$this->staff_id
		  )));
		
		}
		$this->set(compact('student_section_exam_status','offices'));
		
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid taken property'));
			return $this->redirect(array('action' => 'index'));
		}
		 if (!empty($this->request->data) && isset($this->request->data['saveTakenProperties'])) {
			$this->TakenProperty->create();
			if ($this->role_id == ROLE_DEPARTMENT) {
			   unset($this->request->data['TakenProperty']['college_id']);
			   unset($this->request->data['TakenProperty']['office_id']);
			   $this->request->data['TakenProperty']['department_id']=$this->department_id;
			} else if ($this->role_id == ROLE_COLLEGE) {
			   unset($this->request->data['TakenProperty']['department_id']);
			   unset($this->request->data['TakenProperty']['office_id']);
			   $this->request->data['TakenProperty']['college_id']=$this->college_id;
			} else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
			     unset($this->request->data['TakenProperty']['college_id']);
			     unset($this->request->data['TakenProperty']['department_id']);
			       $office_id = $this->TakenProperty->Office->field('id',array(
			     'Office.staff_id'=>$this->staff_id));
			     $this->request->data['TakenProperty']['office_id']=$office_id;
			    
			}
			if ($this->TakenProperty->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The taken property has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The taken property could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				if (!empty($this->request->data['TakenProperty']['student_id'])) {
		           $student_section_exam_status=$this->TakenProperty->Student->get_student_section($this->request->data['TakenProperty']['student_id']);
		           
		        }
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->TakenProperty->read(null, $id);
			$student_section_exam_status=$this->TakenProperty->Student->get_student_section($this->request->data['TakenProperty']['student_id']);
		   
		}
		
		$this->set(compact('student_section_exam_status'));
		
		
		/*$students = $this->TakenProperty->Student->find('list',array('fields'=>array('id','full_name')));
		
		if ($this->role_id == ROLE_DEPARTMENT) {
		   
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.id'=>$this->department_id)));
		   $sections_detail = $this->TakenProperty->Student->Section->find('all',
			        array('conditions'=>array('Section.department_id'=>$this->department_id,
			        'Section.archive'=>0),'contain'=>array('Program'=>array('id','name'),'YearLevel'=>array('id','name'),'ProgramType'=>array('id',
			        'name')),'fields'=>array('id','name','program_id','year_level_id')));
	
		    foreach ($sections_detail as $seindex=>$secvalue ) {
		       //if ($student_section_id != $secvalue['Section']['id'] ) {
		        $sections[$secvalue['Program']['name']][$secvalue['Section']['id']]=$secvalue['Section']['name'].'('.$secvalue['YearLevel']['name'].')';
		        //}
		     }
		    
		    $this->set(compact('sections'));
		    
		} else if ($this->role_id == ROLE_COLLEGE) {
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.college_id'=>$this->college_id)));
		    
	       
		} else if ($this->role_id == ROLE_CLEARANCE) {
		  $colleges = $this->TakenProperty->Student->College->find('list');
		  $offices = $this->TakenProperty->Office->find('list',array('conditions'=>array(
		  'Office.staff_id'=>$this->staff_id
		  )));
		
		}
		
		$this->set(compact('offices','colleges','departments'));
		if (empty($this->request->data)) {
			$this->request->data = $this->TakenProperty->read(null, $id);
		}
		$students = $this->TakenProperty->Student->find('list',array('fields'=>array('id',
		'full_name')));
		$this->set(compact('students', 'offices'));
	    */
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for taken property'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->TakenProperty->delete($id)) {
			$this->Session->setFlash('<span></span>'.__('Taken property deleted'),
			'default',array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Taken property was not deleted'),
		'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
	
	function returned_property () {
	    // search returned items and display in the form of checkbox with returned date 
	    // save all the check options and display success message
	    //
	    $this->TakenProperty->recursive = 0;
		$this->paginate=array('order'=>'TakenProperty.created DESC');
		
		if ($this->role_id == ROLE_COLLEGE) {
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.college_id'=>$this->college_id)));    
	       
		} else if ($this->role_id == ROLE_DEPARTMENT)  {
		    $departments = $this->TakenProperty->Student->Department->find('list',
		    array('conditions'=>array('Department.id'=>$this->department_id)));    
		} else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
		  $colleges = $this->TakenProperty->Student->College->find('list');
		  $departments = $this->TakenProperty->Student->Department->find('list');
		  $offices = $this->TakenProperty->Office->find('list',array('conditions'=>array(
		  'Office.staff_id'=>$this->staff_id
		  )));
		
		}
		$this->__init_search();
		if ($this->Session->read('search_data')) {
		     
		      $this->request->data['Search']=$this->Session->read('search_data');
		       $this->request->data['search']=true;
		}
		
	      // search 
	     if (!empty($this->request->data) && isset($this->request->data['search'])) { 
	            $options = array();
			   // $options[] = array('Search.returned'=>0);
	            if (!empty($this->request->data['Search']['department_id'])) {
	            
	                 if ($this->role_id == ROLE_COLLEGE) {
	                    /*
	                    $options[] = array('Student.department_id' =>$this->request->data['Search']['department_id'],
	                    'Student.college_id'=>$this->college_id,
	                    'Student.department_id is null '); 
	                     */
	                     $options[] = array(
	                            "TakenProperty.returned" =>0,
	                            "Student.college_id"=>$this->college_id,
	                            "Student.department_id"=>$this->request->data['Search']['department_id']);
	                     
	                                   
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {

	                      $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id)); 
	                        $options[] = array(
	                            "TakenProperty.returned" =>0,
	                            "Student.department_id"=>$this->request->data['Search']['department_id']
	                            ,"TakenProperty.office_id"=> $find_office_id
	                            );
	                 }   
	                 $this->request->data['Search']['department_id'] = $this->request->data['Search']['department_id'];
	             }
	            
	            if (!empty($this->request->data['Search']['college_id'])) {
	          
	                if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION ) {
	                     $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id)); 
	                        $options[] = array(
	                            "TakenProperty.returned" =>0,
	                            "Student.college_id"=>$this->request->data['Search']['college_id'],
	                            "TakenProperty.office_id"=> $find_office_id
	                            );
	                 }
	            
	            }
	            
	            if (!empty($this->request->data['Search']['studentnumber'])) {
	                 
	                 if ($this->role_id == ROLE_COLLEGE) {
	                    $options['OR'][] = array(
	                            "TakenProperty.returned" =>0,
	                            "Student.college_id"=>$this->college_id,
	                            "Student.studentnumber like "=> trim($this->request->data['Search']['studentnumber']).'%'
	                            );
	                 } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                    $options['OR'][] = array(
	                            "TakenProperty.returned" =>0,
	                            "Student.department_id"=>$this->department_id,
	                            "Student.studentnumber like "=> trim($this->request->data['Search']['studentnumber']).'%'
	                            );
	   
	                    
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                     
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                      $options['OR'][] = array(
	                            "TakenProperty.returned" =>0,
	                            
	                            'TakenProperty.office_id' =>$find_office_id,
	                            "Student.studentnumber like "=> trim($this->request->data['Search']['studentnumber']).'%'
	                            );
	                     
	                 }   
	  
	              
	              
	              
	              $this->request->data['Search']['studentnumber'] = $this->request->data['Search']['studentnumber'];
	            }
	           if (!empty($this->request->data['Search']['taken_date'])) {
	                  $year=$this->request->data['Search']['taken_date']['year'];
	                  $month = $this->request->data['Search']['taken_date']['month'];
	                  $day = $this->request->data['Search']['taken_date']['day'];
	                  $taken_date = $year.'-'.$month.'-'.$day;
	                
	                 if ($this->role_id == ROLE_COLLEGE) {
	                     
	                       $options[] = array(
	                            "TakenProperty.returned" =>0,
	                            "Student.college_id"=>$this->college_id,
	                            'Student.department_id is null',
	                            'TakenProperty.taken_date >= ' =>$taken_date
	                            );
	                                   
	                 } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                      
	                       $options[] = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.department_id" =>$this->department_id,
	                            "TakenProperty.college_id is null",
	                            'TakenProperty.taken_date >= ' =>$taken_date
	                            );
	                     
	                    
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                       
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                
	                       $options[] = array(
	                            "TakenProperty.returned" =>0,
	                            'TakenProperty.office_id' =>$find_office_id,
	                            'TakenProperty.taken_date >= ' =>$taken_date
	                            );
	                 }   
	  
	              
	                  
	                  
	            }
	           if (isset($options['OR']) && !empty($options['OR'])) {
	               $options=$options['OR'];
	           }
	           $takenProperties=$this->paginate($options);
	           
	          
	          if (empty($takenProperties)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system that takes properties in the given criteria.'),
				    'default',array('class'=>'info-box info-message'));
	                if ($this->role_id == ROLE_COLLEGE) {
	                    
	                      $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.college_id"=>$this->college_id,
	                            "TakenProperty.department_id is null ");
	                     
	                                   
	                 } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                    
	                      $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.department_id"=>$this->department_id,
	                            "TakenProperty.college_id is null ");
	                     
	                    
	                 } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                      
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                      
	                        $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.office_id"=> $find_office_id
	                            );
	                     
	                 }   
		         
		         
		         $takenProperties=$this->paginate($conditions);
	          }
	          
	    } else {
            //$conditions = array();
            if ($this->role_id == ROLE_COLLEGE) {
	                  
	                      $conditions= array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.college_id"=>$this->college_id,
	                            "TakenProperty.department_id is null ");
	                     
	                     
                              
	         } else if ($this->role_id == ROLE_DEPARTMENT ) {
	                     
	                       
	                      $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.department_id"=>$this->department_id,
	                            "TakenProperty.college_id is null ");
	                     
	                     
	                    
	         } else if ($this->role_id == ROLE_CLEARANCE || $this->role_id == ROLE_ACCOMODATION) {
	                   
	                        $find_office_id = $this->TakenProperty->Office->field('id',
	                     array('Office.staff_id'=>$this->staff_id));
	                      
	                        $conditions = array(
	                            "TakenProperty.returned" =>0,
	                            "TakenProperty.office_id"=> $find_office_id
	                            );
	                     
	         }   
		   
		    $takenProperties=$this->paginate($conditions);
		     if (empty($takenProperties)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system that takes properties .'),
				    'default',array('class'=>'info-box info-message'));
				  //  $this->redirect(array('action' => 'index'));
	           
	          }
			    
	    }
		
		if (!empty($this->request->data) && isset($this->request->data['update'])) {
		       if ($this->TakenProperty->saveAll($this->request->data['TakenProperty'],
			        array('validate'=>false))) {
			        $this->Session->setFlash('<span></span>'.
			        __('The taken property return has been updated.'),
				'default',
				array('class'=>'success-box success-message'));
	                 $this->redirect(array('action'=>'returned_property'));		   
			   } else {
			     
			         $this->Session->setFlash('<span></span>'.
			         __('The taken property could not be saved. Please, try again.'),
			         'default',
			         array('class'=>'error-box error-message'));
			   }
			      
		}
		//debug($takenProperties);
		$this->set(compact('offices','colleges','departments'));
		$this->set('takenProperties',$takenProperties);
	}
	
	function __init_search() {
        // We create a search_data session variable when we fill any criteria 
        // in the search form.
                if(!empty($this->request->data['Search'])){
                       
                            $search_session = $this->request->data['Search'];
                           // Session variable 'search_data'
                            $this->Session->write('search_data', $search_session);
                        
                } else {

                	$search_session = $this->Session->read('search_data');
                	$this->request->data['Search'] = $search_session;

                } 

     }
	
	
}
