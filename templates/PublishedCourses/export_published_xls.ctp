<?php
    /**
     * Export all member records in .xls format
     * with the help of the xlsHelper
     */    
     $this->Xls->setHeader('Published courses');
     $this->Xls->addXmlHeader();
     $this->Xls->setWorkSheetName('Published courses of '.$selected_academic_year.' academic year');
     
     if(!empty($publishedCourses)){
       foreach($publishedCourses as $sk=>$sv){
                 $this->Xls->openRow();
                    $this->Xls->writeString("Courses published for registration of semester $sk of $selected_academic_year academic year ");
                $this->Xls->closeRow();  
                foreach ($sv as $pk => $pv) {            
                       $this->Xls->openRow(); 
                               $this->Xls->writeString("Program ".$pk);
                       $this->Xls->closeRow();  
                       foreach ($pv as $ptk=>$ptv) {
                            $this->Xls->openRow(); 
                               $this->Xls->writeString("Program Type ".$ptk);
                            $this->Xls->closeRow();  
                            foreach ($ptv as $yk=>$yv) {
                                     $this->Xls->openRow(); 
                                        $this->Xls->writeString("Year Level ".$yk);
                                     $this->Xls->closeRow();  
                                      foreach ($yv as $section_name=>$section_value) {
                                        $this->Xls->openRow(); 
                                            $this->Xls->writeString("Section ".$section_name);
                                        $this->Xls->closeRow(); 
                                        $this->Xls->openRow();
                                             $this->Xls->writeString('No.');
                                             $this->Xls->writeString('Course Title');
                                             $this->Xls->writeString('Course Code');
                                             $this->Xls->writeString('Credit');
                                             $this->Xls->writeString('L T L');                                          
                                        $this->Xls->closeRow();
                                        $count=1;
                                        
                                    foreach ($section_value as $type_index=>$section_value_detail) {
                                             $this->Xls->openRow();
                                                $this->Xls->writeString($type_index);
                                             $this->Xls->closeRow();
                                         foreach ($section_value_detail as $publishedCourse) {
                                             $this->Xls->openRow();
                                                $this->Xls->writeString($count++);
                                                $this->Xls->writeString($publishedCourse['Course']['course_title']);
                                                $this->Xls->writeString($publishedCourse['Course']['course_code']);
                                                $this->Xls->writeString($publishedCourse['Course']['credit']);
                                                $this->Xls->writeString($publishedCourse['Course']['course_detail_hours']);
                                               // $this->Xls->closeRow();
                           
                                         }
                                         
                                        }
                                         
                                         
                                     }
                            }
                       }
                        
                }               
                    
        }
     }
    $this->Xls->addXmlFooter();
    exit();
?>
