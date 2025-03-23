<?php ?>
<?php echo $this->Form->create('ExamGrade');?>
<script>
var grade_types=Array();
var grades_types_combo='';			
var index_gt = 0;

<?PHP
    foreach($gradeTypes as $grade_id=>$grade_name){
    ?>
    index_gt = gradeTypes.length;
    grade_types[index_gt] = new Array();
    grade_types[index_gt][0] = "<?php echo $grade_id; ?>";
    grade_types[index_gt][1] = "<?php echo $grade_name; ?>";
    grades_types_combo +="<option value='<?php echo $grade_id;?>'><?php echo $grade_name;?></option>";
    <?php
     }
?>


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

function updateGrade(updateid,selectedid) {
           //serialize form data
           var formData = $("#"+selectedid).val();
           $("#"+updateid).empty();
		   $("#"+updateid).attr('disabled', true);
		   //get form action
           var formUrl = '/grades/get_grade_combo/'+formData;
           $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: formData,
                    success: function(data,textStatus,xhr){
							$("#"+updateid).empty();
						    $("#"+updateid).append(data);
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

<?php 
if(isset($this->request->data['ExamGrade'])) {
   $totalRow=count($this->request->data['ExamGrade']);
} else {
  $totalRow=2;
}  
?>;

var totalRow ="<?php echo $totalRow ?>" ;

function updateSequence(tableID) {
	var s_count = 1;
	for(i = 1; i < document.getElementById(tableID).rows.length; i++) {
		document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
	}
}

function addRow(tableID,model,no_of_fields,all_fields) {
	var elementArray = all_fields.split(',');
    
	var table = document.getElementById(tableID);
	var rowCount = table.rows.length;
	var row = table.insertRow(rowCount);
	totalRow++;
	row.id = model+'_'+totalRow;
	var cell0 = row.insertCell(0);
	cell0.innerHTML = rowCount;
	//construct the other cells
	for(var j=1;j<=no_of_fields;j++) {
		var cell = row.insertCell(j);
		
		if (elementArray[j-1] == 'course_title') {
		   var element = document.createElement("input");
		   //element.size = "4";
		   element.type = "text";
		
		} else if (elementArray[j-1] == 'course_code') {
			   var element = document.createElement("input");
			   element.type = "text";
		} else if (elementArray[j-1] == 'credit') {
			   var element = document.createElement("input");
			   element.type = "text";
		} else if (elementArray[j-1] == 'grade_id') {
			    var element = document.createElement("select");						
				string = "";
                 
				for (var f=0;f<grades.length;f++) {
				   string += '<option value="'+grades[f][0]+'"> '+grades[f][1]+'</option>';
				}
				element.id = "grade_id_"+rowCount;
	            element.innerHTML = grades_combo;
		} else if (elementArray[j-1] == 'grade_type_id') {
			 	var element = document.createElement("select");		/*				
				string = "";
				for (var f=0;f<grade_types.length;f++) {
				   string += '<option value="'+grade_types[f][0]+'"> '+grade_types[f][1]+'</option>';
				}
*/
				element.id = "grade_type_id_"+rowCount;
	            element.innerHTML = grade_types_combo;
	
		} else if (elementArray[j-1] == 'edit') {
			   var element = document.createElement("a");
			   element.innerText = "Delete";
			   element.textContent = "Delete";
			   element.setAttribute('href','javascript:deleteSpecificRow(\''+model+'_'+totalRow+'\')');
		}
		
		element.name = "data["+model+"]["+rowCount+"]["+elementArray[j-1]+"]";
		cell.appendChild(element);
	}
	updateSequence(tableID);
}

function deleteRow(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		if(rowCount >2 ){
			table.deleteRow(rowCount-1);
			updateSequence(tableID);
		} else {
			alert('No more rows to delete');
		}
	}catch(e) {
		alert(e);
	}	
}

function deleteSpecificRow(id) {
	try {
		var row = document.getElementById(id);
		//var table = row.parentElement;
		var table = row.parentNode;
		if(table.rows.length > 2 ){
			row.parentNode.removeChild(row);
			updateSequence('grade_details');
		} else {
			alert('There must be at least one exam type.');
		}
	} catch(e) {
		alert(e);
	}
}
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
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
</script>
<?php 
$course_detail=array('course_title'=>1,'course_code'=>2,'credit'=>3,
'grade_type_id'=>4,'grade_id'=>5,'edit'=>6);
$all_course_detail = "";
$sep = "";
foreach ($course_detail  as $key => $tag) {
        $all_course_detail.= $sep.$key;
        $sep = ",";
}
?>

<?php 
//load file for this view to work on 'autocomplete' field
  //$this->Html->script('View/examGrades/academic_status_data_entry_interface', array('inline' => false));
?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="examGrades <?php echo $this->request->action; ?>">

<div class="smallheading"><?php __('Data Entry Interface');?></div>

<p class="fs16">
                    <strong> Important Note: </strong> 
                    This tool will help you to enter student academic status and semester grade manually. This interface will help the registrar to maintain archived data into the system.                
</p>
<div onclick="toggleViewFullId('ListSection')"><?php 
	if (!empty($publishedCourses) || 
(!empty($manuallStatusEntry))) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListSection" style="display:<?php echo ((!empty($manuallStatusEntry) || !empty($publishedCourses['courses'])) ? 'none' : 'display'); ?>">
<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); 
		if(isset($semester_selected)) {
			echo $this->Form->input('semester_selected', array('id' => 'SemesterSelected', 'type' => 'hidden', 'value' => $semester_selected));
		}
		?></td>
	</tr>
	<tr>
		<td>Student Number:</td>
		<td>
		<?php echo $this->Form->input('Search.studentnumber', 
array('id' => 'StudentNumber', 'class' => 'fs14','label' => false, 'type' => 'text')); ?>
		</td>
	    <td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('Continue', true), array('name' => 'listPublishedCourse', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
</table>
</div>

<?php 

if(empty($publishedCourses['courses']) 
&& !empty($manuallStatusEntry)) {
?>

<table>
	 <tr>
	   <td>
	        <table>
            <tr>
           <td><?php echo $this->Form->input('grade_type_id',array('id'=>'grade_type_id',
		    'onchange' => 'updateSubCategory("grade_id_", "grade_details")')); ?></td>
            <td><?php echo $this->Form->input('academic_status_id'); ?></td>

            </tr>
           </table>
	        <table id="grade_details">
	              <tr>
					<th style="width:5%">No</th>
					<th style="width:50%">Course Title</th>
					<th style="width:15%">Course Code</th>
					<th style="width:10%">Credit</th>
					<th style="width:10%">Grade Type</th>
				    <th style="width:10%">Grade</th>
					<th style="width:10%">&nbsp;</th>
		         </tr>
	             <?php 
	         if (!empty($this->request->data['ExamGrade'])){
		     $count=1;        
		     foreach ($this->request->data['ExamGrade'] as $bk=>$bv) {
		         echo "<tr id='ExamGrade_".$count."'><td>".$count."</td><td>".$this->Form->input('ExamGrade.'.$count.'.course_title',array('value'=>isset($this->request->data['ExamGrade'][$count]['course_title'])?$this->request->data['ExamGrade'][$count]['course_title']:'','label'=>false))."</td><td>".$this->Form->input('ExamGrade.'.$count.'.course_code',array('value'=>isset($this->request->data['ExamGrade'][$count]['course_code'])?$this->request->data['ExamGrade'][$count]['course_code']:'','label'=>false))."</td><td>".
$this->Form->input('ExamGrade.'.$count.'.credit',array('type'=>'text','label'=>false,'id'=>'credit_1'))."</td><td>".$this->Form->input('ExamGrade.'.$count.'.grade_type_id',array('id'=>'grade_type_'.$count.'','label'=>false,'onchange' => 'updateGrade("grade_type_id_'.$count.'",this.id)'))."</td><td>".$this->Form->input('ExamGrade.'.$count.'.grade_id',
array('options'=>$grades,'type'=>'select','label'=>false,
'selected'=>!empty($this->request->data['ExamGrade'][$count]['grade_id']) ? $this->request->data['ExamGrade'][$count]['grade_id']:'','id'=>'grade_id_'.$count.'')).
"</td><td><a 
href='javascript:deleteSpecificRow('ExamGrade_".$count."')'>Delete</a></td></tr>";
		              $count++;
		      }
		   } else {
		       echo "<tr id='ExamGrade_1'><td>1</td><td>".
$this->Form->input('ExamGrade.0.course_title',array(
		            'value'=>isset($this->request->data['ExamGrade'][0]['course_title'])?$this->request->data['ExamGrade'][0]['course_title']:'','label'=>false)).
"</td><td>".$this->Form->input('ExamGrade.0.course_code',
		            array( 'value'=>isset($this->request->data['ExamGrade'][0]['course_code'])?$this->request->data['ExamGrade'][0]['course_code']:'','label'=>false))."</td><td>".$this->Form->input('ExamGrade.0.credit',
		            array('type'=>'text','label'=>false,'id'=>'credit_1'))."</td><td>".$this->Form->input('ExamGrade.0.grade_type_id',
		            array(
		            'options'=>$gradeTypes,'type'=>'select','label'=>false,'selected'=>!empty($this->request->data['ExamGrade'][0]['grade_type_id'])? $this->request->data['ExamGrade'][0]['grade_type_id']:'','id'=>'grade_type_id_1',
'onchange' => 'updateGrade("grade_id_1",this.id)'))."</td><td>".$this->Form->input('ExamGrade.0.grade_id',
		            array(
		            'options'=>$grades,'type'=>'select','label'=>false,'selected'=>!empty($this->request->data['ExamGrade'][0]['grade_id'])? $this->request->data['ExamGrade'][0]['grade_id']:'','id'=>'grade_id_1'))."</td><td><a href='javascript:deleteSpecificRow('ExamGrade_1')'>Delete</a></td></tr>";
		         }
		         ?>
	                
	            
	        </table>
	        <table>
	            <tr><td colspan=4><INPUT type="button" value="Add Row" onclick="addRow('grade_details','ExamGrade',6,'<?php echo $all_course_detail; ?>')" />
		            <INPUT type="button" value="Delete Row" 
		            onclick="deleteRow('grade_details')" />
	            </td></tr>
	        </table>
	    </td>
	</tr>
</table>
<?php
		echo $this->Form->submit(__('Submit'), array('div' => false,'class'=>'tiny radius button bg-blue'));
?>
<?php 
}
else if(isset($publishedCourses) && !empty($publishedCourses)) {
	?>
	<div onclick="toggleViewFullId('Profile')"><?php 
	if (!empty($student_academic_profile)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListProfileImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListProfileTxt">Display Student Academic Profile</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListProfileImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListProfileTxt">Hide Student Academic Profile</span><?php
		}
?></div>
     <div id="Profile" style="display:<?php echo (!empty($student_academic_profile) ? 'none' : 'display'); ?>">
	<?php 
		
           echo $this->element('student_academic_profile');
	?>
     </div>
	<p class="fs13">Please select course/s and enter corresponding grade.</p>
	<table>
		<tr>
			<th style="width:10%">
<?php echo $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'SelectAll', 'div' => false, 'label' => false, 'onchange' => 'check_uncheck(this.id)')); ?>Select All</th>
 			<th style="width:25%">Course Title</th>
			<th style="width:25%">Course Code</th>
			
			<th style="width:20%">Credit</th>
			<th style="width:10%">AY/Sem.</th>
			<th style="width:10%">Grade</th>
		</tr>
		<?php
		$st_count = 0;
		$checkBoxCount=0;
		// debug($publishedCourses);
		foreach($publishedCourses['courses'] as $key => $course) {
			$st_count++;
		   	// debug($course);
			?>
			<tr>
				<td><?php 
	if(empty($course['Grade'])) {
			$checkBoxCount++;
					echo $this->Form->input('CourseRegistration.'.$st_count.'.gp', array('type' => 'checkbox', 'label' => false, 'id' => 'StudentSelection'.$st_count));
					echo $this->Form->input('CourseRegistration.'.$st_count.'.student_id', 
array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));
				}
				?></td>
				<td><?php echo $course['Course']['course_title']; ?></td>
	
				<td><?php echo $course['Course']['course_code']; ?></td>
		<td><?php echo $course['Course']['credit']; ?></td>
		<td><?php echo $course['PublishedCourse']['academic_year'].'/'.$course['PublishedCourse']['semester']; ?></td>
			
		<td>
		<?php 
