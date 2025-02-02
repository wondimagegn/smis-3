<?php 

?>
<SCRIPT language="javascript">
var image = new Image();
image.src = '/img/busy.gif';

 //Sub Cat Combo 1
 function updateSubCategory(id,tableID) {
            var table = document.getElementById(tableID);
			var rowCount = table.rows.length;
            //serialize form data
            var formData = $("#grade_type_id").val();
                for(i=1;i<=rowCount;i++){
			    $("#"+id+i).empty();
			    $("#"+id+i).attr('disabled', true);
			    }
			    //get form action
                var formUrl = '/grades/get_grade_combo/'+formData;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: formData,
                    success: function(data,textStatus,xhr){
						    for(i=1;i<=rowCount;i++){
						    $("#"+id+i).attr('disabled', false);
						    $("#"+id+i).empty();
						    $("#"+id+i).append(data);
						    }
						    grades_combo = data;
				    },
                    error: function(xhr,textStatus,error){
                            alert(textStatus);
                    }
			    });
		
		return false;
}
var grades=Array();
var grades_combo='';			
var index = 0;

<?PHP
    foreach($grades as $grade_id=>$grade_name){
    ?>
    index = grades.length;
    grades[index] = new Array();
    grades[index][0] = "<?php echo $grade_id; ?>";
    grades[index][1] = "<?php echo $grade_name; ?>";
    grades_combo +="<option value='<?php echo $grade_id;?>'><?php echo $grade_name;?></option>";
    <?php
        }
?>

		function addRow(tableID,model,no_of_fields,all_fields) {
		    
		   	var elementArray = all_fields.split(',');
		  
			var table = document.getElementById(tableID);

			rowCount = table.rows.length;
			
			var row = table.insertRow(rowCount);
           
			var cell0 = row.insertCell(0);
			
			cell0.innerHTML = rowCount;
			
			//construct the other cells
			for(var j=1;j<=no_of_fields;j++) {
				var cell = row.insertCell(j);
				
				if (elementArray[j-1] == "grade_id") {
				        var element = document.createElement("select");						
						string = "";
						for (var f=0;f<grades.length;f++) {
						   string += '<option value="'+grades[f][0]+'"> '+grades[f][1]+'</option>';
						}
						//element.addEventListener('change', function(){updateSubCategory(rowCount)}, false);
						element.id = "grade_id_"+rowCount;
			            //element.innerHTML = string;
			            element.innerHTML = grades_combo;
				 
				} else if (elementArray[j-1] == 'minimum_result') {
				       var element = document.createElement("input");
				       element.size = "4";
				       element.type = "text";
				} else if (elementArray[j-1] == "maximum_result") {
				      
				        var element = document.createElement("input");
				        element.size = "4";
				        element.type = "text";
				}
				
				
				element.name = "data["+model+"]["+rowCount+"]["+elementArray[j-1]+"]";
				
				cell.appendChild(element);
		   }   

		}

		function deleteRow(tableID) {
			
			try {
			    var table = document.getElementById(tableID);
			    var rowCount = table.rows.length;
			    if(rowCount !=0 ){
                    table.deleteRow(rowCount-1);
			    } else {
			
			        alert('No more rows to delete');
			    }
			
			}catch(e) {
				alert(e);
			}
			
		}
function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}	
</SCRIPT>

<?php 

$grade_scale_detail=array('grade_id'=>1,'minimum_result'=>2,'maximum_result'=>3);
$all_grade_scale_detail = "";
$sep = "";
foreach ($grade_scale_detail  as $key => $tag) {
        $all_grade_scale_detail.= $sep.$key;
        $sep = ",";
}
?>
<div class="gradeScales form">
<?php echo $this->Form->create('GradeScale');?>
<?php if ($role_id == ROLE_REGISTRAR ) { ?>
<p class="fs16">
<strong> Important Note: </strong> 
 This tool will help you to set grade scale for those department who grade scale not delegated.
</p>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($turn_off_search)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($turn_off_search) ? 'none' : 'display'); ?>">
<table class="fs13 small_padding">
	   
		<tr>
			<td style="width:15%">College:</td>
			<td style="width:35%"><?php 
			    echo $this->Form->input('Search.college_id',array(
            'label' => false));
			?>
			</td>
		</tr>
		  
		<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('Continue'), 
			array('name' => 'getPublishedCourse',  'div' => false)); ?></td>
		</tr>
	</table>
