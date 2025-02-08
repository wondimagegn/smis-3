<?php echo $this->Form->create('MergedSectionsExam');  ?>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">


<div class="smallheading"><?php echo __('Merge Sections For Exam'); ?></div>
	<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>

<table cellpadding="0" cellspacing="0">
	<?php 
       	echo '<tr><td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'empty'=>"--Select Program--", 'style'=>'width:200PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,'empty'=>"--Select Program Type--",'style'=>'width:200PX')).'</td>'; 
        echo '<td class="font"> Semester</td>';
		echo '<td>'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'),'empty'=>'--select semester--','style'=>'width:200PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?> 
</table>
 </div>
 </div>
  <div class="row">
	  <div class="large-6 columns">
 <?php 
 if(!empty($formatedSections)){
        
    ?>
    <table style="width:100%;">
    <?php 
	foreach($formatedSections as $sk=>$sections){
	?>
		<tr><td><div class="smallheading"><?php echo __('Year Level: '.$sk); ?></div></td></tr>
	<?php 
		$section_list_name=null;
        foreach($sections as $key=>$value) {
           
            $section_list_name=$value['Section']['name'];
			
			echo '<tr><td>'.$this->Form->input('Section.selected.'.$value['Section']['id'], array('class'=>'mergedSection','label'=>$section_list_name,'type'=>'checkbox','value'=>$value['Section']['id'])).'</td></tr>';
		}
	}
		echo $this->Js->get("input.mergedSection")->event("change", $this->Js->request(array('controller'=>'publishedCourses',
			'action'=>'getPublishedCoursesForExam'), array(
						'update'=>"#PublishedCoursesList",
						'async' => true,
						'method' => 'post',
						'dataExpression'=>true,
						'data'=> $this->Js->serializeForm(array(
						'isForm' => false,
						'inline' => true
			))
		))
	);
	
		
		
    ?>
	
	</table>
<?php 
} else if(empty($formatedSections) && !($isbeforesearch)){
    echo "<div class='info-box info-message'><span></span>No section is found with the search criteria</div>";
}
?>

	  </div> <!-- end of columns 6 -->
	  <div  class="large-6 columns">
	    <div id='PublishedCoursesList'></div></td>
	  </div>

	</div> <!-- end of row -->
	
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end();     ?>
<script type="text/javascript">

  $(document).ready(function() { 
      $(".mergedSection").each( function() {
      	
            if ($(this).is(":checked")){
               $('#PublishedCoursesList').load('/publishedCourses/getPublishedCoursesForExam/2');
            }
    })
  }); 
</script>

<?php echo $this->Js->writeBuffer();?>
