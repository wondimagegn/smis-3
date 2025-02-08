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
<div class="smallheading"><?php echo __('Enrollement Stats View'); ?></div>

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
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('semester', array('id' => 'Semester',
		 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 
		 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
	</tr>
	
	<tr  class="AttrationRateView" style="display:<?php echo $display; ?>">
						<td style="width:15%">Department:</td>
		                <td style="width:30%"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 
		                'class' => 'fs13', 'label' => false, 'type' => 'select', 
		                'style'=>'width:200px',
		                'options' => $departments, 
		                'default' => $default_department_id)); ?></td>
		                
						<td style="width:15%">Type:</td>
						<td style="width:40%"><?php 
						
						echo $this->Form->input('Type.registered', array('type' => 'checkbox', 
						'label' => ' Registered', 'div' => false, 'checked' => (!isset($this->request->data) || 
						$this->request->data['Type']['registered'] == 1 ? 'checked' : false)));
						echo '<br/>';
						
						echo $this->Form->input('Type.dismissed', array('type' => 'checkbox', 
						'label' => ' Dismissed', 'div' => false, 
						'checked' => (!isset($this->request->data) || $this->request->data['Type']['dismissed'] == 1 ? 
						'checked' : false)));
						
						echo '<br/>';
						
						echo $this->Form->input('Type.dropout', array('type' => 'checkbox', 
						'label' => ' Dropout', 'div' => false, 
						'checked' => (!isset($this->request->data) || $this->request->data['Type']['dropout'] == 1 ? 
						'checked' : false)));
						
						echo '<br/>';
						
						echo $this->Form->input('Type.withdrawal', array('type' => 'checkbox', 
						'label' => ' Withdrawal', 'div' => false, 
						'checked' => (!isset($this->request->data) || $this->request->data['Type']['withdrawal'] == 1 ? 
						'checked' : false)));
						
						echo '<br/>';
						
						echo $this->Form->input('Type.transferred', array('type' => 'checkbox', 
						'label' => ' Transferred', 'div' => false, 
						'checked' => (!isset($this->request->data) || $this->request->data['Type']['transferred'] == 1 ? 
						'checked' : false)));
						
						
						?>
						</td>
						
						
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
       Students enrollment statistics of <?php echo $this->request->data['Report']['acadamic_year']; ?> AY, Semester
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
		<th rowspan="2" colspan="2" class="bordering2"  style="vertical-align:bottom; width:8%">Department Name</th>
		<?php 
		$percent = 10;
		$last_percent = false;
		$total_percent = (count($yearLevel)*10) + (count($yearLevel)*10) + 86;
		if($total_percent > 100) {
			
		}
		else if($total_percent < 100) {
			$last_percent = 100 - $total_percent;
		}
		
		?>
		
		<?php foreach ($yearLevel as $k=>$value) { ?>
		
		<th colspan="3"  class="bordering2" style="text-align:center; width:<?php echo $percent; ?>%" 
		class="bordering2"><?php echo $value;?></th>
	   <?php } ?>
	   
	   	<th colspan="3" class="bordering2" style="text-align:center; width:15%" class="bordering2">Grand Total</th>	
    </tr>
    <tr>
       
		
			<?php foreach ($yearLevel as $k=>$value) { ?>
		
		            <th style="width:5%" class="bordering2">M</th>
		            <th style="width:5%" class="bordering2">F</th>
		            <th style="width:5%" class="bordering2">Total</th>
		
	       <?php } ?>
		
		<th style="width:5%" class="bordering2">M</th>
		<th style="width:5%" class="bordering2">F</th>
		<th style="width:5%" class="bordering2">Dept. Total</th>
	
		
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
                     $count=0;
                     $prevDept="";
                     $prevCollege="";
                    foreach ($stat as $department=>$type) {
                           $typeCount=count($type);
                           foreach($type as $typeName=> $yearLevel) {
                             ++$count;
                            
                            
                            ?>
                             <tr>
                               <td class="bordering2">  <?php echo $count; ?> </td>
                               <td class="bordering2" >  
                               
                               <?php 
                                    if (empty($prevCollege)) {
                                       $prevCollege=$college;
                                       echo $college;
                                    } else if ($prevCollege==$college) {
                                         echo ""; 
                                    }
                                   
                               
                               ?> 
                               
                               </td>
                               <td class="bordering2"> 
                               
                                <?php // echo $department; ?> 
                                
                                 
                               <?php 
                                    if (empty($prevDept)) {
                                       $prevDept=$department;
                                       echo $department;
                                    } else if ($prevDept==$department) {
                                         echo ""; 
                                    }
                                   
                               
                               ?> 
                               
                                 </td>
                               <td class="bordering2">  <?php echo $typeName; ?>  </td>
                            
                            <?php 
                              $grand_total_female=0;
                              $grand_total_male=0;
                              $dept_total=0;
                          
                                foreach ($yearLevel as $year=>$statValues) {
                                   
                                   if (isset($yearLevel[$year])) {
                                   ?>
                                     <td class="bordering2"> <?php 
                                           echo $yearLevel[$year]['male_total'];
                                           
                                        ?> 
                                     </td>
                                     
                                    <td class="bordering2"> 
                                     <?php 
                                     echo $yearLevel[$year]['female_total'];
                                     ?> 
                                   </td>
                                   <td class="bordering2"> 
                                    <?php 
                         
                                    echo $yearLevel[$year]['total'];?> 
                         
                                  </td>
                         
                                   <?php 
                                   
                                       $grand_total_female+=$yearLevel[$year]['female_total'];
                      $grand_total_male +=$yearLevel[$year]['male_total'];
                      $dept_total +=$yearLevel[$year]['total']; 
                                   
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
                        
                      
                                   <?php 
                                   
                                     $grand_total_female+=0;
                                      $grand_total_male +=0;
                                      $dept_total +=0; 
                                   
                                   }
                                }
                               ?>
                               
                                <td class="bordering2"> 
                         
                         <?php echo $grand_total_male; ?> </td>
                         <td class="bordering2">
                         
                          <?php echo $grand_total_female; ?> </td>
                       
                         <td class="bordering2"> 
                         
                         <?php echo $dept_total; ?> </td>
                       
                            </tr>   
                               <?php 
                           }  
                    } 
                    ?>
                     
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
        <strong> M : </strong>  Male  <br/>
        <strong> F : </strong>  Female <br/>
        <strong> Total : </strong> Total <br/>       
</p>


 <?php 
}
?>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
