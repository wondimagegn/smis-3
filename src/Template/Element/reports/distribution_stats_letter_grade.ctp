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