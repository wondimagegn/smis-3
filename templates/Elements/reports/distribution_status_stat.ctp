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

if (isset($distributionStatisticsStatus['distributionByStatusYearLevel']) && !empty($distributionStatisticsStatus['distributionByStatusYearLevel'])) {
  ?>
 <h5><?php echo $headerLabel;?></h5>
  <?php 
  echo $this->element('reports/graph');
  ?>
 <table style="width:100%">
                   
                <tr>
                    <td class="bordering2"> S.N<u>o</u> </td> 
                    <td class="bordering2"> Department </td> 
                     <td class="bordering2"> Status</td>
                    <td class="bordering2"> Gender </td> 
                    <td class="bordering2" colspan="<?php echo count($years);?>">Year Level</td> 
                </tr>     
                <tr>
                      <td class="bordering2"> &nbsp;</td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <td class="bordering2"> &nbsp; </td> 
                      <?php 
                      if(empty($this->data['Report']['year_level_id'])){ 
                      foreach ($years as $sk => $svalue) {
                       
                        ?>
                   <td class="bordering2"> 
                   <?php echo $svalue; ?> </td> 
                        <?php 
                       
                      }

                  } else if(!empty($this->data['Report']['year_level_id'])) {
                      ?>
                       <td class="bordering2"> 
                   <?php echo $this->data['Report']['year_level_id']; ?> </td> 

                      <?php 
                  }
                  ?>
                </tr>  
<?php  
$count=0;  
foreach($distributionStatisticsStatus['distributionByStatusYearLevel'] as $departmentNamee=>$regionss) {
    $nameDisplay=false;
    foreach ($regionss as $rkey => $rvalue) {

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
        if(empty($this->data['Report']['year_level_id'])){
        $counttdm=0;
        foreach($rvalue['male'] as $mn=>$ym){ 
          $counttdm++;
          ?>
        <td class="bordering"><?php echo $ym;?></td> 
        <?php } ?>

         <?php 
       
        for($counttdm;$counttdm<count($years);
        $counttdm++) { ?>
                   <td class="bordering">&nbsp;</td>
        <?php } ?>
        <?php } else if(!empty($this->data['Report']['year_level_id'])){ ?>
                  <td class="bordering">
                  	
                  	<?php 
                  		echo $rvalue['male'][$this->data['Report']['year_level_id']];
                  	?>
                  </td>
        <?php } ?>
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
        if(empty($this->data['Report']['year_level_id'])){
        $counttdf=0;
        foreach($rvalue['female'] as $mn=>$ym){ 
          $counttdf++;
          ?>
        <td class="bordering"><?php echo $ym;?></td> 
        <?php } ?>
        <?php 
       
        for($counttdf;$counttdf<count($years);
        $counttdf++) { ?>
                   <td class="bordering"></td>
        <?php 
          } 
         } else if(!empty($this->data['Report']['year_level_id'])){
       ?>
                <td class="bordering"><?php echo $rvalue['female'][$this->data['Report']['year_level_id']];?></td> 

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