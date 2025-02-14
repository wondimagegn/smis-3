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

<style>
.bordering {
border-left:1px #cccccc solid;
border-right:1px #cccccc solid;
}
.bordering2 {
border-left:1px #000000 solid;
border-right:1px #000000 solid;
}
.courses_table tr td, .courses_table tr th {
padding:1px
}
</style>

<?php 
if (isset($alumniSurvey) && !empty($alumniSurvey)) {
?>

<table>
        <tr>
        		<td style="border-left:1px #000000 solid; border-right:1px #000000 solid">S.No</td>
        	  	<td style="border-left:1px #000000 solid; border-right:1px #000000 solid">Full Name</td>
		<?php foreach($surveyQuestions as $sqk=>$sqv){ 
					
		?>
				<?php if($sqk==1) { ?>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid"   colspan="2" ><?php echo $sqv;?></td>
				<?php } else { ?>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid"><?php echo $sqv;?></td>
				<?php } ?>
		
		<?php } ?>
		
        </tr> 
        
     <tr>
        		<td style="border-left:1px #000000 solid; border-right:1px #000000 solid">&nbsp;</td>
        	  	<td style="border-left:1px #000000 solid; border-right:1px #000000 solid">&nbsp;</td>
		<?php foreach($surveyQuestions as $sqk=>$sqv){ 
					
		?>
				<?php if($sqk==1) { ?>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid">Mother</td>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid">Father</td>
				
				<?php } else { ?>
					<td style="border-left:1px #000000 solid; border-right:1px #000000 solid"> &nbsp;</td>
				<?php } ?>
		<?php } ?>
		
        </tr> 
<?php 
$count=0;

foreach($alumniSurvey as $student=>$list) {
    $headerExplode=explode('~',$student);
?>
	
    
         <tr>
		     <td style="border-left:1px #000000 solid; border-right:1px #000000 solid">&nbsp;</td>
			 <td style="border-left:1px #000000 solid; border-right:1px #000000 solid"> 
			 	<?php echo $headerExplode[0]; ?> 
			 </td> 
		 <?php 
		  foreach ($list as $ko=>$val) {
		  ?>
			
				
				<?php if($ko==1) { ?>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid"><?php echo $val['mother']['SurveyQuestionAnswer']['answer_english']; ?>   </td>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid"><?php echo $val['father']['SurveyQuestionAnswer']['answer_english']; ?>   </td>
				
				<?php }  else {  ?>
				<td style="border-left:1px #000000 solid; border-right:1px #000000 solid"> 
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
