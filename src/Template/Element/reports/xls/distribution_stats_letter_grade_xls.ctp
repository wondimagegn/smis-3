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

if (isset($distributionStatsLetterGrade['distributionLetterGrade']) 
  && !empty(
    $distributionStatsLetterGrade['distributionLetterGrade'])) 
{
  ?>
 <h5><?php echo $headerLabel;?></h5>
  <?php 
  //echo $this->element('reports/graph');
  ?>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2"> S.N<u>o</u> </td> 
                    <td class="bordering2">Department </td> 
                    <td class="bordering2">Course</td>
                    <td class="bordering2"> Gender </td> 

                    <td class="bordering2" colspan="<?php echo count($letterGrades);?>">Letter Grade</td> 
                </tr>     
                <tr>
                      <td class="bordering"> &nbsp;</td> 
                      <td class="bordering"> &nbsp; </td> 
                      <td class="bordering"> &nbsp; </td> 
                      <td class="bordering"> &nbsp; </td> 
                      <?php 
                     
                      foreach ($letterGrades as $sk => $svalue) {
                       
                        ?>
                   <td class="bordering"> 
                   <?php echo $svalue; ?> </td> 
                        <?php 
                       
                      }

                  ?>
                </tr>  
<?php  
$count=1;  
foreach($distributionStatsLetterGrade['distributionLetterGrade'] as $departmentNamee=>$regionss) {
    $nameDisplay=false;
    foreach ($regionss as $rkey => $rvalue) {
    ?>

    <?php if(isset($rvalue['male'])){ ?>
      <tr>
        <td class="bordering" > 
                 <?php echo $count++;?>
        </td>
        <td class="bordering" > 
        <?php
      
          echo $departmentNamee;
         
         ?>  </td> 
         <td class="bordering"> <?php echo $rkey;?>  </td> 
         <td class="bordering"> Male</td> 
        <?php 
          foreach ($letterGrades as $lgv) {
             if(isset($rvalue['male'][$lgv])){
                  echo "<td class='bordering'>".$rvalue['male'][$lgv]."</td>";
             } else {
                 echo "<td class='bordering'>0</td> ";
             }
             
         }
        ?>
    </tr>
    <?php } ?>



    <?php if(isset($rvalue['female'])){ ?>
      <tr>
        <td class="bordering" > 
                 <?php echo $count++;?>
        </td>
        <td class="bordering" > 
        <?php
      
          echo $departmentNamee;
         
         ?>  </td> 
         <td class="bordering"> <?php echo $rkey;?>  </td> 
         <td class="bordering"> Female</td> 
        <?php 
          foreach ($letterGrades as $lgv) {
             if(isset($rvalue['female'][$lgv])){
                  echo "<td class='bordering'>".$rvalue['female'][$lgv]."</td>";
             } else {
                 echo "<td class='bordering'>0</td> ";
             }
             
         }
        ?>
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
