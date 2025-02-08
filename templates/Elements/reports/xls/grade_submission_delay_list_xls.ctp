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

if (isset($gradeChangeLists) && !empty($gradeChangeLists)) {
  ?>
 <h5><?php echo $headerLabel;?></h5>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2"> S.N<u>o</u> </td> 
		    <td class="bordering2"> &nbsp;&nbsp;&nbsp;</td> 
                    <td class="bordering2"> Instructor </td> 
                    <td class="bordering2"> Student </td> 
                    <td class="bordering2"> Old Grade </td> 
                    <td class="bordering2"> New Grade </td>
                    <td class="bordering2"> Course </td> 
		     <td class="bordering2"> Initiated By </td> 
                   
                </tr>     
               
<?php  
$count=0;  
foreach($gradeChangeLists as $staffName=>$courseList) { 
     foreach($courseList as $ck=>$cd){ 
      ?>
      <tr>
        <td class="bordering"><?php echo ++$count; ?> </td> 
        <td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
        <td class="bordering"><?php echo $staffName; ?>  </td> 
        <td class="bordering"><?php 
	  echo $cd['full_name'];
?></td> 
       
         <td class="bordering"><?php echo $cd['oldGrade']; ?></td> 
         <td class="bordering"><?php echo $cd['grade']; ?></td> 
	  <td class="bordering"><?php echo $cd['course']; ?></td> 
	  <td class="bordering"><?php 
            if($cd['manual_ng_conversion'] == 1)
			echo 'Registrar NG Grade Conversion';
	    else if($cd['auto_ng_conversion'] == 1)
		       echo 'Automatic F';
            else if ($cd['initiated_by_department']==1)
			echo "Department";
	    else if ($cd['initiated_by_department']==0)
			echo "Instructor"; 
	

?></td> 
     </tr>
     
  <?php } ?> 
   
  <?php 
 }
 ?>
 </table>
 <?php 
}   
?>
