<?php 
/*
This file should be in app/views/elements/export_xls.ctp
Thanks to Marco Tulio Santos for this simple XLS Report
*/
header ("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=".$filename.".xls" );
header ("Content-Description: Exported as XLS" );
?>

<?php 

if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) {
  ?>
 <h5><?php echo $headerLabel;?></h5>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2">S.N<u>o</u> </td> 
                    <td class="bordering2">Instructor Department </td> 
		    <td class="bordering2">Course</td> 
                    <td class="bordering2">Instructor's Name</td>
                    <td class="bordering2">Section </td> 
		    <td class="bordering2">Program </td> 
                    <td class="bordering2">Program Type </td> 
                    <td class="bordering2">Deadline </td> 
                    
                </tr>     
               
<?php  
$count=0;  
foreach($gradeSubmissionDelay as $departmentNamee=>$courseList) {
    foreach ($courseList as $rkey => $rvalue) {

    ?>
   
     
    
        <?php 
   
        foreach($rvalue as $mn=>$ym){ 
         
          ?>
          <tr>
        <td class="bordering" > 
<?php 
       
           echo ++$count;
        
?>
        </td>
        <td class="bordering" > 
         <?php 
          echo $departmentNamee;
	?>
          </td> 
         <td class="bordering" > <?php echo $rkey;?>  </td> 
          <td class="bordering"><?php echo $ym['Staff']['Title']['title'].' '.$ym['Staff']['full_name'].'('.$ym['Staff']['Position']['position'].')';?> </td>
	 <td class="bordering"><?php 
	$year='';
        if(!isset($ym['Section']['YearLevel'])) {
            $year='1st';
	} else {
          $year=$ym['Section']['YearLevel']['name'];
	}
	echo $ym['Section']['name'].'('.$year.')';?> </td>

	 <td class="bordering" ><?php echo $ym['Section']['Program']['name'];?></td> 
          <td class="bordering" ><?php echo $ym['Section']['ProgramType']['name'];?></td> 
          
        <td class="bordering" style="<?php echo $ym['CourseInstructorAssignment']['grade_submission_deadline'] > date('Y-m-d') ? 'color:green':'color:red' ?>"><?php echo $this->Format->humanize_date($ym['CourseInstructorAssignment']['grade_submission_deadline']);?></td> 



    </tr>
   
        <?php } ?>

    
  <?php 
    }
 }
 ?>
 </table>
 <?php 
}   
?>