</div>	
<?php
}
 ?>
	<div class="smallheading"><?php echo __('Set Grade Scale'); ?></div>
	<table>
	  <td><table>
	    <?php
	
		    echo '<tr><td>'.$this->Form->input('name',array('label'=>'Scale Name')).'</td><tr>';
		    //echo '<tr><td>'.$this->Form->input('department_id').'</td><tr>';
		    echo '<tr><td>'.$this->Form->input('GradeScale.program_id').'</td><tr>';
		   if ($role_id ==ROLE_COLLEGE) {
		       if (isset($onlyfresh) && $onlyfresh==true) {
		            echo '<tr><td>'.$this->Form->input('own',array('label'=>'Scale for freshman/Department Unassigned students','checked'=>'checked','disabled'=>true)).'</td></tr>';   
		       } else {
		        echo '<tr><td>'.$this->Form->input('own',array('label'=>'Scale for freshman/Department Unassigned students')).'</td></tr>';
		       }
		    
	        }
	        
	        //echo $this->Form->input('active');
	    ?>
	    </table>
	   </td>
	   <td>
	        <table><tr><td colspan=4 ><?php echo $this->Form->input('grade_type_id',array('id'=>'grade_type_id',
		    'onchange' => 'updateSubCategory("grade_id_", "grade_scale_details")')); ?></td></tr></table>
	        <table id="grade_scale_details">
	            <tr><th>S.No</th><th>Grade</th><th>Minimum Result</th><th>Maximum Result</th></tr>
	            
	                <?php 
	                 
	                   if (!empty($this->request->data['GradeScaleDetail'])) {
		                    $count=1;
		                    
		                      foreach ($this->request->data['GradeScaleDetail'] as $bk=>$bv) {
		                      echo "<tr><td>".$count."</td><td>".$this->Form->input('GradeScaleDetail.'.$bk.'.grade_id',
		            array(
		            'options'=>$grades,'type'=>'select','label'=>false,'selected'=>!empty($this->request->data['GradeScaleDetail'][$bk]['grade_id'])?$this->request->data['GradeScaleDetail'][$bk]['grade_id'] :'','id'=>"grade_id_".$count))."</td><td>".$this->Form->input('GradeScaleDetail.'.$bk.'.minimum_result',
		            array(
		            'value'=>isset($this->request->data['GradeScaleDetail'][$bk]['minimum_result'])?$this->request->data['GradeScaleDetail'][$bk]['minimum_result']:'','label'=>false,'div'=>false,'size'=>4))."</td><td>".$this->Form->input('GradeScaleDetail.'.$bk.'.maximum_result',
		            array( 'value'=>isset($this->request->data['GradeScaleDetail'][$bk]['maximum_result'])?$this->request->data['GradeScaleDetail'][$bk]['maximum_result']:'','label'=>false,'div'=>false,'size'=>4));
		                      echo "</td></tr>";
		                      $count++;
		                  }
		         } else {
		            echo "<tr><td>1</td><td>".$this->Form->input('GradeScaleDetail.0.grade_id',
		            array(
		            'options'=>$grades,'type'=>'select','label'=>false,'selected'=>!empty($this->request->data['GradeScaleDetail'][0]['grade_id'])? $this->request->data['GradeScaleDetail'][0]['grade_id']:'','id'=>'grade_id_1'))."</td><td>".$this->Form->input('GradeScaleDetail.0.minimum_result',
		            array(
		            'value'=>isset($this->request->data['GradeScaleDetail'][0]['minimum_result'])?$this->request->data['GradeScaleDetail'][0]['minimum_result']:'','label'=>false,'size'=>4))."</td><td>".$this->Form->input('GradeScaleDetail.0.maximum_result',
		            array( 'value'=>isset($this->request->data['GradeScaleDetail'][0]['maximum_result'])?$this->request->data['GradeScaleDetail'][0]['maximum_result']:'','label'=>false,'size'=>4));
		                      echo "</td></tr>";
		                      
		         }
		         ?>
	                
	            
	        </table>
	        <table>
	            <tr><td colspan=4><INPUT type="button" value="Add Row" 
	            onclick="addRow('grade_scale_details','GradeScaleDetail',3,
	            '<?php echo $all_grade_scale_detail; ?>')" />
		            <INPUT type="button" value="Delete Row" 
		            onclick="deleteRow('grade_scale_details')" />
	            </td></tr>
	        </table>
	    </td>
	</tr>
	</table>
<?php echo $this->Form->end(__('Submit'));?>
<div class="gradeScales index">
		<h2><?php echo __('Already recorded grade scales');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('grade_type_id');?></th>
			<!--<th><?php echo $this->Paginator->sort('college_id');?></th>
			<th><?php echo $this->Paginator->sort('department_id');?></th> -->
			<th><?php echo $this->Paginator->sort('program_id');?></th>
			<th><?php echo $this->Paginator->sort('Freshman','own');?></th>
			<th><?php echo $this->Paginator->sort('active');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	
	foreach ($gradeScales as $gradeScale):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
	   
		<td><?php echo $gradeScale['GradeScale']['id']; ?>&nbsp;</td>
		<td><?php echo $gradeScale['GradeScale']['name']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($gradeScale['GradeScaleDetail'][0]['Grade']['GradeType']['type'], array('controller' => 'grade_types', 'action' => 'view', 
			$gradeScale['GradeScaleDetail'][0]['Grade']['GradeType']['id'])); ?>
		</td>
	
		<td>
			<?php echo $this->Html->link($gradeScale['Program']['name'], array('controller' => 'programs', 'action' => 'view', $gradeScale['Program']['id'])); ?>
		</td>
		<?php if ($gradeScale['GradeScale']['own']==1) ?>
		<td><?php echo ($gradeScale['GradeScale']['own']==1 ? 'Yes':'No'); ?>&nbsp;</td>
		
		<td><?php echo $gradeScale['GradeScale']['active']; ?>&nbsp;</td>
		<td><?php echo $gradeScale['GradeScale']['created']; ?>&nbsp;</td>
		<td><?php echo $gradeScale['GradeScale']['modified']; ?>&nbsp;</td>
		<td class="actions">
		    <?php 
		        $action_controller_id='add~gradeScales';
			
			?>
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $gradeScale['GradeScale']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $gradeScale['GradeScale']['id'])); ?>
			<?php 
			
			 echo $this->Html->link(__('Delete'), array('action' => 'delete', $gradeScale['GradeScale']['id'], $action_controller_id), null, sprintf(__('Are you sure you want to delete # %s?'), $gradeScale['GradeScale']['id']));
			 
			 ?> 
			<?php 
			/* echo $this->Html->link(__('Delete'), array('action' => 'delete', $gradeScale['GradeScale']['id'],$action_controller_id), null, sprintf(__('Are you sure you want to delete # %s?'), $gradeScale['GradeScale']['id']));
			*/
			 ?>
			
		</td>
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
</div>
</div>
