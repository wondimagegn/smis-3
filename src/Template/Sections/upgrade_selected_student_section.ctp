<?php 
echo $this->Form->create('Section',
array('controller'=>'sections','action'=>'section_move_update',
"method"=>"POST"));
?>
<div class="row">
<div class="large-12 columns">
	
  <div class="row">
		<div class="large-6 columns">
			 <?php 
debug($student_detail);
debug($nextSection);
	echo $this->Form->input('Selected_section_id',array('label'=>false,'id'=>'Selected_section_id','type'=>'select',
        'options'=>$sections,'empty'=>"--Select Section--"));

	echo $this->Form->hidden('previous_section_id', 
array('value'=>$previous_section_id));


?>
		</div>
		<div class="large-6 columns">
			 		 

<?php 
echo $this->Form->Submit(__('Move'),array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'attach'));
?>
		</div>
	
</div>

<table>
   <tr><th colspan=5 class="smallheading"><?php echo  __(''.$previousSectionName['Section']['name'].'-'.$previousSectionName['YearLevel']['name'].'('.$previousSectionName['Program']['name'].'-'.$previousSectionName['ProgramType']['name'].'-'.$previousSectionName['Department']['name'].')');?></th></tr>
	<tr>
	        
            <th><?php echo ('No.'); ?> </th>
            
		    <th><?php echo ('Student Number');?></th>
            <th><?php echo ('Name');?></th>
			<th><?php echo ('Sex');?></th>	
	</tr>
	
	  <tr>         
		<td>1</td>
       
       <?php 

echo $this->Form->hidden('Section.1.selected_id',
array('value'=>1)); 

echo $this->Form->hidden('Section.1.student_id',array('value'=>$students['Student']['id'])); ?>
        <td><?php echo $students['Student']['studentnumber']; ?>&nbsp;</td>
        <td><?php echo $students['Student']['full_name']; ?>&nbsp;</td>
		<td><?php echo $students['Student']['gender']; ?>
&nbsp;</td>
       </tr>
         
	<?php 
    ?>

</table>

</div>
</div>

<?php echo $this->Form->end();?>
<a class="close-reveal-modal">&#215;</a>

