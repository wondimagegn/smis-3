
<?php 
  echo $this->Form->Create('AcceptedStudent');
?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseRegistrations index">

 	<div class="smallheading"><?php echo __('Placement Report View ');?></div>

	<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.academic_year',array('options'=>$acyear_array_data,'empty'=>'--select academic year--','required'=>true,'id'=>'AcademicYear',
'onchange'=>'updateCourseListOnChangeofOtherField()','label'=>false)); ?></td>
		<td style="width:15%">Department:</td>
		<td style="width:50%"><?php echo $this->Form->input('Search.department_id',
array('required'=>true,'empty'=>'--select dept--',
'id'=>'DepartmentId','label'=>false)); ?></td>
	</tr>
	<tr>
		<td style="width:15%">Result Criteria:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.result_criteria_id',
array('id'=>'PlacementResultCriteria','label'=>false)); ?></td>
		<td style="width:15%">Gender:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.sex',
array('options'=>array('all'=>'All','male'=>'Male','female'=>'Female'),'label'=>false)); ?></td>
		
	</tr>

	<tr>
		<td style="width:15%">Placement Based:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.placement_based',
array('options'=>array('all'=>'All','C'=>'Competitive','Q'=>'Quota'),'label'=>false));  ?></td>
		<td style="width:15%">Assigned:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.placementtype',
array('options'=>array('all'=>'All','AUTO PLACED'=>'AUTO PLACED','DIRECT PLACED'=>'DIRECT PLACED','REGISTRAR PLACED'=>'REGISTRAR PLACED','CANCELLED PLACEMENT'=>'CANCELLED PLACEMENT'),'label'=>false)); ?></td>
		
	</tr>

	
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit('Search',array('class'=>'tiny radius button bg-blue','name' => 'search', 'div' => false,'id'=>'Search')); ?>
		</td>
	</tr>
</table>

<?php 
if(isset($autoplacedstudents) && !empty($autoplacedstudents)) {

$summery=$autoplacedstudents['auto_summery'];
unset($autoplacedstudents['auto_summery']);

/*

echo "<table><tbody><tr><td>".$this->Html->link($this->Html->image("pdf_icon.gif",array("alt"=>"Print to PDF")),
array('action'=>"print_autoplaced_pdf"),array('escape'=>false))." Print</td><td>".$this->Html->link($this->Html->image("xls-icon.gif",array('alt'=>'Export To Xls')),array('action'=>"export_autoplaced_xls"),array('escape'=>false))." Export</td></tr></tbody></table>";
*/

echo "<table><tbody><tr><td>".$this->Form->submit('Generate PDF',array('class'=>'tiny radius button bg-blue','name' => 'generatePlacedList', 'div' => false,'id'=>'generatePlacedList'))."</td></tr></tbody></table>";
echo "<table><tbody>";
echo "<tr><th colspan=5> Summery of Auto Placement.</th></tr>";
 echo "<tr><th>Department</th><th>Competitive</th><th> Privilaged Quota</th><th>Female By Quota</th><th>Female By Competition</th>";
foreach ($summery as $sk=>$sv){
         echo "<tr><td>".$sk."</td><td>".$sv['C']."</td><td>".$sv['Q'].'</td>
<td>'.$sv['QF'].'</td><td>'.$sv['CF'].'</td>';
        
}
echo "</tbody></table>";

foreach($autoplacedstudents as $key =>$data)
{

?>
<table>
 <tr><td colspan=12 class="headerfont"><?php echo $key ?></td></tr> 
	<tr>
            <th><?php echo ('S.No');?></th>
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
			<th><?php echo ('EHEECE');?></th>
            <th><?php echo ('Pre Result');?></th>
			<th><?php echo ('Preference Order');?></th>
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Academic Year');?></th>
			<th><?php echo ('Department approval');?></th>
			<th><?php echo ('Placement Type ');?></th>
			<th><?php echo ('Placement Based');?></th>
			
	</tr>
	<?php
	$i = 0;
	$count=1;
	foreach ($data as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
        <td><?php echo $count++; ?>&nbsp;</td>
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['freshman_result']; ?>&nbsp;</td>
		<td><?php 
		if(!empty($acceptedStudent['Preference'])){
		       foreach($acceptedStudent['Preference'] as $key=>$value) {
		        if($value['department_id']==$acceptedStudent['Department']['id']){
	                	echo $value['preferences_order']; 
	                	break;
	        	}
		    }
		}
		?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Department']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		<td><?php echo isset($acceptedStudent['AcceptedStudent']['approval']) ? 'Yes':'No'; ?>&nbsp;</td>
	
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>

	<?php 
	} 


}

?>

</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->


<?php 

echo $this->Form->end();
?>

<script>
function updateCourseListOnChangeofOtherField() 
{
           //serialize form data
			var formData='';
			var academic_year= $("#AcademicYear").val().replace("/", "-");
            if(typeof academic_year!="undefined") 
            {
                 formData = academic_year;
		    } else {
              return false;
		    }
			
            $("#PlacementResultCriteria").attr('disabled', true);
            $("#DepartmentId").attr('disabled', true);
			$("#Search").attr('disabled',true);
			//get  participating department
            var formUrl = '/participatingDepartments/getParticipatingDepartment/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
				        $("#AcadamicYear").attr('disabled', false);
					    $("#DepartmentId").attr('disabled', false);
                        $("#Search").attr('disabled',false);
			
						$("#DepartmentId").empty();
					    $("#DepartmentId").append(data);
                    
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
            var formUrl = '/placementsResultsCriterias/getPlacementResultCriteria/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
				        $("#AcadamicYear").attr('disabled', false);
					    $("#PlacementResultCriteria").attr('disabled', false);
						$("#PlacementResultCriteria").empty();
					    $("#PlacementResultCriteria").append(data);
                    
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
		    			
			return false;
		
 }

</script>
