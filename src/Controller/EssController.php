<?php   
App::uses('ConnectionManager', 'Model');  
class EssController extends AppController {
        public $name = "Ess";
        public $uses = array();
        public $menuOptions = array(
             //'title'=>'ljkasdfjklasdf',
            'exclude'=>array('index'),
            'weight'=>-10000000,
        );
    	public $conn;
    	public $config=array();
    	 public $components =array('AcademicYear','EthiopicDateTime');
        public function beforeFilter(){
            parent::beforeFilter();
           // $this->Auth->allow('index','push_students_onecard_db'); 
        }
         public function beforeRender() {
       			 parent::beforeRender();
        		$acyear_array_data = $this->AcademicYear->acyear_array();
		        //To diplay current academic year as default in drop down list
		        $defaultacademicyear=$this->AcademicYear->current_academicyear();
		       
	        	$this->set(compact('acyear_array_data','defaultacademicyear'));
	   }

        public function approve_cafeNonCafe_request(){

        }
	 	public function index() {
	 	
            
	 	}
	 	/*

       public function push_students_onecard_db($department_id = null, $program_id = null, $program_type_id = null) {
      	$db = ConnectionManager::getDataSource("mssql");

		$SLN_Designation=18;
		$SLN_Category=3;
      	$queryStr='';
      	$queryStr.=" SLN_Company=5 and SLN_Category=$SLN_Category and SLN_Designation=$SLN_Designation";

      	$programs = ClassRegistry::init('Student')->Program->find('list');
		$program_types =ClassRegistry::init('Student')->ProgramType->find('list');
		$departments=ClassRegistry::init('Student')->Department->allDepartmentsByCollege2(0, 
		$this->department_ids, $this->college_ids);
		
		$department_combo_id = null;
		$program_types = array(0 => 'All Program Types') + $program_types;
		$departments = array(0 => 'All University Students') + $departments;

		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;
      	$student_ids=array();
      	$options= array();
      	$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null )';
		$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM senate_lists where student_id is not null )';
		$options['conditions'][] = 'Student.print_count >=1 ';

	 	if(!empty($this->request->data['Ess']['department_id'])){
              $options['conditions']['Student.department_id'] = $this->request->data['Ess']['department_id'];
               $queryStr.=' and SLN_Department="'.$this->request->data['Ess']['department_id'].'"';
	 	}

	 	if(!empty($this->request->data['Ess']['program_id'])){
              $options['conditions']['Student.program_id'] = $this->request->data['Ess']['program_id'];

	 	}
	 	if(!empty($this->request->data['Ess']['program_type_id'])){
              $options['conditions']['Student.program_type_id'] = $this->request->data['Ess']['program_type_id'];
	 	}
        $options['contain']=array('Department');
       
        //When any of the button is clicked (List students or Add to Senate List)
		
		if(!empty($this->request->data) && !empty($this->request->data['listStudentsForOneCardList'])) {
			
			if(!empty($queryStr)){
						$list=array();
						$queryCommaSeparated="
						SELECT Employee_Code
						FROM dbo.MSTR_Employee 
						WHERE $queryStr
						";
						if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    							$studentList=$db->query($queryCommaSeparated);	
								foreach($studentList as $k=>$v){	
									$list[]=''.$v[0]['Employee_Code'].'';
								}
						} else {
						   		$studentList=$this->__mssql($db->config,$queryCommaSeparated);
						   		while ($row = mssql_fetch_array($studentList)) {
						       		 $list[]=$row['Employee_Code'];
						       	}
						}
												
						
						if(!empty($studentList) && !empty($list)){
								
								$listIds=ClassRegistry::init('Student')->find('list',array('conditions'=>array('Student.studentnumber'=>$list),
									'field'=>array('Student.id')));
								$options['conditions'][] = 'Student.id NOT IN ('.implode(',',$listIds).')';
								
						}

      	   }
      	
			$students_for_onecard_list=ClassRegistry::init('Student')->find('all', $options);
			debug($students_for_onecard_list);	
			$default_department_id = $this->request->data['Ess']['department_id'];
			$default_program_id = $this->request->data['Ess']['program_id'];
			$default_program_type_id = $this->request->data['Ess']['program_type_id'];
		}
		else if(!empty($department_id) && !empty($program_id)) {
			$options['conditions']['Student.department_id'] =$department_id; 
			$options['conditions']['Student.program_id'] =$program_id; 
			$options['conditions']['Student.program_type_id'] =$program_type_id; 
			$students_for_onecard_list=ClassRegistry::init('Student')->find('all', $options);
			$default_department_id = $department_id;
			$default_program_id = $program_id;
			$default_program_type_id = $program_type_id;
		}
		//debug($students_for_senate_list);
		if(!empty($this->request->data) && !empty($this->request->data['addStudentToOneCardDb'])) {
		$count=0;
		
		foreach($this->request->data['Ess'] as $key => $student) {
				$value=ClassRegistry::init('Student')->find('first',
				array('conditions'=>array('Student.id'=>$student['id']),'recursive'=>-1));

		       if($student['include'] == 1) {
							$studentQuery="SELECT count(*) as c FROM MSTR_Employee AS S WHERE Employee_Code='".$value['Student']['studentnumber']."'";
					 		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					 			$studentResult = $db->query($studentQuery);
					 		} else{
                              	$queryResult=$this->__mssql($db->config,$queryCommaSeparated);
								$studentResult[0][0]['c']=mssql_num_rows($queryResult);
					 		}
					  
					  	if(empty($studentResult[0][0]['c'])){
							  	$count++;
							  	if(strcmp($value['Student']['gender'], 'male')==0){
							  			$gender=1;
							  	} else{
							  		$gender=0;
							  	}
							  
							  	// do inseration to ess db
							  	$studNumber=$value['Student']['studentnumber'];
							  	$studDepartment=$value['Student']['department_id'];

							  	$studFirstName=$value['Student']['first_name'];
							  	$studLastName=$value['Student']['last_name'];
							  	$studeEmail=$value['Student']['email'];
							  	$studPhoneMobile=$value['Student']['phone_mobile'];
							  	$Card_Number=$value['Student']['phone_mobile'];;
							  	$studentQueryInsert="INSERT INTO MSTR_Employee(SLN_Company,Employee_Code,SLN_Department,SLN_Designation,
							  	SLN_Category,First_Name,
							  	Last_Name,Gender,Personal_Email,
							  	Personal_Phone,Card_Number) VALUES ('5','$studNumber','$studDepartment','$SLN_Designation','$SLN_Category','$studFirstName','$studLastName','$gender','$studeEmail',
							  	'$studPhoneMobile','$Card_Number')";
							   if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					 			$studentResult = $db->query($studentQueryInsert); 
					 		   } else{
                            	  	$studentResult=$this->__mssql($db->config,$studentQueryInsert);
					 		   }  
		     		}
		  }
		}
		
		$this->Session->setFlash('<span></span>'.__($count.' students are included in the one card database. Access to cafe needs meal hall assignment and approval of being cafe user.'), 'default',array('class'=>'success-box success-message'));

		}

		if (!strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				mssql_close($this->conn);
		}

          
        $this->set(compact('programs', 'program_types', 'departments', 'department_combo_id', 'students_for_onecard_list', 'default_department_id', 'default_program_id', 'default_program_type_id'));
	 }
	 */

