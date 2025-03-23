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
<h5><?php echo $headerLabel;?></h5>
<?php 

if (isset($distributionStatsGraduate['distributionByDepartmentGraduate']) && !empty($distributionStatsGraduate['distributionByDepartmentGraduate'])) {
  ?>
 <h5><?php echo $headerLabel;?></h5>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2"> S.N<u>o</u> </td> 
                    <td class="bordering2"> Department </td> 
                     <td class="bordering2"> Region</td>
                    <td class="bordering2"> Gender </td> 
                    <td class="bordering2" colspan="<?php echo count($academicStatus);?>">Status</td> 
                </tr>     
                <tr>
                      <td class="bordering2"> &nbsp;</td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <?php 
                      foreach ($academicStatus as $ykey => $yvalue) {
                       
                        ?>
                   <td class="bordering2"> 
                   <?php echo $yvalue; ?> </td> 
                        <?php 
                       
                      }
                      ?>
                </tr>  
<?php  
$count=0;  
foreach($distributionStatsGraduate['distributionByDepartmentGraduate'] as $departmentNamee=>$regionss) {
    $nameDisplay=false;
    foreach ($regionss as $rkey => $rvalue) {

    ?>
    <?php if(isset($rvalue['male'])){ ?>
      <tr>
        <td class="bordering2" > 
<?php 
        if($nameDisplay==false){
           echo ++$count;
        }
?>
        </td>
        <td class="bordering2" > 
        <?php
        if($nameDisplay==false){
          echo $departmentNamee;
          $nameDisplay=true;
        }
         
         ?>  </td> 
         <td class="bordering2" > <?php echo $rkey;?>  </td> 
         <td class="bordering2" > Male</td> 
        <?php 
         $counttdm=0;
        foreach($rvalue['male'] as $mn=>$ym){
          $counttdm++;
         ?>
        <td class="bordering2"><?php echo $ym;?></td> 
        <?php } ?>


         <?php 
       
        for($counttdm;$counttdm<count($academicStatus);
        $counttdm++) { ?>
                   <td class="bordering2"></td>
        <?php } ?>

    </tr>
    <?php } ?>


    <?php if(isset($rvalue['female'])){ ?>
      <tr>
        <td class="bordering2" > 
<?php 
        if($nameDisplay==false){
           echo ++$count;
        }
?>
        </td>
        <td class="bordering2" > 
        <?php
        if($nameDisplay==false){
          echo $departmentNamee;
          $nameDisplay=true;
        }
         
         ?>  </td> 
         <td class="bordering2" > <?php echo $rkey;?>  </td> 
         <td class="bordering2" > Female</td> 
        <?php 
         $counttdf=0;
        foreach($rvalue['female'] as $mn=>$ym){
       $counttdf++;
         ?>
        <td class="bordering2"><?php echo $ym;?></td> 
        <?php } ?>

         <?php 
       
        for($counttdf;$counttdf<count($academicStatus);
        $counttdf++) { ?>
                   <td class="bordering2"></td>
        <?php } ?>

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
