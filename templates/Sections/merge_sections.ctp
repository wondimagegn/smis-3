<?php 
 echo $this->Form->create('Section');  
?>
<script type='text/javascript'>
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
<div class="box">
  <div class="box-body">
	<div class="row">
		<div class="large-12 columns">
		     <h2 class="box-title">
			<?php echo __('Section Merge');?>
		      </h2>
		</div>

               <div class="large-12 columns">
		  
<p class="fs16">
                    <strong> Important Note: </strong> 
                    This tool will help you to merge section for the purpose of management 
                     if the number of students in given section 
                    has too small number.
                    
</p>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($sections)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($sections) ? 'none' : 'display'); ?>">
  <table class="fs13 small_padding">
	   
		<tr>
			<td style="width:15%">Academic Year:</td>
			<td style="width:35%">
			<?php 
			    echo $this->Form->input('Section.academicyear',array(
            'label' => false,'type'=>'select','style'=>'width:60%','options'=>$acyear_array_data,
          
                'selected'=>isset($this->request->data['Section']['academicyear'])?
                $this->request->data['Section']['academicyear']:
                (isset($defaultacademicyear) ? $defaultacademicyear:'' )
            
            )
            
            );
			?>
			</td>
			<td style="width:13%"> Program:</td>
			<td style="width:37%">
			<?php 
			   echo $this->Form->input('Section.program_id',array('id'=>'program_id','label'=>false,
			 'type'=>'select','div'=>false,'style'=>'width:60%'));
			?>
			
			</td>
		</tr>
		  
		<tr>
			
			<td style="width:15%"> Program Type:</td>
			<td style="width:35%">
			&nbsp;
			<?php 
			  echo $this->Form->input('Section.program_type_id',array('id'=>'program_type_id','label'=>false,
			 'type'=>'select','div'=>false,'style'=>'width:60%'));
			
			?>		
			</td>
			<td style="width:13%">Year Level:</td>
			<td style="width:37%"><?php 
			   if(ROLE_COLLEGE != $role_id ) {  
                echo $this->Form->input('year_level_id',array('id'=>'year_level_id','label'=>false,
			     'type'=>'select','div'=>false,'style'=>'width:60%'));
                }
			    
			   
			
			 ?>
			</td>
		</tr>
	  
		<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('Continue'), 
			array('name' => 'search','class'=>'tiny radius button bg-blue','div' => false)); ?></td>
		</tr>
	</table>
</div>

    <?php 
 if(!empty($sections)){
        $section_list_name=array();
        foreach($sections as $key=>$value) {
            echo $this->Form->hidden('Section.'.$key.'.id', array('value'=>$value['Section']['id']));
            $section_list_name[]=$value['Section']['name'].' (Current hosted students: '.$current_sections_occupation[$key].
			', Section students curriculum: '.$sections_curriculum_name[$key].')';
        }
    ?>
    <table style="width:100%;">
    <?php 
        echo '<tr><td class="auto-width">'.$this->Form->input('Section.Sections', array('type' => 'select', 'multiple' => 
			'checkbox','div'=>'input select','options'=>$section_list_name)).'</td></tr>';
       
        echo '<tr><td >'. $this->Form->Submit('Merge',array('name'=>'merge','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
        $this->Form->end();     
    ?>
	</table>
<?php 
} else if(empty($sections) && !($isbeforesearch)){

    echo "<div class='info-box info-message'><span></span>No section is found with the search criteria</div>";
}
?>
		</div>
          </div>
   </div>
</div>
<?php 
 $this->Form->end();
?>
