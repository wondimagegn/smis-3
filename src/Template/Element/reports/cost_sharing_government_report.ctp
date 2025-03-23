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

if (isset($costSharingForMoE['StudentList']) && !empty($costSharingForMoE['StudentList'])) {
  $countColum=count($costSharingForMoE['CostSharingYearList'])*4+4+3+6;

  ?>
  
 <table style="width:100%">
                <tr>
                    <td style="text-align:center;" colspan="<?php echo $countColum;?>" class="bordering2"> ARBA MINCH UNIVERSITY </td>
                </tr>  
                 <tr>
                    <td style="text-align:center;" colspan="<?php echo $countColum;?>" class="bordering2"> OFFICE OF THE REGISTRAR </td>
                </tr>  
                <tr>
                  <td style="text-align:center;" colspan="<?php echo $countColum;?>" class="bordering2">COST SHARING STATISTICAL DATA</td>

                </tr>

                <tr>
                  <td style="text-align:center;" colspan="<?php echo $countColum;?>" class="bordering2">Cost sharing Debt (Agreement)of <?php echo $this->request->data['Report']['graduated_academic_year'];?> Ac.Yr Graduates</td>

                </tr>

                <tr>
                    <td rowspan="3" class="bordering2"> S.N<u>o</u> </td> 
                    <td rowspan="3" class="bordering2"> ID </td> 
                     <td rowspan="3" class="bordering2"> Name of Student</td>
                    <td rowspan="3" class="bordering2"> Sex </td> 
                    <td rowspan="3" class="bordering2"> Field of Study</td> 
                    <td rowspan="3" class="bordering2"> Date of graduation</td> 

                </tr>
                      <tr>
                  <?php foreach ($costSharingForMoE['CostSharingYearList'] 
                  as $ck=>$cvalue) { ?>
                
                          <th colspan="4"  class="bordering2" style="text-align:center;" 
                          class="bordering2"><?php echo $cvalue;?></th>
                    <?php }?>
                     <th colspan="4"  class="bordering2" style="text-align:center;" 
                          class="bordering2">Grand Total</th>


                    <td rowspan="2" class="bordering2"> Aggregating Unit Cost</td> 
                   <td rowspan="2" class="bordering2"> No. of costsharing </td> 
                      <td rowspan="2" class="bordering2"> Remark</td> 
                  </tr>
                    <tr>
                    <?php foreach ($costSharingForMoE['CostSharingYearList'] 
                  as $ck=>$cvalue) { ?>

                          <th class="bordering2">Education</th>
                          <th class="bordering2">Cafteria</th>

                          <th class="bordering2">Accommodation
                          </th>
                          <th class="bordering2">Medical
                          </th>
                   

                   <?php } ?>

                          <th class="bordering2">Education</th>
                          <th class="bordering2">Cafteria</th>

                          <th class="bordering2">Accommodation
                          </th>
                          <th class="bordering2">Medical
                          </th>
                     </tr>    
                   
                      <?php 
                      
                      $count=1;
                      $grandEdu=0;
                      $grandCafe=0; 
                      $grandAccom=0; 
                      $grandMedical=0; 
                      
                      foreach($costSharingForMoE['StudentList'] as $cck=>$ccv) { 
                        $explodedValues=explode('~', $cck);
                        $noCostSharing=0;
                        ?>
                        <tr>
                          <td class="bordering"><?php echo $count++;?></td>

                          <td class="bordering"><?php echo $explodedValues[2] ;?></td>
                          <td class="bordering"><?php echo $explodedValues[1] ;?></td>
                          <td class="bordering"><?php echo $explodedValues[3] ;?></td>
                          <td class="bordering"><?php echo $explodedValues[0] ;?></td>
                          <td class="bordering"><?php echo $explodedValues[4] ;?></td>
                          <?php 
                          foreach($costSharingForMoE['CostSharingYearList'] as $moek=>$moev){
                               if(isset($ccv[$moek])){
                                  $grandEdu+=$ccv[$moek]['education_fee'];
                                  $grandCafe+=$ccv[$moek]['cafeteria_fee'];
                                  $grandAccom+=$ccv[$moek]['accomodation_fee'];
                                  $grandMedical+=$ccv[$moek]['medical_fee'];
                                  $noCostSharing++;

                            ?>
                                 <td class="bordering"><?php echo $ccv[$moek]['education_fee'];?></td>
                                 <td class="bordering"><?php echo $ccv[$moek]['accomodation_fee'];?></td>

                                 <td class="bordering"><?php echo $ccv[$moek]['cafeteria_fee'];?></td>
                                 <td class="bordering"><?php echo $ccv[$moek]['medical_fee'];?></td>
                          <?php 
                               } else if(!isset($ccv[$moek])){
                                ?>
                                 <td class="bordering">---</td>
                                 <td class="bordering">---</td>
                                 <td class="bordering">---</td>
                                 <td class="bordering">---</td>
                                <?php 
                               }
                           }
                          ?> 
                         
                          <td class="bordering"><?php echo $grandEdu; ?></td>
                          <td class="bordering"><?php echo $grandCafe; ?></td>
                           <td class="bordering"><?php echo $grandAccom; ?></td>
                         <td class="bordering"><?php echo $grandMedical; ?></td>

                           <td class="bordering">
                             <?php 
                              echo $grandEdu+$grandCafe+$grandAccom+$grandMedical;
                             ?>

                           </td>
                          <td class="bordering">
                            <?php 
                              echo  $noCostSharing;
                            ?>
                          </td>
                          <td class="bordering">&nbsp;</td>

                        </tr>

                      <?php 
                }
                ?>

                <?php  
         ?>
 </table>
 <?php 
}   
?>