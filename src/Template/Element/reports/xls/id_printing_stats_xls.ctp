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

if (isset($distributionIDPrintingCount['distributionIDPrintingCount']) && !empty($distributionIDPrintingCount['distributionIDPrintingCount'])) {
  ?>
 <h5><?php echo $headerLabel;?></h5>
  <?php 
 // echo $this->element('reports/graph');
  ?>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2"> S.N<u>o</u> </td> 
                    <td class="bordering2"> Department </td> 
                    <td class="bordering2"> Print Count</td>
                    <td class="bordering2"> Gender </td> 
                    <?php  if(empty($this->data['Student']['year_level_id'])){ ?>
                    <td class="bordering2" colspan="<?php echo count($years);?>">Year Level</td> 
                    <?php } else { ?>
                       <td class="bordering2">Year Level</td> 
                    <?php } ?>
                    <td class="bordering2">Total</td>
                </tr>     
                <tr>
                      <td class="bordering2"> &nbsp;</td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <?php 
                      if(empty($this->data['Student']['year_level_id'])){ 
                      foreach ($years as $sk => $svalue) {
                       
                        ?>
                   <td class="bordering2"> 
                   <?php echo $svalue; ?> </td> 
                        <?php 
                       
                      }

                  } else if(!empty($this->data['Student']['year_level_id'])) {
                      debug($this->data['Student']['year_level_id']);
                      ?>
                       <td class="bordering2"> 

                        <?php echo $this->data['Student']['year_level_id']; ?> 
                      </td> 

                      <?php 
                  }
                  ?>
                   <td class="bordering2"> &nbsp; </td> 
                </tr>  
<?php  
$count=0;  
$totalCountYear=array();
foreach($distributionIDPrintingCount['distributionIDPrintingCount'] as $departmentNamee=>$printCounts) {
    $nameDisplay=false;
   
    foreach ($printCounts as $rkey => $rvalue) {
     $totalCountDept['male']=0;
    $totalCountDept['female']=0;
    ?>
    <?php if(isset($rvalue['male'])){ ?>
      <tr>
        <td class="bordering" > 
<?php 
        if($nameDisplay==false){
           echo ++$count;
        }
?>
        </td>
        <td class="bordering" > 
        <?php
        if($nameDisplay==false){
          echo $departmentNamee;
          $nameDisplay=true;
        }
         
         ?>  </td> 
         <td class="bordering" > <?php echo $rkey;?>  </td> 
         <td class="bordering" > Male</td> 
        <?php 
        if(empty($this->data['Student']['year_level_id'])){
        $counttdm=0;

        foreach($rvalue['male'] as $mn=>$ym){ 
          $counttdm++;
               $totalCountDept['male']+=$ym;
               $totalCountYear[$mn]+=$ym;

          ?>
        <td class="bordering"><?php echo $ym;?></td> 
        <?php } ?>

         <?php 
       
        for($counttdm;$counttdm<count($years);
        $counttdm++) { ?>
                   <td class="bordering">&nbsp;</td>
        <?php } ?>
        <?php } else if(!empty($this->data['Student']['year_level_id'])){ ?>
                  <td class="bordering">
                    
                    <?php 
                      $totalCountDept['male']+=$rvalue['male'][$this->data['Student']['year_level_id']];
                  
                     //$totalCountDept['male']+=$rvalue['male'][$this->data['Student']['year_level_id']];
                       $totalCountYear[$this->data['Student']['year_level_id']]+= $rvalue['male'][$this->data['Student']['year_level_id']];
                      echo $rvalue['male'][$this->data['Student']['year_level_id']];
                    ?>
                  </td>
        <?php } ?>
         <td class="bordering"><?php echo $totalCountDept['male'];?></td>
    </tr>
    <?php } ?>


    <?php if(isset($rvalue['female'])){ ?>
      <tr>
        <td class="bordering" > 
<?php 
        if($nameDisplay==false){
           echo ++$count;
        }
?>
        </td>
        <td class="bordering" > 
        <?php
        if($nameDisplay==false){
          echo $departmentNamee;
          $nameDisplay=true;
        }
         
         ?>  </td> 
         <td class="bordering" > <?php echo $rkey;?>  </td> 
         <td class="bordering" > Female</td> 
        <?php 
        if(empty($this->data['Student']['year_level_id'])){
        $counttdf=0;
        foreach($rvalue['female'] as $mn=>$ym){ 
          $counttdf++;
           //$totalCountYear[$mn]+=$totalCountYear[$mn];
           $totalCountDept['female']+=$ym;
           $totalCountYear[$mn]+= $ym;
           //$totalCountDept['female']+=$ym;
          ?>
        <td class="bordering"><?php echo $ym;?></td> 
        <?php } ?>
        <?php 
       
        for($counttdf;$counttdf<count($years);
        $counttdf++) { ?>
                   <td class="bordering"></td>
        <?php 
          } 
         } else if(!empty($this->data['Student']['year_level_id'])){
           $totalCountDept['female']+=$rvalue['female'][$this->data['Student']['year_level_id']];
           $totalCountYear[$this->data['Student']['year_level_id']]+=$rvalue['female'][$this->data['Student']['year_level_id']];

         

       ?>
                <td class="bordering"><?php echo $rvalue['female'][$this->data['Student']['year_level_id']];?></td> 

       <?php } ?>
       <td class="bordering"><?php echo $totalCountDept['female'];?></td>
    </tr>
    <?php } ?>

    
  <?php 
    }
 }
 
 ?>
                   <?php 
                    $sum=0;
                    if(empty($this->data['Student']['year_level_id'])){ 
                        foreach ($totalCountYear as $sk => $svalue) {
                          ?>
                          
                          <?php 
                        }
                     } else if(!empty($this->data['Student']['year_level_id'])) {
                      
                      ?>
                     
                      <?php 
                  }
                  ?>
        
 </table>
 <?php 
}   
?>