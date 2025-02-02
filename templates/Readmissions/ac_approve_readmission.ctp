<?php 
echo $this->Form->create('Readmission',array('novalidate' => true));
?>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<p class="smallheading">Please select academic year, department, program and semester for which you want to approve readmission application.</p>
	<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
			<td style="width:12%">Academic Year:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
			
		</tr>
		<tr>
			<td style="width:12%">Department:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.department_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$departments)); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.program_id', array('class' => 'fs14',  'style' => 'width:125px', 'label' => false,'options'=>$programs)); ?></td>
			
		</tr>
			<tr>
			<td style="width:12%">Program Type:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.program_type_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$programTypes)); ?></td>
			<td style="width:8%">&nbsp;</td>
			<td style="width:25%">&nbsp;</td>
			
		</tr>
		<tr>
		<td colspan='4'><?php echo $this->Form->submit(__('Filter Readmission Application'), array('name' => 'filterReadmission', 'div' => false,'class'=>'tiny radius button bg-blue')); ?></td>
		</tr>
	</table>

  <div class="readmissions index">
<?php 
    if (!empty($readmissions)) {
    $options=array('1'=>'Accept','-1'=>'Reject');
   $attributes=array('legend'=>false,'label'=>false,'separator'=>"<br/>");
   
?>
	<div class="smallheading"><?php echo __('List of readmission applicant accepted by registrar and waiting your decision.');?></div>
	<?php
	 foreach ($readmissions as $deptname=>$program) {          // department 
	        echo '<div class="fs16">Department: '.$deptname.'</div>';
	        foreach ($program as $progr_name=>$programType) {        // program
	            echo '<div class="fs16">Progam: '.$progr_name.'</div>'; 
	            foreach ($programType as $progr_type_name=>$readmissionsss) {    // program type 
	                echo '<div class="fs16">ProgramType: '.$progr_type_name.'</div>';
	                
	                ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo 'Full Name' ?></th>
			<th><?php echo 'Acadamic Year';?></th>
			<th><?php echo 'Semester';?></th>
			
			<th><?php echo 'GPA';?></th>
			<th><?php echo 'Clearance/Withdrawal';?></th>
			<th><?php echo 'Student Copy';?></th>
			
			<th><?php echo 'Academic Commision Approval';?></th>
			<th><?php echo 'Minute Number';?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($readmissionsss as $readmission):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start; ?>&nbsp;</td>
		<td class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?php echo$readmission['Student']['id'];?>">
			<?php //echo $this->Html->link($readmission['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $readmission['Student']['id'])); 
			echo $readmission['Student']['full_name']; 
			
			 echo $this->Form->hidden('Readmission.'.$start.'.id',array('label'=>false,'div'=>false,'value'=>$readmission['Readmission']['id'])); 
		     echo $this->Form->hidden('Readmission.'.$start.'.student_id',array('label'=>false,'div'=>false,'value'=>$readmission['Readmission']['student_id'])); 
			
			?>
			
		</td>
		<td><?php echo $readmission['Readmission']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $readmission['Readmission']['semester']; ?>&nbsp;</td>
		
		<td><?php 
		   
		    
		    if (isset($readmission['Student']['StudentExamStatus']) && empty($readmission['Student']['StudentExamStatus'])) {
		        echo 'SGPA:-- <br/>';
		        echo 'CGPA:--';
		    } else {
		         echo 'SGPA:'.$readmission['Student']['StudentExamStatus'][0]['sgpa'].'<br/>';
		         echo 'CGPA:'.$readmission['Student']['StudentExamStatus'][0]['cgpa']; 
		    }
		    
		    ?> &nbsp;</td>
		    
			<td>
		    <?php 
		        
		          if (!empty($readmission['Student']['Clearance'])) {
		                echo 'Reason:<strong>'.$readmission['Student']['Clearance'][0]['reason'].'
		                </strong><br/>';
		                echo 'Clearance Date: <strong>'.$readmission['Student']['Clearance'][0]['request_date'].'</strong><br/>';
		                echo 'Cleared: <strong>Yes</strong> <br/>';
		                if ($readmission['Student']['Clearance'][0]['forced_withdrawal'] != "" ) {
		                        if ($readmission['Student']['Clearance'][0]['forced_withdrawal']==1) {
		                            echo 'Withdrawal Reason: Accepted.</br>';
		                            echo "Minute Number: ".
		                            $readmission['Student']['Clearance'][0]['minute_number'].".";
		                        } else if ($readmission['Student']['Clearance'][0]['forced_withdrawal']==-1) {
		                            echo 'Withdrawal Reason: <strong>Rejected.</strong>';
		                            echo "Minute Number: <strong>".
		                            $readmission['Student']['Clearance'][0]['minute_number'].
		                            " </strong>.";
		                        
		                        }
		                }
		                
		                if (isset($readmission['Student']['Clearance'][0]['Attachment']) && 
		                !empty($readmission['Student']['Clearance'][0]['Attachment'])) { 
			             
			              echo " <a href=".$this->Media->url($readmission['Student']['Clearance'][0]['Attachment'][0]['dirname'].DS.$readmission['Student']['Clearance'][0]['Attachment'][0]['basename'],true)." target=_blank'>View Attachment</a>";
		                 
		                }
		    
		          }
		          
		    ?>
		</td>
		<td>
		    <?php 
		    //exam_grades/
		     // echo $this->Html->link('View', array('controller' => 'exam_grades', 'action' => 'student_copy', $readmission['Student']['id'])); 
		     
		    //exam_grades/
		     if (isset($readmission['Student']['StudentExamStatus']) && empty($readmission['Student']['StudentExamStatus'])) {
		        echo '---';
		     } else {
		        echo $this->Html->link('View', array('controller' => 'exam_grades', 'action' => 'student_copy', $readmission['Student']['id'])); 
		    
		     }
		    
		    
		    
		    ?>
		</td>
		
		<td><?php 
		      echo $this->Form->radio('Readmission.'.$start.'.academic_commision_approval',$options,$attributes)
		     
		    ?>
		    &nbsp;</td>
		    <td><?php 
		echo $this->Form->input('Readmission.'.$start.'.minute_number',array('label'=>false,'div'=>false)); 
		
		$start++;
		?>
		
		&nbsp;</td>
		
	
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	
	     echo $this->Form->submit('Accept/Reject Readmission',array('name'=>'saveIt','class'=>'tiny radius button bg-blue','div'=>'false')); 
	
	?>	</p>

<?php 
    
            }
        }
      }
    }
 ?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
