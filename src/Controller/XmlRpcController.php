<?php  

// Import the app/vendor/xmlrpc.php library 
App::import('Vendor', 'xmlrpc'); 
App::import('Xml'); 

class XmlRpcController extends AppController { 

    // This demo doesn't need models 
    var $uses = array('Student','Section', 'College'); 
     
    // The XML-RPC server object 
    var $server = null; 

    function beforeFilter(){
         parent::beforeFilter();
        
         $this->Auth->allow('index');
    }
     
    function index() { 
        // Disable debug information 
        // Required to generate valid XML output 
        Configure::write('debug', 0);  
        
        // Avoids render() call 
        $this->autoRender = false; 
       
        // XML-RPC callbacks settings 
        // Use this parameter to map XML-RPC methods to your protected or private controller methods 
        $callbacks = array(); 
        $callbacks['department'] = array(&$this, '_department');
        $callbacks['studentView']=array(&$this,'_studentView');
        $callbacks['college'] = array(&$this, '_college'); 
        $callbacks['section'] = array(&$this, '_section'); 
        $callbacks['student'] = array(&$this, '_student'); 
        $callbacks['studentDetail'] = array(&$this, '_studentDetail'); 
        $callbacks['studentDBIDs'] = array(&$this, '_studentDBIDs'); 
        $callbacks['studentNameNumber'] = array(&$this, '_studentNameNumber'); 
         
        
      
        // Handle XML-RPC request 
        $this->server = new IXR_Server($callbacks);
        
        
        
    } 
  
    // Protected Method 
   // Protected Method
    function _department($college_id=null) {
     
       return $this->Student->Department->find('list', array('conditions'=>
       array('Department.college_id'=>$college_id))); 
    }
    
    function _college($id=null) {
    return $this->College->find('list');
//              return $this->Student->Department->College->find('list'); 
    }
    
    function _studentView($id=null) {
        if (!$id) { 
            return new IXR_Error(2, 'Invalid student'); 
        } 
        return $this->Student->read(null, $id); 
    }
    // Protected Method 
    function _section($department_id=null) { 
          
		   return $this->Section->get_sections_by_dept($department_id);
		 
    } 
    
    function _student($section_id=null) {
            $students = $this->Section->allStudents($section_id);
            return $students;
    }
     function _studentDetail($student_id=null) {
            $student_basic_detail = $this->Student->get_student_details($student_id);
            return $student_basic_detail;
    }
    
    function _studentDBIDs($name=null){
        $student_ids = $this->Student->find('list',
        array('conditions'=>array('Student.first_name like'=>trim($name).'%'),'fields'=>array('Student.id','Student.id')));
        return $student_ids;
    }
    
    function _studentNameNumber($student_ids=array()) {
        return $this->Student->getStudentLists($student_ids);
    }
    
    
} 
?>
