<?php ?>
<script type="text/javascript">
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
function toggleViewFullId(id, label) {
	if($('.'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		if(label == 1) {
			$('#'+id+'Txt').empty();
			$('#'+id+'Txt').append(' Hide Options');
		}
	}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		if(label == 1) {
			$('#'+id+'Txt').empty();
			$('#'+id+'Txt').append(' Display Options');
		}
	}
	$('.'+id).toggle("slow");
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="attrationView index">
<?php //echo $this->Form->create('SenateList');?>
<?php echo $this->Form->Create('Report'); ?>
<div class="smallheading"><?php echo __('Attration Rate View'); ?></div>

<div style="margin-top:0px" onclick="toggleViewFullId('AttrationRateView', 1)"><?php 
	if (!isset($attrationRate) || empty($attrationRate)) {
		echo $this->Html->image('minus2.gif', array('id' => 'AttrationRateViewImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="AttrationRateViewTxt"> 
		Hide Options</span><?php
		$display = 'display';
		}
	else {
		echo $this->Html->image('plus2.gif', array('id' => 'AttrationRateViewImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="AttrationRateViewTxt">
		Display Options</span><?php
		$display = 'none';
		}
?></div>

<table cellspacing="0" cellpadding="0" class="fs13">
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td style="width:11%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?></td>
		<td style="width:11%">Program Type:</td>
		<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
	</tr>
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td>Department:</td>
		<td colspan="3"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 
		'class' => 'fs13', 'label' => false, 'type' => 'select', 
		'style'=>'width:200px',
		'options' => $departments, 
		'default' => $default_department_id)); ?></td>
	</tr>
	
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
	</tr>
	
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td colspan="6">
		<?php echo $this->Form->submit(__('Get Report'), array('name' => 'getReport', 'div' => false)); ?>
		</td>
	</tr>
</table>
<?php echo $this->Form->end();
?>
</div>
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
if (isset($attrationRate) && !empty($attrationRate)) {

$table_width = (count($yearLevel)*10) + (count($yearLevel)*10) + 86;
$universityStat=$attrationRate['University'];
unset($attrationRate['University']);
foreach($attrationRate as $program=>$programType) {
    foreach ($programType as $programTypeName=> $statDetail) {
?>
<p class="fs16">
        Student Attration rate of <?php echo $this->request->data['Report']['acadamic_year']; ?> AY, Semester
         <?php  echo $this->request->data['Report']['semester']; ?> <br/>
        <strong> Program : </strong>   <?php 
              echo $program;
            ?>
            <br/>
        <strong> Program Type: </strong>  <?php 
              echo $programTypeName;
              
             
            ?>
</p>

<table style="width:100%">
  
    <tr>
		<th rowspan="2" class="bordering2" style="vertical-align:bottom; width:2%">S.N<u>o</u>
		</th>
		
		<th rowspan="2"  class="bordering2" style="vertical-align:bottom; width:15%">College/Institute name</th>
		<th rowspan="2" class="bordering2"  style="vertical-align:bottom; width:8%">Department Name</th>
		<?php 
		$percent = 10;
		$last_percent = false;
		$total_percent = (count($yearLevel)*10) + (count($yearLevel)*10) + 86;
		if($total_percent > 100) {
			//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
		}
		else if($total_percent < 100) {
			$last_percent = 100 - $total_percent;
		}
		
		?>
		
		<?php foreach ($yearLevel as $k=>$value) { ?>
		
		<th colspan="4"  class="bordering2" style="text-align:center; width:<?php echo $percent; ?>%" 
		class="bordering2"><?php echo $value;?></th>
	   <?php } ?>
	   
	   	<th colspan="4" class="bordering2" style="text-align:center; width:15%" class="bordering2">Grand Total</th>	
    </tr>
    <tr>
       
		
			<?php foreach ($yearLevel as $k=>$value) { ?>
		
		        <th style="width:5%" class="bordering2">M</th>
		<th style="width:5%" class="bordering2">F</th>
		<th style="width:5%" class="bordering2">Total</th>
		<th style="width:5%" class="bordering2">Rate(%)</th>
	   <?php } ?>
		
		<th style="width:5%" class="bordering2">M</th>
		<th style="width:5%" class="bordering2">F</th>
		<th style="width:5%" class="bordering2">Dept. Total</th>
		<th style="width:5%" class="bordering2">Dept. Rate(%)</th>
		
    </tr>
   
  
    <?php     
    $count = 0;
 
    foreach($statDetail as $college => &$stat) {
   
        
    ?>
  
              <?php
                
                if (strcmp($college,'College')!==0) {
                    $copyCollegeGrand=$stat['College'];
                    unset($stat['College']);
                    
                     $grand_college_sum_female=0;
                     $grand_college_sum_male=0;
                     $grand_college_total=0;
                     $grand_college_rate=0;
                   
                    foreach ($stat as $department=>$deptyearLevel) {
                           
                         $count++;
              ?>
                   <tr>
                       <td class="bordering2">  <?php echo $count; ?> </td>
                       <td class="bordering2">  <?php echo $college; ?> </td>
                       <td class="bordering2">  <?php echo $department; ?> </td>
                       <?php 
                          $grand_total_female=0;
                          $grand_total_male=0;
                          $dept_total=0;
                          $dept_rate=0;
                         
                      foreach ($yearLevel as $yk=>$yvalue) {
                     
                      if (isset($deptyearLevel[$yvalue])) {
                       
                          
                       ?>
                         <td class="bordering2"> <?php 
                           echo $deptyearLevel[$yvalue]['male_total'];
                           
                           ?> 
                         </td>
                         <td class="bordering2"> 
                         <?php 
                         echo $deptyearLevel[$yvalue]['female_total'];
                         ?> </td>
                         <td class="bordering2"> <?php 
                         
                         echo $deptyearLevel[$yvalue]['total'];?> </td>
                         
                         <td class="bordering2"> <?php 
                         if ($deptyearLevel[$yvalue]['total']>0) {
                         echo number_format(
                           ($deptyearLevel[$yvalue]['male_total']+$deptyearLevel[$yvalue]['female_total'])/
                           $deptyearLevel[$yvalue]['total'],3, '.', '');
                         } else {
                            echo "0";
                         }
                         ?> 
                         
                         </td>  
                     <?php
                         $grand_total_female+=$deptyearLevel[$yvalue]['female_total'];
                      $grand_total_male +=$deptyearLevel[$yvalue]['male_total'];
                      $dept_total +=$deptyearLevel[$yvalue]['total']; 
                      
                        
                        } else {
                       
                      ?>
                          
                          <td class="bordering2"> 
                          --
                         </td>
                         <td class="bordering2"> 
                           --
                         </td>
                         <td class="bordering2"> 
                            --
                          </td>
                         
                         <td class="bordering2">
                           --
                         </td>  
                      
                      <?php 
                          $grand_total_female+=0;
                      $grand_total_male +=0;
                      $dept_total +=0; 
                      }
                    
                      
                     ?>
                     
                      
                       
                    
              <?php 
                       
                      } //foreach 
                 
                    ?>
                    
                       <td class="bordering2"> 
                         
                         <?php echo $grand_total_male; ?> </td>
                         <td class="bordering2">
                         
                          <?php echo $grand_total_female; ?> </td>
                       
                         <td class="bordering2"> 
                         
                         <?php echo $dept_total; ?> </td>
                         <td class="bordering2"> 
                        
                         <?php 
                            if ($dept_total>0) {
                           echo number_format(
                           ($grand_total_female+$grand_total_male)/$dept_total,3, '.', '');
                         
                            } else {
                              echo "0";
                            }
                         ?> 
                         
                       </td>  
                    
                   </tr>  
                   
                   <?php
                   }
                    $count=0;
                    ?>
                     <tr>
                        <td class="bordering2">&nbsp;</td>
                         <td class="bordering2" >  Sub Total </td>
                          <td class="bordering2">  <?php // echo $college ?> </td>
                      <?php 
                       // college  grand total 
                         if (isset($copyCollegeGrand) && !empty($copyCollegeGrand)) {
                         $college_grand_total_female=0;
                         $college_grand_total_male =0;
                         $college_total =0;
                         foreach ($yearLevel as $yk=>$yvalue) {
                            if (isset($copyCollegeGrand[$yvalue])) { 
                             $college_grand_total_female+=$copyCollegeGrand[$yvalue]['female_total'];
                            $college_grand_total_male +=$copyCollegeGrand[$yvalue]['male_total'];
                            $college_total +=$copyCollegeGrand[$yvalue]['total'];
                             
                      ?>
                         
                          <td class="bordering2"><?php echo $copyCollegeGrand[$yvalue]['male_total'];?> </td>
                         <td class="bordering2"><?php echo $copyCollegeGrand[$yvalue]['female_total']?> </td>
                         <td class="bordering2"><?php echo $copyCollegeGrand[$yvalue]['total'];?> </td>
                         <td class="bordering2"> <?php
                          if ($copyCollegeGrand[$yvalue]['total']>0) {
                             echo number_format(($copyCollegeGrand[$yvalue]['male_total']+
                         $copyCollegeGrand[$yvalue]['female_total'])/$copyCollegeGrand[$yvalue]['total'],3, '.', '');
                      
                          } else {
                                echo "0";
                          }
                              ?> 
                          
                          
                          </td>  
    
                     
                      <?php 
                            }
                            
                        }
                      ?>
                             
                          <td class="bordering2"><?php echo $college_grand_total_male ; ?> </td>
                          <td class="bordering2"><?php echo  $college_grand_total_female; ?> </td>
                          <td class="bordering2"><?php echo $college_total; ?> </td>
                          
                            <td class="bordering2"> <?php 
                            if ($college_total>0) {
                                     echo number_format(
                            ($college_grand_total_male+
                        $college_grand_total_female)/$college_total, 3, '.', ','); 
                            } else {
                               echo "0";
                            }
                        
                        ?> </td>  
                                 
                            
                       
                            
                            <?php 
                            
                        } 
                         
                      ?>
                      </tr>
                    <?php 
              } 
            ?>
		    
  
    <?php 
    
    }
    ?>
</table>
  <?php 
  } 
 }
 ?>
 
<p class="fs16">
        <strong > Label </strong> <br/>
        <strong> M : </strong>  Male Dismissed <br/>
        <strong> F : </strong>  Female Dismissed <br/>
        <strong> Total : </strong> Total Registred <br/>
        <strong> Rate : </strong> Rate Dismissed <br/>
        <strong> - : </strong> No registration for that year <br/>
       
</p>


 <?php 
}
?>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
