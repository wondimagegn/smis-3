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
if (isset($alumniSurvey) && !empty($alumniSurvey)) {
?>

<table style="width:100%">
        <tr>
        		<td>S.No</td>
        	  	<td>Full Name</td>
		<?php foreach($surveyQuestions as $sqk=>$sqv){ 
					
		?>
				<?php if($sqk==1) { ?>
				<td   colspan="2" ><?php echo $sqv;?></td>
				<?php } else { ?>
				<td><?php echo $sqv;?></td>
				<?php } ?>
		
		<?php } ?>
		
        </tr> 
        
     <tr>
        		<td>&nbsp;</td>
        	  	<td>&nbsp;</td>
		<?php foreach($surveyQuestions as $sqk=>$sqv){ 
					
		?>
				<?php if($sqk==1) { ?>
				<td>Mother</td>
				<td>Father</td>
				
				<?php } else { ?>
					<td> &nbsp;</td>
				<?php } ?>
		<?php } ?>
		
        </tr> 
<?php 
$count=0;

foreach($alumniSurvey as $student=>$list) {
    $headerExplode=explode('~',$student);
?>
	
    
         <tr>
		     <td>&nbsp;</td>
			 <td> 
			 	<?php echo $headerExplode[0]; ?> 
			 </td> 
		 <?php 
		  foreach ($list as $ko=>$val) {
		  ?>
			
				
				<?php if($ko==1) { ?>
				<td><?php echo $val['mother']['SurveyQuestionAnswer']['answer_english']; ?>   </td>
				<td><?php echo $val['father']['SurveyQuestionAnswer']['answer_english']; ?>   </td>
				
				<?php }  else {  ?>
				<td> 
					<?php 
					if(isset($val['answer']['SurveyQuestionAnswer']) && !empty($val['answer']['SurveyQuestionAnswer']))
					{
						echo $val['answer']['SurveyQuestionAnswer']['answer_english']; 
					} else if(is_string($val['answer'])) {
						echo $val['answer'];
					} else if(is_array($val['answer'])) {
					    foreach($val['answer'] as $ank=>$anv){
					       echo $anv['SurveyQuestionAnswer']['answer_english'].' ';
					    }
					}
				?>
				 </td> 
				<?php } ?>
			  
          <?php 
          }
          ?>
          </tr>   
   
<?php 
}
?> 
 </table>  
 <?php 
}   
?>

