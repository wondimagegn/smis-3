<?php //echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php //echo $this->Html->script('jquery-department_placement');?>
<?php echo $this->Form->create('AcceptedStudent', array('action' => 'auto_placement'));?> 
<script type='text/javascript'>

var image = new Image();
image.src = '/img/busy.gif';
//$("#runautoplacementbutton").attr('disabled', true);
 //Get placement setting summery 
function getPlacementSummery() {
            //serialize form data
            var summery = $("#academicyear").val();
            $("#academicyear").attr('disabled', true);
            $("#runautoplacementbutton").attr('disabled', true);
           
            $("#summery_student_result_category").empty().
            html('<img src="/img/busy.gif" class="displayed" >');
          //get form action
            var formUrl = '/reservedPlaces/get_summeries/'+summery;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: summery,
                success: function(data,textStatus,xhr){
                    $("#academicyear").attr('disabled', false);
                    $("#runautoplacementbutton").attr('disabled', false);
                    $("#summery_student_result_category").empty();
                    $("#summery_student_result_category").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
 
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="reservedPlaces form">
<h3>Auto Student Placement to Departments</h3>
<table><tbody><tr><td width="10%">
<table><tbody><tr> 
	
	<td>
	<?php if (!isset($auto_already_run)) { ?>

	 <?php 
	        echo '<div class="fs14">Academic Year</div>';
			echo $this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
            'label' => false,'onchange'=>'getPlacementSummery()','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--"));
            }
             ?>
    
	</td></tr>
	<?php 
	 $options=array('C'=>'Normal Assignment','Q'=>'Quota Based Assignment');
	 $attributes=array('legend'=>false,'label'=>false);
		        //echo $this->Form->radio('gender',$options,$attributes);
		       // echo '<tr><td>'. $this->Form->radio('gender',$options,$attributes).'</td></tr>';
		        ?>
	
</tr></tbody></table>
</td>

<td width="90%" id="summery_student_result_category">
	
</td>

</tr>
<tr><td colspan=3>
<?php 
//echo $this->Form->end(__('Run Auto Placement')); 
if (!isset($auto_already_run)) { 
echo $this->Form->Submit(__('Run Auto Placement'),array('div'=>false,'name'=>'runautoplacement',
'id'=>'runautoplacementbutton','class'=>'tiny radius button bg-blue'));
}
?>
</td></tr>
<tr><td colspan=2>
<?php
    
if(!empty($autoplacedstudents)){
$summery=$autoplacedstudents['auto_summery'];

echo "<table><tbody><tr><td>".$this->Html->link($this->Html->image("pdf_icon.gif",array("alt"=>"Print to PDF")),
array('action'=>"print_autoplaced_pdf"),array('escape'=>false))." Print</td><td>".$this->Html->link($this->Html->image("xls-icon.gif",array('alt'=>'Export To Xls')),array('action'=>"export_autoplaced_xls"),array('escape'=>false))." Export</td></tr></tbody></table>";
echo "<table><tbody>";
echo "<tr><th colspan=3> Summery of Auto Placement.</th></tr>";
echo "<tr><th>Department</th><th>Competitive Assignment</th><th> Privilaged Quota Assignment</th>";
foreach ($summery as $sk=>$sv){
         echo "<tr><td>".$sk."</td><td>".$sv['C']."</td><td>".$sv['Q'].'</td>';
        
}
echo "</tbody></table>";
unset($autoplacedstudents['auto_summery']);

foreach($autoplacedstudents as $key =>$data){
?>
<table>
 <tr><td colspan=11 class="headerfont"><?php echo $key ?></td></tr> 
	<tr>
           
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
			<th><?php echo ('Assignment Type');?></th>
			<th><?php echo ('EHEECE Total Result');?></th>
			<th><?php echo ('Preference Order');?></th>
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Academic Year');?></th>
			<th><?php echo ('Department approval');?></th>
			
			<th><?php echo ('Placement Type ');?></th>
			<th><?php echo ('Placement Based');?></th>
			
	</tr>
	<?php
	$i = 0;
	foreach ($data as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
      
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['assignment_type']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?>&nbsp;</td>
		<td><?php 
		if(!empty($acceptedStudent['Preference'])){
		       foreach($acceptedStudent['Preference'] as $key=>$value){
		        if($value['department_id']==$acceptedStudent['Department']['id']){
	                	echo $value['preferences_order']; 
	                	break;
	        	}
		    }
		}
		?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Department']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		<td><?php echo isset($acceptedStudent['AcceptedStudent']['approval'])?'Approved By Department':'Not Approved By Department'; ?>&nbsp;</td>
	
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>

<?php 
}
} 
 ?>
</td></tr>
    </tbody></table>
   
</td></tr>
</tbody></table>

</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end(); ?>