	  public function push_students_onecard_db($department_id = null, $program_id = null, $program_type_id = null) {
	  	$this->_config();
      	
		$SLN_Designation=18;
		$SLN_Category=3;
      	$queryStr='';
      	$queryStr.=" SLN_Company=5 and SLN_Category=$SLN_Category and SLN_Designation=$SLN_Designation";

      	$programs = ClassRegistry::init('Student')->Program->find('list');
		$program_types =ClassRegistry::init('Student')->ProgramType->find('list');
	
		$department_combo_id = null;
		$program_types = array(0 => 'All Program Types') + $program_types;
		
		$default_department_id = null;
		$default_program_id = null;
		$default_program_type_id = null;
      	$student_ids=array();
      	$options= array();
      	$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM graduate_lists where student_id is not null )';
		$options['conditions'][] = 'Student.id NOT IN (SELECT student_id FROM senate_lists where student_id is not null )';
		$options['conditions'][] = 'Student.print_count >=1 ';

	 	if(!empty($this->request->data['Ess']['department_id'])){
              $options['conditions']['Student.department_id'] = $this->request->data['Ess']['department_id'];
               $queryStr.=' and SLN_Department="'.$this->request->data['Ess']['department_id'].'"';
	 	}

	 	if(!empty($this->request->data['Ess']['program_id'])){
              $options['conditions']['Student.program_id'] = $this->request->data['Ess']['program_id'];

	 	}
	 	if(!empty($this->request->data['Ess']['program_type_id'])){
              $options['conditions']['Student.program_type_id'] = $this->request->data['Ess']['program_type_id'];
	 	}

	 	if(!empty($this->request->data['Ess']['academicyear'])){
              $options['conditions']['Student.admissionyear']=$this->AcademicYear->get_academicYearBegainingDate($this->request->data['Search']['academicyear']);

	 	}

        $options['contain']=array('Department');
       
        //When any of the button is clicked (List students or Add to Senate List)
		
