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
if (isset($distributionStatistics['distributionByDepartmentYearLevel']) && !empty($distributionStatistics['distributionByDepartmentYearLevel'])) {

  ?>
 <h5><?php echo $headerLabel;?></h5>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2"> S.N<u>o</u> </td> 
                    <td class="bordering2"> Department </td> 
                    <td class="bordering2"> Gender </td> 
                    <td class="bordering2" colspan="<?php echo count($years);?>">Year Level</td> 
                </tr>     
                <tr>
                      <td class="bordering2"> &nbsp;</td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <?php 
                      foreach ($years as $ykey => $yvalue) {
                       
                        ?>
                   <td class="bordering2"> 
                   <?php echo $yvalue; ?> </td> 
                        <?php 
                       
                      }
                      ?>
                     
                    
                </tr>  
<?php  
 $count=0;  
foreach($distributionStatistics['distributionByDepartmentYearLevel'] as 
  $departmentName=>$yll) {
    ?>
      <tr>


        <td class="bordering2" > <?php echo ++$count; ?> </td> 
        <td class="bordering2" > <?php echo $departmentName; ?>  </td> 
         <td class="bordering2" > Male</td> 
        <?php foreach($yll as $yn=>$yv){ ?>
        <td class="bordering2"><?php echo $yv['male'];?></td> 
        <?php } ?>
 
    </tr>

     <tr>


        <td class="bordering2"></td> 
        <td class="bordering2"></td> 
        <td class="bordering2">Female</td> 
        <?php foreach($yll as $yn=>$yv){ ?>
        <td class="bordering2"><?php echo $yv['female'];?></td> 
        <?php } ?>
 
    </tr>
        
  <?php 
 }
 ?>
 </table>
 <?php 
}   
?>