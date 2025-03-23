<script>
function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Field');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Adjust Fields');
		}
	$('#'+id).toggle("slow");
}
</script>
<?php  
$this->request->data['Display']=$this->Session->read('display_field_student');

echo $this->Form->Create('AlumniMember');
?>
<div class="box">
     <div class="box-body">
       <div class="row">
   <div class="large-12 columns">
	 <div onclick="toggleViewFullId('ListAlumniMember')">
			 <?php 
			if (!empty($alumniMembers)) {
				echo $this->Html->image('plus2.gif', array('id' => 'ListAlumniMemberImg')); 
				?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListAlumniMemberTxt">Adjust Fields</span><?php
				}
			else {
				echo $this->Html->image('minus2.gif', array('id' => 'ListAlumniMemberImg')); 
				?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListAlumniMemberTxt">Hide Fields</span><?php
			}
		?>
	   </div>
	</div>
   <div class="large-12 columns">
    	<div class="row">
			<div class="large-4 columns" >
				<?php   
				/*  
					echo $this->Form->input('Search.gradution',
					array('style'=>'width:100px',
					'type'=>'text',
					'label'=>'Gradution Year'));
				*/
				?>
				<?php 
					echo $this->Form->input('Search.gradution');
					?>
			</div>
			<div class="large-4 columns" >
					<?php 
					echo $this->Form->input('Search.college');
					?>
			</div>
			<div class="large-4 columns" >
					<?php 
					echo $this->Form->input('Search.department',array('empty'=>' '));
					?>
			</div>
	    </div>
	   <div class="row">
			<div class="large-3 columns" >
					<?php 
					echo $this->Form->input('Search.name',array('label'=>'Name'));
					?>
			</div>
			
			<div class="large-3 columns" >
			 <?php 
					echo $this->Form->input('Search.program');
				   
				?>
			</div>
			<div class="large-3 columns" >
			 <?php 
				   echo $this->Form->input('Search.gender',array('label'=>'Gender','type'=>'select','empty'=>'All',
							'options' => array('female' => 'Female', 'male' => 'Male')));
						
						?>
			</div>
			<div class="large-3 columns" >
			 <?php 
			echo $this->Form->input('Search.limit',array('label'=>'Limit'));
			?>
			</div>
			
		 </div>
		
	   </div>
       
       <div class="row">
       <div class="large-12 columns">
	 <div onclick="toggleViewFullId('ListStudents')">
			 <?php 
			if (!empty($alumniMembers)) {
				echo $this->Html->image('plus2.gif', array('id' => 'ListStudentsImg')); 
				?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt">Adjust Fields</span><?php
				}
			else {
				echo $this->Html->image('minus2.gif', array('id' => 'ListStudentsImg')); 
				?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt">Hide Fields</span><?php
				}
		?>
	   </div>
</div>
<div class="large-12 columns" id="ListStudents" style="display:<?php echo (!empty($alumniMembers) ? 'none' : 'display'); ?>">
	<div class="row">
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.full_name',array('label'=>'Fullname','type'=>'checkbox',
		  		'checked'=>true));?>
		  </div>
		 
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.gender',array('label'=>'Gender','type'=>'checkbox'));?>
		  </div>
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.email',array('label'=>'Email','type'=>'checkbox'));?>
		  </div>
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.phone',array('label'=>'Phone','type'=>'checkbox'));?>
		  </div>
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.current_position',array('label'=>'Position','type'=>'checkbox'));?>
		  </div>
		  
		  
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.gradution',array('label'=>'Gradution Year','type'=>'checkbox'));?>
		  </div>
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.program
',array('label'=>'Study Level','type'=>'checkbox'));?>
		  </div>
		  <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.city
',array('label'=>'City','type'=>'checkbox'));?>
		  	</div>
		  	<div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.country
