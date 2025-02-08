<div class="students form" style="align:center">
<?php echo $this->Form->create('Student',array('enctype' => 'multipart/
form-data'
));?>
	<fieldset>
		<legend><?php echo __('Edit Student'); ?></legend>
		<div id="basic_fields" style="display:block">	
		 <?php
            echo $this->element('user_tab_menu',
                array('current_tab' => 'basic_fields'));
            
            echo "<div class=\"AddTab\">\n";
                echo '<table cellspacing="0" cellpading="0"><tbody>';
                echo "<tr><td><table><tbody>";
                 echo '<tr><td colspan=2><hr/></td></tr>';
                 echo '<tr><td colspan=2><strong>Demographic Information</strong></td></tr>';
                  echo '<tr><td colspan=2><hr/></td></tr>';
		        
	            // save name in the basic information
	            echo $this->Form->hidden('id');
		        echo '<tr><td>'.$this->Form->input('firstname',array('label'=>'First name')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('middlename',array('label'=>'Middle name')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('lastname',array('label'=>'Last name')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('estimated_grad_date').'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('gender',array('label'=>'Gender')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('ethnicity',array('label'=>'Ethnicity')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('lanaguage',array('label'=>'Primary Lanaguage')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('Address.0.email',array('label'=>'Email')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('Address.0.phone',array('label'=>'Phone')).'</td></tr>';
		      
		        echo '<tr><td>'.$this->Form->input('birthdate',array('label'=>'Birth date')).'</td></tr>';
		       //echo '<tr><td>'.$this->Form->input('file',array('label'=>'Upload profile picture','type'=>'file')).'</td></tr>';
		      
		        echo "</tbody></table></td>";
		      
		          // save account information in the user table
		        echo '<td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Profile Picture</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
               // debug($this->request->data);
		        $model=$this->Form->model();
		        foreach($this->request->data['Attachment'] as $key=>$value){
		               
		                echo $this->Form->hidden('Attachment'.$key.'id');
		        }
		    
	   
	        //echo $this->Html->image($this->Media->url($student['Attachment'][0]['basename'],true), array('alt' => 'CakePHP'));
	  // echo $this->Media->embed($this->Media->file('s'.DS.$student['Attachment'][0]['dirname'].DS.$student['Attachment'][0]['basename']));

		        //echo $this->Form->hidden('Attachment.0.id');
		        //echo '<tr><div class="profile-picture"><td align="center">'.$this->Media->embed($this->Media->file('s'.DS.$this->request->data['Attachment']['dirname'].DS.$this->request->data['Attachment']['basename'])).'</td></div></tr>';
		        echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media','label' =>'Profile Picture')).'</td></tr>';
		        //echo "<tr><td>".$this->Form->input('Attachment',array('type'=>'file'))."</td></tr>";
		        echo '</tbody></table></td></tr>';
		        echo '<tr><td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        
		        echo '<tr><td colspan=2><strong>School Information</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td>'.$this->Form->input('dormitory_id').'</td></tr>';
		       
		        echo "<tr><td>". $this->Form->input('college_id')."</td></tr>";
		        echo '<tr><td>'.$this->Form->input('department_id').'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('grade_level',array('label'=>'Grade Level')).'</td></tr>';
		       
		        echo '</tbody></table></td>';
		        echo '<td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Access Information</strong></td></tr>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo $this->Form->hidden('User.id');
		        //echo $this->Form->hidden('User.role_id');
		        echo '<tr><td>Username  :   '.$this->request->data['User']['username'].'</td></tr>';
		         echo '<tr><td>Password :   ****************</td></tr>';
		       
		        echo "</tbody></table></td>";
		        echo '</tr>';
		        echo '</tbody></table>';
		    echo "</div>"; // end add tab div
		    
	    ?>
	   </div> <!-- end basic info block --->
	<div id="add_address" style="display:none">
	<?php		
        echo $this->element('user_tab_menu',
                array('current_tab' => 'add_address'));

        echo "<div class=\"AddTab\">\n";
              echo $this->Form->hidden('Address.0.id');
              echo $this->Form->input('Address.0.city');
              echo $this->Form->input('Address.0.zone/subcity');
              echo $this->Form->input('Address.0.woreda');
              echo $this->Form->input('Address.0.kebele');
              echo $this->Form->input('Address.0.house_number');
              echo $this->Form->input('Address.0.pobox',array('label'=>'P.O.Box'));
		echo '</div>'; // End add tab div
		?>
	 </div> <!-- End address block -->
	</fieldset>
<?php echo $this->Form->end(__('Save'));?>
</div>
