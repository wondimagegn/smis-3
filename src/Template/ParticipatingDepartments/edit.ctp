<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="participatingDepartments form">
<?php echo $this->Form->create('ParticipatingDepartment');?>
	<table><tbody>
	<tr><td class='headerfont'>
		<?php echo __('Edit Participating Department'); ?>
		</td>
    </tr>
    
	<?php
		echo $this->Form->input('id');
		echo '<tr><td>'.$this->Form->input('college_id').'</td></tr>'; 
		echo '<tr><td>'.$this->Form->input('department_id').'<td></tr>';
		
		
		if(!empty($this->request->data['ParticipatingDepartment']['other_college_department'])){
		    echo '<tr><td>'.$this->Form->input('other_college_department',
		    array('disabled'=>'true')).'</td></tr>';
		    echo $this->Form->hidden('other_college_department',array('value'=>
		    $this->request->data['ParticipatingDepartment']['other_college_department']));
		    echo '<tr><td>'.$this->Form->input('number').'</td></tr>';
		}
		echo '<tr><td>'.$this->Form->input('academic_year',array('readonly'=>'readonly')).'</td></tr>';
	
		echo '<tr><td>'.$this->Form->input('number').'<td></tr>';
	    //echo '<tr><td>'.$this->Form->input('female').'<td></tr>';
	    //echo '<tr><td>'.$this->Form->input('regions').'<td></tr>';
	    //echo '<tr><td>'.$this->Form->input('disability').'<td></tr>';
	?>
<?php echo '<tr><td>'.$this->Form->end(__('Submit')).'</tr></td>';?>
    </tbody>
    </table>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
