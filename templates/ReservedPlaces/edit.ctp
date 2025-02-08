<?php echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php echo $this->Html->script('jquery-department_placement');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            <table cellpadding=0 cellspacing=0>
<tbody>
<tr>    
        <td width="50%">
                <table>
	                <tr>
	                
	                <?php echo $this->Form->create('ReservedPlace');?>
	               
	                <td class="headerfont"><?php echo __('Edit Reserved Place'); ?></td></tr>
	                <tr>
	                
	                
	        <?php
	                if(isset($college_name)){
	                    echo '<tr><td class="font">'.$college_name.'</td></tr>';
	                }
	                
	                if(isset($departmentname)){
	                  
	                    echo '<tr><td class="font">'.$departmentname.'</td></tr>';
	                }
	               
	                echo '<tr><td>'.$this->Form->input('id').'</td></tr>';
	                echo '<tr><td>'.$this->Form->input('placements_results_criteria_id').'</td></tr>';
	                echo '<tr><td>'.$this->Form->hidden('participating_department_id').'</td></tr>';
	                echo '<tr><td>'.$this->Form->hidden('college_id').'</td></tr>';
	                echo '<tr><td>'.$this->Form->input('number').'</td></tr>';
	                echo '<tr><td>'.$this->Form->input('description').'</td></tr>';
	                echo  '<tr><td>'.$this->Form->input('academicyear',array('readonly'=>'readonly')).'</td></tr>';
	        ?>
	                
	                </tr>
	                <tr><td colspan=2>
	                <?php echo $this->Form->end(__('Submit'));?></td></tr>
	                </table>
	     </td>
	       <td width="50%">
          <table>
            <tbody>
            <?php 
            foreach($othersreservedquota as $k=>$v) {
                echo '<tr><td class="font">'.
                $v['ReservedPlace']['department_name'].' has '.
               $v['ReservedPlace']['number'].' reserved place for '.$v['PlacementsResultsCriteria']['name'].' category </td></tr>';
                
            } 
            
            ?>
           
            </tbody>
          </table>
        </td>
</tr>

</tbody>
</table>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
