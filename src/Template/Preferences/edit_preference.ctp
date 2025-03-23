<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<?php 
if(!empty($this->request->data['Preference'])){

echo $this->Form->create('Preference', array('action'=>'edit_preference'));
    echo '<table><tbody>';
    if(isset($college_name)){
    echo '<tr><td class="font">'.$college_name.'</td></tr>';
    }
    if(isset($student_full_name)){
        echo '<tr><td class="font">'.$student_full_name.'</td></tr>';
    }
    $count=1;
    echo '<div id="preference">';
foreach($this->request->data['Preference'] as $key => $value) {

    echo '<tr><td> Preference '.$value['preferences_order'].''.$this->Form->input('Preference.'.$key.'.department_id',array('id'=>'participating_department_id'.$count)).'</td></tr>';
    
    echo '<tr><td>'.$this->Form->hidden('Preference.'.$key.'.accepted_student_id').'</td></tr>';
    echo '<tr><td>'.$this->Form->input('Preference.'.$key.'.id').'</td></tr>';
    echo $this->Form->hidden('Preference.'.$key.'.edited_by',array('value'=>$user_id));
    $count++;
}
   echo '</div>';
 echo '<tr><td>'.$this->Form->end(array('label'=>'Save All Preference','class'=>'tiny radius button bg-blue')).'</td></tr>';
 echo '</tbody></table>';
} else {
 echo '<div class="message">'. __('You have not selected a preference to edit').'</div>';

}
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
