<?php
App::import('Vendor', 'PhpExcelReader/Excel/reader');
class ApplicablePaymentsController extends AppController {

	public $name = 'ApplicablePayments';
    public $menuOptions = array(
             'parent'=>'costShares',
             'alias' => array(
                    'index'=>'View Payment',
                    'add'=>'Add Applicable Payment',
                    
            )
    );
    
     public $components =array('AcademicYear');
     public function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();
        $this->set(compact('acyear_array_data','defaultacademicyear'));
      
	}
	 public function beforeFilter () {
	    parent::beforeFilter();
	    $this->Auth->allow('mass_applicablepayment_import');
	 }
	 public function index() {
		/*$this->ApplicablePayment->recursive = 0;
		$this->paginate = array('contain'=>array('Student' => array('Department', 'ProgramType', 'Program')));
		$this->set('applicablePayments', $this->paginate());
	    */
	    $this->paginate = array('order'=>array('ApplicablePayment.created DESC'),'contain'=>array('Student' => array('Department','College', 'ProgramType', 'Program')));
	    $this->__init_search();
		if ($this->Session->read('search_data')) {
		  $this->request->data['viewPayment']=true;
		}
	    if (!empty($this->request->data) && isset($this->request->data['viewPayment'])) { 
	           
	                $options = array();
	               
			        if (!empty($this->deparment_ids)) {
		                  if (empty($this->request->data['ApplicablePayment']['department_id'])) {
		                      $options [] = array(
	                                'Student.department_id'=>$this->department_ids);
		                  }
		            
		            } else if (!empty($this->college_ids)) {
		              
		                  if (empty($this->request->data['ApplicablePayment']['college_id'])) {
		                      $options [] = array(
	                                'Student.college_id'=>$this->college_ids,
		                            'Student.department_id is null'
	                               
	                               );
		                  }		            
		            }
		            if (!empty($this->request->data['ApplicablePayment']['department_id'])) {
		               $options [] = array(
		                    'Student.department_id'=>$this->request->data['ApplicablePayment']['department_id']
		               
		                 );
		            }
	            
		            if (!empty($this->request->data['ApplicablePayment']['college_id'])) {
		                 $options [] = array(
		                    'Student.college_id'=>$this->request->data['ApplicablePayment']['college_id']
		               
		                 );
		                
		            }
	            if (!empty($this->request->data['ApplicablePayment']['paid_date_to'])) {
	               $options [] = array(
	                    'ApplicablePayment.student_id is not null',
	                    'ApplicablePayment.created >= \''.
	                    $this->request->data['ApplicablePayment']['paid_date_from']['year'].'-'.
	                    $this->request->data['ApplicablePayment']['paid_date_from']['month'].'-'.
	                    $this->request->data['ApplicablePayment']['paid_date_from']['day'].' 00:00:01\'',
	                    
	                     'ApplicablePayment.created <= \''.
	                    $this->request->data['ApplicablePayment']['paid_date_to']['year'].'-'.
	                    $this->request->data['ApplicablePayment']['paid_date_to']['month'].'-'.
	                    $this->request->data['ApplicablePayment']['paid_date_to']['day'].' 23:59:59 \'',
	               
	               );
			
	            }
	           
	            if (!empty($this->request->data['ApplicablePayment']['studentnumber'])) {
	               unset($options);
	               $options [] = array(
	                    'Student.studentnumber like '=>$this->request->data['ApplicablePayment']['studentnumber'].'%'
	               );
			
	            }
	            
	           if (!empty($this->request->data['ApplicablePayment']['sponsor_type'])) {
	              
	               $options [] = array(
	                    'ApplicablePayment.sponsor_type like '=>$this->request->data['ApplicablePayment']['sponsor_type'].'%'
	               );
			
	            }
	            if(!empty($this->program_type_id)){
	            	  $options [] = array(
	                    'Student.program_type_id'=>$this->program_type_id
	               );
	            }
	            if(!empty($this->program_id)){
	            	  $options [] = array(
	                    'Student.program_id'=>$this->program_id
	               );
	            }
	            
	          $this->Paginator->settings['conditions']=$options;
		
	          $applicablePayments= $this->Paginator->paginate('ApplicablePayment');
	          
	          if (empty($applicablePayments)) {
	            $this->Session->setFlash('<span></span>'.
	            __('There is no student in the system in the given criteria.'),
				    'default',array('class'=>'info-box info-message'));
			  }
	     } 
		if (!empty($this->request->data['ApplicablePayment']['college_id'])) {
		      if (!empty($this->department_ids)) {
		          $departments = $this->ApplicablePayment->Student->Department->find('list',
		        array('conditions'=>array('Department.college_id'=>
		        $this->request->data['ApplicablePayment']['college_id'],'Department.id'=>$this->department_ids
		        )));
		      }
		       
		      $this->set(compact('departments'));
		        
		}
		$colleges=$this->ApplicablePayment->Student->College->find('list');
		$this->set(compact('applicablePayments','colleges'));
	    
	}

 function __init_search() {
        // We create a search_data session variable when we fill any criteria 
        // in the search form.
        if(!empty($this->request->data['ApplicablePayment'])){
               
                    $search_session = $this->request->data['ApplicablePayment'];
                   // Session variable 'search_data'
                    $this->Session->write('search_data', $search_session);
                
        } else {

        	$search_session = $this->Session->read('search_data');
        	$this->request->data['ApplicablePayment'] = $search_session;
        } 

    }

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid applicable payment'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('applicablePayment', $this->ApplicablePayment->read(null, $id));
	}

	function add() {
	    
	    if (!empty($this->request->data) && isset($this->request->data['saveApplicablePayment'])) {
			$this->ApplicablePayment->create();
			//check 
			
			if ($this->ApplicablePayment->duplication($this->request->data)==0) {			
			        if ($this->ApplicablePayment->save($this->request->data)) {
				        $this->Session->setFlash('<span></span>'.__('The applicable payment has been saved'),
				        'default',array('class'=>'success-box success-message'));
				        $this->redirect(array('action' => 'index'));
			        } else {
				        $this->Session->setFlash('<span></span>'.__('The applicable payment could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				        $this->request->data['continue']=true;
				        $student_number=$this->ApplicablePayment->Student->field('studentnumber',
			                        array('id'=>trim($this->request->data['ApplicablePayment']['student_id'])));
				        $this->request->data['ApplicablePayment']['studentID']=$student_number;
			        }
			
			} else {
			        $this->Session->setFlash('<span></span>'.__('You have already recorded the applicable payment for the selected student for '.$this->request->data['ApplicablePayment']['academic_year'].' of semester '.$this->request->data['ApplicablePayment']['semester'].'.'),'default',array('class'=>'error-box error-message'));
				        $this->request->data['continue']=true;
				    $student_number=$this->ApplicablePayment->Student->field('studentnumber',
			                        array('id'=>trim($this->request->data['ApplicablePayment']['student_id'])));
				        $this->request->data['ApplicablePayment']['studentID']=$student_number;
			}
		}
		if (!empty($this->request->data) && isset($this->request->data['continue'])) {
		       
		         $everythingfine=false;
			     if (!empty($this->request->data['ApplicablePayment']['studentID'])) {
			            $check_id_is_valid=$this->ApplicablePayment->Student->
			            find('count',
			            array('conditions'=>array('Student.studentnumber'=>
			            trim($this->request->data['ApplicablePayment']['studentID']))));
			            $studentIDs=1;
			            
			             if ($check_id_is_valid>0) {
			                 $everythingfine=true;
			                $student_id=$this->ApplicablePayment->Student->field('id',
			                array('studentnumber'=>trim($this->request->data['ApplicablePayment']['studentID'])));
			                $student_section_exam_status=$this->ApplicablePayment->Student->
	                get_student_section($student_id);
		                    $this->set(compact('student_section_exam_status'));
		
			                $this->set(compact('studentIDs'));
			             } else {
			                $this->Session->setFlash('<span></span> '.__('The provided student number is not valid.'),'default',array('class'=>'error-box error-message'));      
			             }
			             
			     } else {
			          $this->Session->setFlash('<span></span> '.__('Please provide student number to maintain student applicable payment.'),'default',array('class'=>'error-box error-message'));  
			    
			     }
			
		}
		
		
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid applicable payment'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ApplicablePayment->save($this->request->data)) {
				$this->Session->setFlash(__('The applicable payment has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The applicable payment could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ApplicablePayment->read(null, $id);
		}
		
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for applicable payment'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->ApplicablePayment->delete($id)) {
			$this->Session->setFlash('<span></span>'.__('Applicable payment deleted'),
			'default',array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Applicable payment was not deleted'),
		'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}

	public function mass_applicablepayment_import () 
	{
         $acyear_list = $this->AcademicYear->academicYearInArray(date('Y')-4,date('Y'));
		 $this->set(compact('acyear_list'));
		 debug($this->program_id);
		 debug($this->program_type_id);
		 if (!empty($this->request->data) && is_uploaded_file($this->request->data['ApplicablePayment']['File']['tmp_name']))
		 {
                //check the file type before doing the fucken manipulations.
			    if(strcasecmp($this->request->data['ApplicablePayment']['File']['type'],'application/vnd.ms-excel')) {
                     $this->Session->setFlash('<span></span>'.__('Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type while you saved the file and import again. Try also to use other 97-2003 file types if you are using office 2010 or recent versions. Current file format is: '.$this->request->data['ApplicablePayment']['File']['type']),'default', array('class'=>'error-box error-message'));
                    return ;
                }

               $data = new Spreadsheet_Excel_Reader();
                // Set output Encoding.
               $data->setOutputEncoding('CP1251');
               $data->read($this->request->data['ApplicablePayment']['File']['tmp_name']);    
               $headings = array();
               $xls_data = array();
             
               //check without department 
              //TODO: Remove studentnumber
               $required_fields = array('studentnumber','tutition_fee','meal',
               'accomodation', 'health','sponsor_type','sponsor_name','sponsor_address');
			
			    $non_existing_field=array();
                $non_valide_rows=array();
                if(empty($data->sheets[0]['cells'])) {
                     $this->Session->setFlash('<span></span>'.__('Importing Error. The excel file you uploaded is empty.', true),'default', 
array('class'=>'error-box error-message'));
                    return ;
                }
                if(empty($data->sheets[0]['cells'][1])) {
                     $this->Session->setFlash('<span></span>'.
                     __('Importing Error. Please insert your filed name (studentnumber,tutition_fee,meal,
               accomodation, health,sponsor_type,sponsor_name,sponsor_address)  at first row of your excel file.', true),'default', array('class'=>'error-box error-message'));
                    return ;
                }           
                debug($required_fields);  
                for($k=0;$k<count($required_fields); $k++){
                     if(in_array($required_fields[$k], 
$data->sheets[0]['cells'][1])===FALSE)
                        $non_existing_field[]=$required_fields[$k];
                }
              
				 if(count($non_existing_field) > 0)
                 {
                      $field_list = "";
                      foreach($non_existing_field as $k=>$v) 
                            $field_list .= ($v.", ");
                      
                      $field_list = substr($field_list, 0, (strlen($field_list)-2));

                      $this->Session->setFlash('<span></span>'.__('Importing Error. '.$field_list.' is/are required in the excel file you imported at first row.', true),'default', array('class'=>'error-box error-message')); 
 
                      return;
                 }
                 else
                 {
                   $fields_name_applicablepayment_table=$data->sheets[0]['cells'][1];
                   $duplicated_student_number = array();
                  
                   for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                       $row_data = array();
                       $name_error_duplicate = false;
                       for ($j = 1; $j <= count( $fields_name_applicablepayment_table); $j++) {
                              //check student number is given and populate with value 
                              
                				if($fields_name_applicablepayment_table[$j] == "studentnumber")
                           {
                           	   $currentStudentNumber=trim($data->sheets[0]['cells'][$i][$j]);
                                //check from the database if the student is valid 
                           	   if(isset($currentStudentNumber)
                           	   	&& !empty($currentStudentNumber)){
                           	   	   $validStudent=$this->ApplicablePayment->Student->find('first',
array('conditions'=>array('Student.studentnumber like '=>$currentStudentNumber.'%',
	'Student.program_id'=>$this->program_id,'Student.program_type_id'=>$this->program_type_id
	),
'recursive'=>-1
));
                                debug($validStudent);
                                debug($this->ApplicablePayment->Student->find('first',
array('conditions'=>array('Student.studentnumber like '=>$currentStudentNumber.'%'
	),
	'recursive'=>-1
)));
                                debug($currentStudentNumber);
										  
                                if(empty($validStudent)) {
                                  
                                       $non_valide_rows[] = "Student number at row number ".$i." does not exist on the system"; 
                                       continue;
                                }

                           	   } else {
                           	   	continue;
                           	   }
                             
								
                               
                                if (isset($currentStudentNumber)&& !empty($currentStudentNumber)) {
  											  $duplicated_student_number[$currentStudentNumber]=
                                   isset($duplicated_student_number[$currentStudentNumber]) ?
                                   $duplicated_student_number[$currentStudentNumber] : 0 +1;
                                  
                                   if(isset($duplicated_student_number[$currentStudentNumber])
                                   && $duplicated_student_number[$currentStudentNumber]>1) {
                                   
                                       $non_valide_rows[] = "Duplicated student number at row number ".$i; 
                                       continue;
                                   }
                                } else {
                                   $duplicated_student_number[$currentStudentNumber]=0;
                                } 
                          }
                          
					     if ($fields_name_applicablepayment_table[$j] == "tutition_fee" 
                                && isset($data->sheets[0]['cells'][$i][$j]) && 
                                $data->sheets[0]['cells'][$i][$j]!="" && !(strcasecmp($data->sheets[0]['cells'][$i][$j],'true') || strcasecmp($data->sheets[0]['cells'][$i][$j],'false') )) {
                                //TODO: Uncomment the following two lines
                                $non_valide_rows[] = "Please enter a valid value for  tutition fee on row number ".$i.' must be true OR false ';
                                continue;
                                
                          }
                          if ($fields_name_applicablepayment_table[$j] == "accomodation" 
                                && isset($data->sheets[0]['cells'][$i][$j]) && 
                                $data->sheets[0]['cells'][$i][$j]!="" && !(strcasecmp($data->sheets[0]['cells'][$i][$j],'true') || strcasecmp($data->sheets[0]['cells'][$i][$j],'false') ) ) {
                                //TODO: Uncomment the following two lines
                                $non_valide_rows[] = "Please enter a valid accomodation fee on row number ".$i;
                                continue;
                          }
						if ($fields_name_applicablepayment_table[$j] == "meal" && 
					     		  isset($data->sheets[0]['cells'][$i][$j]) && $data->sheets[0]['cells'][$i][$j]!="" && !(strcasecmp($data->sheets[0]['cells'][$i][$j],'true') || strcasecmp($data->sheets[0]['cells'][$i][$j],'false'))) {
                                //TODO: Uncomment the following two lines
                            		$non_valide_rows[] = "Please enter a valid cafeteria fee on row number ".$i;
                           	 continue; 
                          }

                           if ($fields_name_applicablepayment_table[$j] == "health" 
                                && isset($data->sheets[0]['cells'][$i][$j]) && 
                                $data->sheets[0]['cells'][$i][$j]!="" && !(strcasecmp($data->sheets[0]['cells'][$i][$j],'true') || strcasecmp($data->sheets[0]['cells'][$i][$j],'false'))) {
                                //TODO: Uncomment the following two lines
                                $non_valide_rows[] = "Please enter a valid medical fee on row number ".$i;
                                continue;
                           }


						  if ($fields_name_applicablepayment_table[$j] == "sponsor_type" 
                                && isset($data->sheets[0]['cells'][$i][$j]) && 
                                $data->sheets[0]['cells'][$i][$j]!="" && empty($data->sheets[0]['cells'][$i][$j])) {
                                //TODO: Uncomment the following two lines
                                $non_valide_rows[] = "Please enter sponsor type at ".$i;
                                continue;
                           }

                            if(in_array($fields_name_applicablepayment_table[$j], $required_fields))
                            {

                                 if($fields_name_applicablepayment_table[$j] == "studentnumber"){
                                   $validStudent=$this->ApplicablePayment->Student->find('first',
array('conditions'=>array('Student.studentnumber like '=>trim($data->sheets[0]['cells'][$i][$j]).'%','Student.program_id'=>$this->program_id,'Student.program_type_id'=>$this->program_type_id
	)));
                                 	if(empty($validStudent)){
$non_valide_rows[] = "The  data on row number ".$i." not in your privilage list or not in the system.";
                                 		continue;
                                 	} else {
                                 	$row_data['student_id']=$validStudent['Student']['id'];
                                 	}
                                 } else if ($fields_name_applicablepayment_table[$j] == "tutition_fee") {
                                    $row_data['tutition_fee']=$data->sheets[0]['cells'][$i][$j];
                                 } else if ($fields_name_applicablepayment_table[$j] == "accomodation") {
                                     $row_data['accomodation']=$data->sheets[0]['cells'][$i][$j];
                                 } else if ($fields_name_applicablepayment_table[$j]=="meal") {
                                    $row_data['meal']=$data->sheets[0]['cells'][$i][$j];
                                 } else if ($fields_name_applicablepayment_table[$j]=="health") {
                                    $row_data['health']=$data->sheets[0]['cells'][$i][$j];
								 } else if($fields_name_applicablepayment_table[$j]=="sponsor_type"){
								 	  $row_data['sponsor_type']=$data->sheets[0]['cells'][$i][$j];
								 } else if($fields_name_applicablepayment_table[$j]=="sponsor_address"){
								 	  $row_data['sponsor_address']=$data->sheets[0]['cells'][$i][$j];
								 } else if($fields_name_applicablepayment_table[$j]=="sponsor_name"){
								 		$row_data['sponsor_name']=$data->sheets[0]['cells'][$i][$j];
								 }
                            }
	                        $row_data['academic_year']=$this->request->data['ApplicablePayment']['academic_year'];
	                        $row_data['semester']=$this->request->data['ApplicablePayment']['semester'];
					   	}
					   		
			            $is_duplicated=$this->ApplicablePayment->find('count',
			                   array('conditions'=>$row_data,'recursive'=>-1));
			                   //debug($is_duplicated);
			                   if($is_duplicated>0){
			                    $non_valide_rows[] = "The  data on row number ".$i." has already existed or imported.
			                    Please remove it from your excel file.";
			                   }
			
					   		
					   		    $xls_data[] = array('ApplicablePayment' => $row_data);
			                   $data->sheets[0]['cells'][$i]=null;
			                   if(count($non_valide_rows) == 19) {
								   		$non_valide_rows[] = "Please check other similar errors in the file you imported.";
								   		break;
									 }
		              }

                		//invalid rows 
               		if(count($non_valide_rows) > 0)
               		{
                      $row_list = "";
                      $this->Session->setFlash('<span></span>'.__('Importing Error. 
                      Please correct the following listed rows in your excel file.', true),
                      'default', array('class'=>'error-box error-message'));
                      $this->set('non_valide_rows',$non_valide_rows);
                      return;
              			} 
			       }
			   
			      if(!empty($xls_data))
			      {
			       	$reformat_for_saveAll=array();
			       	
					$dupExit=array();
					$invalidAC=array();
                    foreach ($xls_data as $xlk=>&$xlv) {
                    	
                    	//check if imported earlier
                    	$duplicationExist=$this->ApplicablePayment->find('count',array(
						'conditions'=>array('ApplicablePayment.student_id'=>$xlv['ApplicablePayment']['student_id'],
						'ApplicablePayment.academic_year'=>$this->request->data['ApplicablePayment']['academic_year']
						
						),
                        ));
                        $validACc=$this->ApplicablePayment->Student->find('count',array(
						'conditions'=>array('Student.id'=>$xlv['ApplicablePayment']['student_id'],
						'AcceptedStudent.academicyear <='=>$this->request->data['ApplicablePayment']['academic_year']
						),
                        ));

				        if($duplicationExist==0 && $validACc){
				        	 $reformat_for_saveAll['ApplicablePayment'][]=$xlv['ApplicablePayment'];
				        } else {
				        	if($validACc==0){
                              $invalidAC[]=$xlv['ApplicablePayment'];
				        	}
				        	if($duplicationExist>0){
				        		$dupExit[]=$xlv['ApplicablePayment'];
				        	}
				        }
                    }
                   
                    if(!empty($reformat_for_saveAll['ApplicablePayment']) && $this->ApplicablePayment->saveAll($reformat_for_saveAll['ApplicablePayment'],array('validate'=>'first'))) {
								
                        $this->Session->setFlash('<span></span>Success. Imported '. count($reformat_for_saveAll['ApplicablePayment'])
                        .' records.','default',array('class'=>'success-box success-message'));
                        //$this->redirect(array('action'=>'index'));
                    } else {
                    	if(!empty($invalidAC)){
                    		 $this->Session->setFlash('<span></span>Error. You can not maintain applicable for students before they are admitted. Applicable payment academic year always greater than  student admission academic year. ','default',
                        array('class'=>'error-box error-message'));

                    	} else if(!empty($dupExit)){
                    		 $this->Session->setFlash('<span></span>Error.The cost sharing you tried to import has already imported. ','default',
                        array('class'=>'error-box error-message'));
                    	} else{
                        $this->Session->setFlash('<span></span>Error. Unable to import records. Please try again.','default',
                        array('class'=>'error-box error-message'));
                    	}
                    } 
                } else {
                 	$this->Session->setFlash('<span></span>Error. Unable to import records. Please try again.','default',array('class'=>'error-box error-message'));
               } 
		  }
    } 

}
