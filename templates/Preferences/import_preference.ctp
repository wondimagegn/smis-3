<div class="acceptedStudents index">
 <?php echo $this->Form->create('AcceptedStudent', array('controller' => 'acceptedStudents', 'action' => 'import_newly_students', 'type' => 'file'));
 
 ?>
<table>
<tbody>
<tr><th colspan=5 class="smallheading"><?php 
echo __("Beaware: Before importing the excel
    ,make sure that the value of student preferences is listed horizontally.");
    ?>
   Here is a link to the excel template that shows you how you can store 
   the data in excel that are compatible with the system database. 
   <?php 
  
    /* echo $this->Html->link('Download Import Template!', array('action'=>'download',
                                                "template",
                                                "template.xls"));
                                                */
      
   
   ?>
 
    <a href="/files/template/template.xls">Download Import Template!</a>
    </th>
<tr>
<td>
   <?php
    echo "<table><tbody><tr><th>Import Students Preferences</th></tr>";
    echo "<tr><td>";
    echo $this->Form->input('Preference.academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($defaultacademicyear)?$defaultacademicyear:'')); 
    echo "</td></tr><tr><td>";
    
    echo $this->Form->file('File').'</td></tr>';
    echo '<tr><td>'.$this->Form->submit('Upload').'</td></tr></tbody></table>';
    ?>
</td>
</tr>
<tr>
<?php
    if(isset($non_valide_rows)){
         echo "<td colspan=5>";
          echo "<ul style='color:red'>";
          foreach($non_valide_rows as $k=>$v){
                echo "<li>".$v."</li>";
          }
          echo "</ul>";
          echo "</td></tr>";
    }
   
 ?>
</tbody>
</table>
<?php    
echo $this->Form->end();

?>
</div>
