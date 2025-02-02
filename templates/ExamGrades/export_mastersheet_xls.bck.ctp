<?php
    /**
     * Export all member records in .xls format
     * with the help of the xlsHelper
     */
    
     $this->Xls->setHeader('Master Sheet ');
     $this->Xls->addXmlHeader();
     $this->Xls->setWorkSheetName('Section '.$section_detail['name']);
     
     if(!empty($master_sheet['students_and_grades'])){
     
	    $this->Xls->openRow();
                $this->Xls->writeString('College: '.$college_detail['name']);
        $this->Xls->closeRow(); 
        $this->Xls->openRow();
                $this->Xls->writeString('Program: '.$program_detail['name']);
        $this->Xls->closeRow();  
        $this->Xls->openRow();
                $this->Xls->writeString('Program Type: '.$program_type_detail['name']);
        $this->Xls->closeRow(); 
        $this->Xls->openRow();
                $this->Xls->writeString('Department: '. (!empty($department_detail['name']) && 
                $department_detail['name'] != "" ? $department_detail['name'] : 'Freshman Program'));
        $this->Xls->closeRow(); 
        
        $this->Xls->openRow();
                $this->Xls->writeString('Academic Year: '. $academic_year);
        $this->Xls->closeRow(); 
        
        $this->Xls->openRow();
                $this->Xls->writeString('Semester: '.$semester);
        $this->Xls->closeRow(); 
        
		$this->Xls->openRow();
		$this->Xls->closeRow(); 	
		
		if(count($master_sheet['registered_courses']) > 0) {
		  	$this->Xls->openRow();
			    $this->Xls->writeString('No');
			    $this->Xls->writeString('Course Title');
			    $this->Xls->writeString('Course Code');
			    $this->Xls->writeString('Cr. Hr.');
			   
		    $this->Xls->closeRow();
		   
		    $registered_and_add_course_count = 0;
			$registered_course_credit_sum = 0;
			foreach($master_sheet['registered_courses'] as $key => $registered_course) {
				$registered_and_add_course_count++;
				$registered_course_credit_sum += $registered_course['credit'];
			    $this->Xls->openRow();
				     $this->Xls->writeString($registered_and_add_course_count);
				     $this->Xls->writeString($registered_course['course_title']);
				     $this->Xls->writeString($registered_course['course_code']);
				     $this->Xls->writeString($registered_course['credit']);
			     $this->Xls->closeRow();
			
			}
			
			$this->Xls->openRow();
				     $this->Xls->writeString('Total:'.$registered_course_credit_sum);	
			$this->Xls->closeRow();
			
		
		}
		$this->Xls->openRow();
		$this->Xls->closeRow(); 	
		
		if(count($master_sheet['added_courses']) > 0) {
		  	$this->Xls->openRow();
			    $this->Xls->writeString('No');
			    $this->Xls->writeString('Course Title');
			    $this->Xls->writeString('Course Code');
			    $this->Xls->writeString('Cr. Hr.');
			   
		    $this->Xls->closeRow();
		   
		   $added_course_credit_sum = 0;
			foreach($master_sheet['added_courses'] as $key => $added_course) {
				$registered_and_add_course_count++;
				$added_course_credit_sum += $added_course['credit'];
				
			    $this->Xls->openRow();
				     $this->Xls->writeString($registered_and_add_course_count);
				     $this->Xls->writeString($added_course['course_title']);
				     $this->Xls->writeString($added_course['course_code']);
				     $this->Xls->writeString($added_course['credit']);
			     $this->Xls->closeRow();
			
			}
			
			$this->Xls->openRow();
				     $this->Xls->writeString('Total:'.$added_course_credit_sum);	
			$this->Xls->closeRow();
			
		
		}
		
		$this->Xls->openRow();
		$this->Xls->closeRow(); 
		
		$this->Xls->openRow();
			 $this->Xls->writeString('');
			 $this->Xls->writeString('');
			 $this->Xls->writeString('');
			 $this->Xls->writeString('');
			
		
		
		$registered_and_add_course_count = 0;
		
		foreach($master_sheet['registered_courses'] as $key => $registered_course) {
			$registered_and_add_course_count++;
			 $this->Xls->writeString($registered_and_add_course_count);
			  $this->Xls->writeString('');
		
			
		}
		
		foreach($master_sheet['added_courses'] as $key => $added_course) {
			$registered_and_add_course_count++;
		    $this->Xls->writeString($registered_and_add_course_count);
		     $this->Xls->writeString('');
		
		}
		
		  $this->Xls->writeString('Semester');
		  $this->Xls->writeString('');
		  $this->Xls->writeString('');
		  $this->Xls->writeString('Previous');
		  $this->Xls->writeString('');
		  $this->Xls->writeString('');
		  
	      $this->Xls->writeString('Cumulative');
	      $this->Xls->writeString('');
		  $this->Xls->writeString('');
		  
		  $this->Xls->writeString('Status');
		  
		
		
		$this->Xls->closeRow();
		
	    $this->Xls->openRow();
			 $this->Xls->writeString('No');
			 $this->Xls->writeString('Full Name');
			 $this->Xls->writeString('ID NO');
			 $this->Xls->writeString('Sex');
		
		    foreach($master_sheet['registered_courses'] as $key => $registered_course) {
		           $this->Xls->writeString('G');
		           $this->Xls->writeString('GP');
		    }
		
		    foreach($master_sheet['added_courses'] as $key => $added_course) {
			      $this->Xls->writeString('G');
		           $this->Xls->writeString('GP');
		    }
		
			 
			  $this->Xls->writeString('CH');
			  $this->Xls->writeString('GP');
			  $this->Xls->writeString('SGPA');
			
			
			  $this->Xls->writeString('CH');
			  $this->Xls->writeString('GP');
			  $this->Xls->writeString('SGPA');
			  
			  $this->Xls->writeString('CH');
			  $this->Xls->writeString('GP');
			  $this->Xls->writeString('SGPA');
			    
		
		$this->Xls->closeRow();
		
		$student_count = 0;
        foreach($master_sheet['students_and_grades'] as $key => $student) {
	        $credit_hour_sum = 0;
	        $gp_sum = 0;
	        $student_count++;
	        $this->Xls->openRow();
	            $this->Xls->writeString($student_count);
	            $this->Xls->writeString($student['full_name']);
	            $this->Xls->writeString($student['studentnumber']);
	            $this->Xls->writeString((strcasecmp($student['gender'], 'male') == 0 ? 'M' : 'F'));
	       foreach($master_sheet['registered_courses'] as $key => $registered_course) {
			 if($student['courses']['r-'.$registered_course['id']]['registered'] == 1) {
			 	if(isset($student['courses']['r-'.$registered_course['id']]['grade'])) {
			 		   $this->Xls->writeString($student['courses']['r-'.$registered_course['id']]['grade']);
			 		    $this->Xls->writeString($student['courses']['r-'.$registered_course['id']]['grade']);
			 		
			 		
			 		if(isset($student['courses']['r-'.$registered_course['id']]['point_value'])) {
			 			$this->Xls->writeString(number_format(($student['courses']['r-'.$registered_course['id']]['credit'] *
			 			 $student['courses']['r-'.$registered_course['id']]['point_value']), 2, '.', ''));
			 			 $gp_sum += ($student['courses']['r-'.$registered_course['id']]['credit'] * $student['courses']['r-'.$registered_course['id']]['point_value']);
			 		}
			 		
			 	}
			 	else {
			 	     $this->Xls->writeString($student['courses']['r-'.$registered_course['id']]['droped'] == 1 ? 'DP' : '**');
			 	     $this->Xls->writeString('');
			 	}
			 if($student['courses']['r-'.$registered_course['id']]['droped'] == 0)
			 	$credit_hour_sum += $student['courses']['r-'.$registered_course['id']]['credit'];
			 }
			 else {
			     $this->Xls->writeString('---');
			 	 $this->Xls->writeString('');
			 }
		}
		foreach($master_sheet['added_courses'] as $key => $added_course) {
			 if($student['courses']['a-'.$added_course['id']]['added'] == 1) {
			 	if(isset($student['courses']['a-'.$added_course['id']]['grade'])) {
			 	
			 		$this->Xls->writeString($student['courses']['a-'.$added_course['id']]['grade']);
			 	    $this->Xls->writeString('');
			 	 
			 		if(isset($student['courses']['a-'.$added_course['id']]['point_value'])) {
			 			 $this->Xls->writeString(number_format(($student['courses']['a-'.$added_course['id']]['credit'] * 
			 			 $student['courses']['a-'.$added_course['id']]['point_value']), 2, '.', ''));
			 			$gp_sum += ($student['courses']['a-'.$added_course['id']]['credit'] * $student['courses']['a-'.$added_course['id']]['point_value']);
			 		}
			 		
			 	}
			 	else {
			 		
			 		$this->Xls->writeString('***');
			 	    $this->Xls->writeString('');
			 	}
			 $credit_hour_sum += $student['courses']['a-'.$added_course['id']]['credit'];
			 }
			 else {
			 	 $this->Xls->writeString('---');
			 	 $this->Xls->writeString('');
			 }
		}
		
		 $this->Xls->writeString(!empty($student['StudentExamStatus']) ? 
		 $student['StudentExamStatus']['credit_hour_sum'] : '---'); 
		 $this->Xls->writeString(!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['grade_point_sum'] : 
		 '---');
		 
		$this->Xls->writeString(!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['sgpa'] : '---');

	  $this->Xls->writeString(!empty($student['PreviousStudentExamStatus']) ? 
	  $student['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---');
	  
		$this->Xls->writeString(!empty($student['PreviousStudentExamStatus']) ? 
		$student['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---');
		
		$this->Xls->writeString(!empty($student['PreviousStudentExamStatus']) ? 
		$student['PreviousStudentExamStatus']['cgpa'] : '---');
	
			if(!empty($student['StudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
				$this->Xls->writeString(($student['StudentExamStatus']['credit_hour_sum']+
				$student['PreviousStudentExamStatus']['previous_credit_hour_sum']) - $student['deduct_credit']);
			}
			else if(!empty($student['StudentExamStatus'])) {
				
				$this->Xls->writeString($student['StudentExamStatus']['credit_hour_sum']);
			}
			else if(!empty($student['PreviousStudentExamStatus'])) {
				$this->Xls->writeString($student['PreviousStudentExamStatus']['previous_credit_hour_sum']);
			}
			else
				 $this->Xls->writeString('---');
		
			if(!empty($student['StudentExamStatus']) && !empty($student['PreviousStudentExamStatus'])) {
			    $this->Xls->writeString((($student['StudentExamStatus']['grade_point_sum']+
			    $student['PreviousStudentExamStatus']['previous_grade_point_sum']) - $student['deduct_gp']));
			}
			else if(!empty($student['StudentExamStatus'])) {
				
				 $this->Xls->writeString($student['StudentExamStatus']['grade_point_sum']);
			}
			else if(!empty($student['PreviousStudentExamStatus'])) {
				
				  $this->Xls->writeString($student['PreviousStudentExamStatus']['previous_grade_point_sum']);
			}
			else
			   $this->Xls->writeString('---');
		
		
		 
          $this->Xls->writeString((!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['cgpa'] : '---'));
	      $this->Xls->writeString((!empty($student['AcademicStatus']) && 
		!empty($student['AcademicStatus']['id'])? $student['AcademicStatus']['name'] : '---'));  
	    
	     $this->Xls->writeString('');
	    
	    $this->Xls->closeRow();
	
	
	 }
		
		
		$this->Xls->openRow();
		$this->Xls->closeRow(); 	
	}
    $this->Xls->addXmlFooter();
    exit();
?>