if(empty($course['Grade'])) {

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.academic_year', array('value'=>$course['PublishedCourse']['academic_year']));
if(isset($course['CourseRegistration'])) {
echo $this->Form->hidden('CourseRegistration.'.$st_count.'.id', array('value'=>$course['CourseRegistration']['id']));
}
echo $this->Form->hidden('CourseRegistration.'.$st_count.'.semester', array('value'=>$course['PublishedCourse']['semester']));

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.year_level_id', array('value'=>$course['PublishedCourse']['year_level_id']));

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.section_id', array('value'=>$course['PublishedCourse']['section_id']));


if(isset($course['Course']['grade_scale_id'])) {
echo $this->Form->hidden('CourseRegistration.'.$st_count.'.grade_scale_id', array('value'=>$course['Course']['grade_scale_id']));

} else if(isset($course['PublishedCourse']['grade_scale_id'])) {

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.grade_scale_id', array('value'=>$course['PublishedCourse']['grade_scale_id']));

}


echo $this->Form->hidden('CourseRegistration.'.$st_count.'.published_course_id', array('value'=>$course['PublishedCourse']['id']));

echo $this->Form->input('CourseRegistration.'.$st_count.'.student_id', 
array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));

}

?>
<?php 
 
   $gradeList = array();
if(isset($course['Course']['GradeType']['Grade'])) {
   foreach($course['Course']['GradeType']['Grade'] as $key=>$value) {
	$gradeList[$value['grade']]=$value['grade'];
   }
  
}

if(empty($course['Grade'])) {
   
echo $this->Form->input('CourseRegistration.'.$st_count.'.grade', array('label' => false,'type'=>'select','options'=>$gradeList));



} else {
	echo ''.$course['Grade']['grade'];

}

?>


		</td>
		
	</tr>
			<?php
		}
	 
	?>
        <tr>
		<td >
			<?php 
if($checkBoxCount>0) {
echo $this->Form->submit(__('Save ', true), array('name' => 'saveGrade','class'=>'tiny radius button bg-blue', 'div' => false)); 
}
?>
		</td>
	</tr>
	</table>
	
	<?php
	
}
?>
<?php echo $this->Form->end(); ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
