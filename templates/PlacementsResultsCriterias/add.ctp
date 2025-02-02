<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="placementsResultsCriterias form">
<?php 
echo $this->Form->create('PlacementsResultsCriteria');
?>
<?php 
if (isset($check_auto_placement_already_run_not_allow_adding_or_edit) && 
$check_auto_placement_already_run_not_allow_adding_or_edit>0) {
    echo '<div class="info-box info-message"><span></span>Result category for '.$selected.' academic year for student auto placement to department for '.$college_name.'. You have already run the auto placement, you can not add or delete result category.</div>';

} else {
  echo '<h3> Result Category for '.$college_name.' Student Auto Placement to Department </h3>';
}
?>


<?php if(!isset($prepartory_academic_year)) { ?>
	
	<?php 
	   echo '<table><tbody>';
	   echo '<tr><td>';  
		echo $this->Form->input('admissionyear',array('id'=>'admissionyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected)?$selected:''));
		echo '</td></tr>';
		 echo '<tr><td>';  
		echo $this->Form->input('prepartory_result',array('checked'=>'checked'));
		echo '</td></tr>';
		 echo '<tr><td>';    
		    echo $this->Form->Submit('Continue',array('div'=>false,
 'name'=>'prepandacademicyear','class'=>'tiny radius button bg-blue'));
		 echo '</td></tr>';
		 echo '</tbody></table>';
	?>
	
	<?php
	} else {
	 ?>
	 <table>
	<tbody>
	
	 <tr><td> 
	 <table><tbody>
	 <?php 
		 echo '<tr><td>';  
		echo $this->Form->hidden('admissionyear',array('value'=>$selected_academicyear));
		echo '</td></tr>';
		 echo '<tr><td>';  
		echo '<div style="font-weight:bold">Academic Year: '.$selected_academicyear.'</div>';
		echo '</td></tr>';
		 echo '<tr><td>';  
		echo $this->Form->hidden('prepartory_result',array('value'=>$result_type));
		echo '</td></tr>';
		echo '<tr><td style="font-weight:bold">Result Type: ';  
		if($result_type){
		    echo 'Preparatory';
		} else {
		    echo 'Freshman';
		}
		
		echo '</td></tr>';
		
		 if (isset($check_auto_placement_already_run_not_allow_adding_or_edit) && 
$check_auto_placement_already_run_not_allow_adding_or_edit>0) {
		
		 
		 } else {
		    echo '<tr><td>';    
		echo $this->Form->input('name');
		echo '</td></tr>';
        
		 echo '<tr><td>';  
		echo $this->Form->hidden('college_id', array('value'=>$college_id));
	    echo '</td></tr>';
	     echo '<tr><td>'; 
		echo $this->Form->input('result_from',array('id'=>'result_from'));
		 echo '</td></tr>';
		 
		 
		 
		 echo $this->Js->get("#result_from")->event('change', 
                $this->Js->request(array('controller'=>'acceptedStudents','action' => 'count_result'), array( 
                'update' => '#result_from_count', 'async' => true, 'dataExpression' => true, 'method' => 'post', 
                 'data' => $this->Js->serializeForm(array('isForm' => false, 'inline' => true)) 
                ))); 
		
		   echo '<tr><td>'; 
		   echo $this->Form->input('result_to',array('id'=>'result_to'));
		  
		   echo $this->Js->get("#result_to")->event('change', 
                $this->Js->request(array('controller'=>'acceptedStudents','action' => 'count_result'), array( 
                'update' => '#result_from_count', 'async' => true, 'dataExpression' => true, 'method' => 'post', 
                 'data' => $this->Js->serializeForm(array('isForm' => false, 'inline' => true)) 
                ))); 
           echo "<tr><td id='result_from_count' style='border: 1px solid green;'></td></tr>";
		   echo '<tr><td>';    
		   echo $this->Form->Submit('Submit',array('div'=>false,
 'name'=>'addresultcategory','class'=>'tiny radius button bg-blue'));
		 echo '</td></tr>';
		 }
       }
    ?>
	</tbody></table>
	</td>	
    <td>
    <?php 
	if(isset($max)){
    // TODO:graph
            echo '<table><tbody><tr><th colspan=2>Result Summery</th></tr><tr><td> Average </td><td>';
            echo number_format($average, 2, '.', ',').'</td></tr>';
            echo '<tr><td> Maximum Result </td><td>'.$max.'</td></tr>';
            echo '<tr><td> Minimum Result </td><td>'.$min.'</td></tr>';
            echo '</tbody></table>';
               
    } 
	
	?>
    
    <?php if(isset($previous_result_category)){
           
            echo '<div style="font-weight:bold">Already Recorded Result Category</div>';
            echo "<table><tbody>";
            echo "<tr><th>Name</th><th>Result From</th><th>Result To</th><th>Action</th></tr>";
			//debug($previous_result_category);
			$academicyear=str_replace('/', '-', $selected_academicyear);
			
            foreach($previous_result_category as $k=>$v){
                    echo "<tr><td>".$v['PlacementsResultsCriteria']['name']."</td><td>".$v['PlacementsResultsCriteria']['result_from']."</td><td>
                    ".$v['PlacementsResultsCriteria']['result_to']."</td><td>";
                    if (isset($check_auto_placement_already_run_not_allow_adding_or_edit) && $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
echo $this->Html->link(__('Delete'), array('action' => ''),array('onclick'=>'return false','style'=>'color:gray')); 
                    } else {
                       echo $this->Html->link(__('Delete'), array('action' => 'delete', $v['PlacementsResultsCriteria']['id'],$academicyear,$result_type), null, sprintf(__('Are you sure you want to delete \'%s\'?'), $v['PlacementsResultsCriteria']['name'])); 
                    }
                   "</td></tr>";
            }
            echo "</table></tbody>";
    } 
?>
    </td>
    </tr>
    </tbody></table>
<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Js->writeBuffer();?>
