<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php 
echo $this->Form->create('Readmission');

if ($role_id != ROLE_STUDENT ) {
?>
<p class="smallheading">View Readmission Applications.</p>
	<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
			<td style="width:12%">Academic Year:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
			
		</tr>
		<tr>
			 <?php 
		        if (!empty($departments)) {
		        ?>
		         <td style="width:12%">Department:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.department_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$departments)); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.program_id', array('class' => 'fs14',  'style' => 'width:125px', 'label' => false,'options'=>$programs)); ?></td>    
		        <?php 
		        
		        } else if (!empty($colleges)) {
		        ?>
		        <td style="width:12%">College:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.college_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$colleges)); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.program_id', array('class' => 'fs14',  'style' => 'width:125px', 'label' => false,'options'=>$programs)); ?></td>
		        <?php 
		        }
		    ?>
			
			
		</tr>
			<tr>
			<td style="width:12%">Program Type:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.program_type_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$programTypes)); ?></td>
			<td style="width:8%">&nbsp;</td>
			<td style="width:25%">&nbsp;</td>
			
		</tr>
		<?php
		    if ($role_id == ROLE_REGISTRAR) {
		 ?>
		<tr>
		  	<td> Type:</td>
			<td><?php 
			echo $this->Form->input('Search.rejected', array('type' => 'checkbox', 'label' => 'Rejected', 'div' => false, 'checked' => (!isset($this->request->data) || $this->request->data['Search']['rejected'] == 1 ? 'checked' : false))).'<br/>';
			echo $this->Form->input('Search.accepted', array('type' => 'checkbox', 'label' => 'Accepted', 'div' => false, 'checked' => (!isset($this->request->data) || $this->request->data['Search']['accepted'] == 1 ? 'checked' : false))).'<br/>';
		    
		    echo $this->Form->input('Search.notprocessed', array('type' => 'checkbox', 'label' => 'Not Processed', 'div' => false, 'checked' => (!isset($this->request->data) || $this->request->data['Search']['notprocessed'] == 1 ? 'checked' : false)));
			
			?></td>		
		</tr>
		<?php 
		    }
		?>
		<tr>
		<td colspan='4'><?php echo $this->Form->submit(__('View Readmission Application'), array('name' => 'viewReadmission', 'div' => false)); ?></td>
		</tr>
</table>
<?php 
}
?>
<div class="readmissions index">
<?php if (!empty($readmissions)) { 
?>
	<div class="smallheading"><?php echo __('Readmission Application ');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('Full Name','student_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('minute_number');?></th>
			<th><?php echo $this->Paginator->sort('registrar_approval');?></th>
			<th><?php echo $this->Paginator->sort('registrar_approval_date');?></th>
			<th><?php echo $this->Paginator->sort('academic_commision_approval');?></th>
			<th><?php echo $this->Paginator->sort('academic_commision_approval_date');?></th>
			<th><?php echo $this->Paginator->sort('remark');?></th>
			
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($readmissions as $readmission):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td>
			
			<?php 
			

	echo $this->Html->link(
    $readmission['Student']['full_name'],
    '#',
   array('class'=>'jsview','data-animation'=>"fade",
'data-reveal-id'=>'myModal','data-reveal-ajax'=>"/students/get_modal_box/".$readmission['Student']['id'])
);

			?>
		</td>
		<td><?php echo $readmission['Readmission']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $readmission['Readmission']['semester']; ?>&nbsp;</td>
		<td><?php echo $readmission['Readmission']['minute_number']; ?>&nbsp;</td>
		<td><?php 
		
		      if ($readmission['Readmission']['registrar_approval']==1) {
		        
		         echo 'Registrar Accepted Application and Dispatched To AC ';
		        
		      } else if ($readmission['Readmission']['registrar_approval']==-1) {
		         
		           echo 'Registrar Rejected Application';
		      } else if ($readmission['Readmission']['registrar_approval']=="") {
		            //echo 'Rejected Application';
		            echo 'Waiting Decision';
		      }
		    ?>
		    &nbsp;</td>
		<td>
		    <?php 
		      if (!empty($readmission['Readmission']['registrar_approval_date'])) {    
		        echo $this->Format->humanize_date($readmission['Readmission']['registrar_approval_date']); 
		       }
		    ?>&nbsp;
		    
		    </td>
		<td><?php 
		   
		      
		       if ($readmission['Readmission']['academic_commision_approval']==1) {
		        
		         echo 'Approved By AC';
		        
		      } else if ($readmission['Readmission']['academic_commision_approval']==-1) {
		         
		           echo 'Rejected By AC';
		      } else  {
		              if ($readmission['Readmission']['registrar_approval']==1) {
		                    echo 'Waiting AC Decision';
		              } else {
		                    echo '---';
		              }
		      }
		      
		    ?>
		    &nbsp;</td>
		<td>
		    <?php 
		         if (!empty($readmission['Readmission']['academic_commission_approval_date'])) {    
		        echo $this->Format->humanize_date($readmission['Readmission']['academic_commission_approval_date']); 
		       }
		    
		    ?>
		    &nbsp;
		</td>
		<td><?php echo $readmission['Readmission']['remark']; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
<?php } ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