',array('label'=>'Country','type'=>'checkbox'));?>
		  	</div>
		  	 <div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.college',array('label'=>'College','type'=>'checkbox'
		  		));?>
		  	</div>
		  	<div class="large-2 columns">
		  		<?php echo $this->Form->input('Display.department',array('label'=>'Department','type'=>'checkbox'));?>
		  	</div>	
	</div>
</div>
<div class="large-12 columns">
<?php 
 echo $this->Form->Submit('Search',array('class'=>'tiny radius button bg-blue','div'=>false));
?>
</div>
<div class="large-12 columns">
  <div class="alumniMembers index">
	<h2><?php echo __('Alumni Members'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	
	<tr>
	<?php 
	echo '<th>S.N<u>o</u></th>';
	if(isset($this->request->data['Display']) && !empty($this->request->data['Display']) && $this->Session->read('display_field_student')){
		
		foreach ($this->request->data['Display'] as $dk => $dv) {
			if($dv==1){
				 echo '<th>'.$this->Paginator->sort($dk).'</th>';
			}
		  
		}
	} else {
	?>
		<th><?php echo $this->Paginator->sort('full_name'); ?></th>
			
			<th><?php echo $this->Paginator->sort('email'); ?></th>
			<th><?php echo $this->Paginator->sort('gender'); ?></th>
			
			<th><?php echo $this->Paginator->sort('institute_college'); ?></th>
			<th><?php echo $this->Paginator->sort('department'); ?></th>
			<th><?php echo $this->Paginator->sort('program'); ?></th>
		       
<?php } ?>
	<th class="actions"><?php __('Actions');?></th>
	
</tr>
	
	</thead>
	<tbody>
	<?php 
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	
	foreach ($alumniMembers as $alumniMember):
	
	$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	 ?>
	<tr <?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<?php 
		if(isset($this->request->data['Display']) && !empty($this->request->data['Display']) && !empty($this->Session->read('display_field_student'))){
		foreach ($this->request->data['Display'] as $dk => $dv) {
			if($dv==1){
				

			if($dk=='full_name'){
				 echo '<td>'.$alumniMember['AlumniMember']['full_name'].'</td>';
			} else if($dk=='gradution'){
			 echo '<td>'.$alumniMember['AlumniMember']['gradution'].'</td>';
			}  else if($dk=='gender'){
               echo '<td>'.$alumniMember['AlumniMember']['gender'].'</td>';
			} else if($dk=='program'){
				 echo '<td>'.$alumniMember['AlumniMember']['program'].'</td>';
			} else if($dk=='college'){
				 echo '<td>'.$alumniMember['AlumniMember']['institute_college'].'</td>';
			} else if($dk=='department') {
				 echo '<td>'.$alumniMember['AlumniMember']['department'].'</td>';
			} else if($dk=='city'){
				 echo '<td>'.$alumniMember['AlumniMember']['city'].'</td>';
			} else if($dk=='country'){
				 echo '<td>'.$alumniMember['AlumniMember']['country'].'</td>';
			} else {
				 echo '<td>'.$alumniMember['AlumniMember'][$dk].'</td>';
			}
           
			}
		  
		}
			
		} else {
		?>
		
		<td><?php echo h($alumniMember['AlumniMember']['full_name']);?></td>
			
			<td><?php echo h($alumniMember['AlumniMember']['email']); ?></td>
			<td><?php  echo h($alumniMember['AlumniMember']['gender']);  ?></td>
			
			<td><?php 
			echo h($alumniMember['AlumniMember']['institute_college']); 
			 ?></td>
			<td><?php echo h($alumniMember['AlumniMember']['department']);  ?></td>
			<td><?php  echo h($alumniMember['AlumniMember']['program']); ?></td>
			
		<?php }?>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $alumniMember['AlumniMember']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $alumniMember['AlumniMember']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $alumniMember['AlumniMember']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $alumniMember['AlumniMember']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
			</div>
			</div>
		</div>
	</div>
   </div>
</div>
<?php 
echo $this->Form->end();
?>
