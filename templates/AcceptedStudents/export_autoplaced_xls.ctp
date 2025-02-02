<?php
    /**
     * Export all member records in .xls format
     * with the help of the xlsHelper
     */
    
     $this->Xls->setHeader('Auto Placement Result');
     $this->Xls->addXmlHeader();
     $this->Xls->setWorkSheetName('Auto Placement Result');
     
     if(!empty($autoplacedstudents)){
        $summery=$autoplacedstudents['auto_summery'];
        $this->Xls->openRow();
                $this->Xls->writeString("Summery of Auto Placement");
        $this->Xls->closeRow();
        $this->Xls->openRow();
                $this->Xls->writeString("Department");
                $this->Xls->writeString("Competitive Assignment");
                $this->Xls->writeString("Privilaged Quota Assignment");
        $this->Xls->closeRow();
        foreach ($summery as $sk=>$sv){  
            $this->Xls->openRow();
                $this->Xls->writeString($sk);
                $this->Xls->writeString($sv['C']);
                $this->Xls->writeString($sv['Q']);
            $this->Xls->closeRow();
        
        }
        unset($autoplacedstudents['auto_summery']);
         ?>
       
		<?php 
		
       
        foreach($autoplacedstudents as $key =>$data){
                $this->Xls->openRow();
                    $this->Xls->writeString($key);
                $this->Xls->closeRow();      
                $this->Xls->openRow();
                     $this->Xls->writeString('Full Name');
                     $this->Xls->writeString('Sex');
                     $this->Xls->writeString('Student Number');
                     $this->Xls->writeString('EHEECE Result');
                    // $this->Xls->writeString('EHEECE_total_results');
                     $this->Xls->writeString('Department');
                     $this->Xls->writeString('Preference Order');
                     $this->Xls->writeString('Placement Type');
                     $this->Xls->writeString('Placement Based');
                $this->Xls->closeRow();
                foreach($data as $acceptedStudent){
                         $this->Xls->openRow();
                             $this->Xls->writeString($acceptedStudent['AcceptedStudent']['full_name']);
                             $this->Xls->writeString($acceptedStudent['AcceptedStudent']['sex']);
                             $this->Xls->writeString($acceptedStudent['AcceptedStudent']['studentnumber']);
                             $this->Xls->writeString($acceptedStudent['AcceptedStudent']['EHEECE_total_results']);
                             $this->Xls->writeString($acceptedStudent['Department']['name']);
                             //preference
                             if(!empty($acceptedStudent['Preference'])){
		                               foreach($acceptedStudent['Preference'] as $pk=>$pv){
		                                if($pv['department_id']==$acceptedStudent['Department']['id']){
	                                        
	                                        	 $this->Xls->writeString($pv['preferences_order']); 
	                                        	break;
	                                	}
		                            }
		                     } else {
		                        $this->Xls->writeString(null); 
		                     }
                            
                            $this->Xls->writeString($acceptedStudent['AcceptedStudent']['placementtype']);
                            $this->Xls->writeString($acceptedStudent['AcceptedStudent']['placement_based']=="C" ? "Competitive":"Privilaged Quota");
                      $this->Xls->closeRow();
                }
        }
     }
    $this->Xls->addXmlFooter();
    exit();
?>
