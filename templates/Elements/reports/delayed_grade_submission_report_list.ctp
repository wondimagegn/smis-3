<?php ?>
<style>
.bordering {
border-left:1px #cccccc solid;
border-right:1px #cccccc solid;
}
.bordering2 {
border-left:1px #000000 solid;
border-right:1px #000000 solid;
border-top:1px #000000 solid;
border-bottom:1px #000000 solid;
}
.courses_table tr td, .courses_table tr th {
padding:1px
}
</style>

<?php 

if (isset($delayedGradeSubmissionReportList) && !empty($delayedGradeSubmissionReportList)) {
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
                    <td class="bordering2">Delayed </td> 
                    
                </tr>     
               
<?php  
$count=0;  
foreach($delayedGradeSubmissionReportList as $departmentNamee=>$courseList) {
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
          
        <td class="bordering" style="<?php echo $ym['CourseInstructorAssignment']['grade_submission_deadline'] > date('Y-m-d') ? 'color:green':'color:red' ?>"><?php echo $this->Format->humanTiming($ym['CourseInstructorAssignment']['grade_submission_deadline']);?></td> 



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