		if(!empty($this->request->data) && !empty($this->request->data['listStudentsForOneCardList'])) {
			
			if(!empty($queryStr)){
						$list=array();
						$queryCommaSeparated="
						SELECT TOP(3) Employee_Code
						FROM dbo.MSTR_Employee 
						WHERE $queryStr
						";
						
				   		$studentList=$this->_mssql($queryCommaSeparated);
				   		while ($row = mssql_fetch_assoc($studentList)) {
				       		 $list[]=$row['Employee_Code'];
				       	}
						mssql_free_result($studentList);
						if(!empty($list)){
								
								$listIds=ClassRegistry::init('Student')->find('list',array('conditions'=>array('Student.studentnumber'=>$list),
									'field'=>array('Student.id')));
								$options['conditions'][] = 'Student.id NOT IN ('.implode(',',$listIds).')';
								
						}

      	   }
      	
			$students_for_onecard_list=ClassRegistry::init('Student')->find('all', $options);
			debug($students_for_onecard_list);	
			$default_department_id = $this->request->data['Ess']['department_id'];
			$default_program_id = $this->request->data['Ess']['program_id'];
			$default_program_type_id = $this->request->data['Ess']['program_type_id'];
		}
		else if(!empty($department_id) && !empty($program_id)) {
			$options['conditions']['Student.department_id'] =$department_id; 
			$options['conditions']['Student.program_id'] =$program_id; 
			$options['conditions']['Student.program_type_id'] =$program_type_id; 
			$students_for_onecard_list=ClassRegistry::init('Student')->find('all', $options);
			$default_department_id = $department_id;
			$default_program_id = $program_id;
			$default_program_type_id = $program_type_id;
		}
		//debug($students_for_senate_list);
		if(!empty($this->request->data) && !empty($this->request->data['addStudentToOneCardDb'])) {
		$count=0;
		
		foreach($this->request->data['Ess'] as $key => $student) {
				$value=ClassRegistry::init('Student')->find('first',
				array('conditions'=>array('Student.id'=>$student['id']),'recursive'=>-1));

		       if($student['include'] == 1) {
					    $studentQuery="SELECT count(*) as c FROM MSTR_Employee AS S WHERE Employee_Code='".$value['Student']['studentnumber']."'";
                      	$queryResult=$this->_mssql($queryCommaSeparated);
						$studentResult[0][0]['c']=mssql_num_rows($queryResult);
			 			mssql_free_result($queryResult);
					  	if(empty($studentResult[0][0]['c'])){
							  	$count++;
							  	if(strcmp($value['Student']['gender'], 'male')==0){
							  			$gender=1;
							  	} else{
							  		$gender=0;
							  	}
							  
							  	// do inseration to ess db
							  	$studNumber=$value['Student']['studentnumber'];
							  	$studDepartment=$value['Student']['department_id'];

							  	$studFirstName=$value['Student']['first_name'];
							  	$studLastName=$value['Student']['last_name'];
							  	$studeEmail=$value['Student']['email'];
							  	$studPhoneMobile=$value['Student']['phone_mobile'];
							  	$Card_Number=$value['Student']['phone_mobile'];;
							  	$studentQueryInsert="INSERT INTO MSTR_Employee(SLN_Company,Employee_Code,SLN_Department,SLN_Designation,
							  	SLN_Category,First_Name,
							  	Last_Name,Gender,Personal_Email,
							  	Personal_Phone,Card_Number) VALUES ('5','$studNumber','$studDepartment','$SLN_Designation','$SLN_Category','$studFirstName','$studLastName','$gender','$studeEmail',
							  	'$studPhoneMobile','$Card_Number')";
							
                            	$studentResult=$this->_mssql($studentQueryInsert);
		     		}
		  }
		   mssql_close($this->conn);
		}
		
		$this->Session->setFlash('<span></span>'.__($count.' students are included in the one card database. Access to cafe needs meal hall assignment and approval of being cafe user.'), 'default',array('class'=>'success-box success-message'));

		}


         if($this->role_id==ROLE_SYSADMIN){
		 	$department_ids=ClassRegistry::init('Department')->find('list',array('fields'=>
		 		array('Department.id','Department.id')));
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, 
			$department_ids, $this->college_ids);
		 } else if (!empty($this->department_ids) || !empty($this->college_ids)) {
		     $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, 
			$this->department_ids, $this->college_ids);
		 } else if(!empty($this->department_id)) {
		     $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(1, 
			$this->department_id, $this->college_id);
		 }  else {
            $departments=array();
		 }

        $this->set(compact('programs', 'program_types', 'departments', 'department_combo_id', 'students_for_onecard_list', 'default_department_id', 'default_program_id', 'default_program_type_id'));
	 }

	 private function _mssql($query){
	 		//connect to the database 
	 		$this->conn=mssql_connect($this->config['host'],$this->config['login'],$this->config['password']);
	 		$selectDB=mssql_select_db($this->config['database'],$this->conn);
	 		$result=mssql_query($query);
	 		return  $result;

	 }
	 private function _config(){
            $this->config['host'] = '10.144.5.210';
			$this->config['login'] = 'sa';
			$this->config['password'] = 'admin@123';
			$this->config['database']='ESS';
	 }
}
?>