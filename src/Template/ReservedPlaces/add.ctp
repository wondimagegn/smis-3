<?php //echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php //echo $this->Html->script('jquery-department_placement');?>
<?php echo $this->Form->create('ReservedPlace');?>

<script type="text/javascript">
var dep_capacity=Array();
var gr_capacity=Array();   
<?php
$j_count = 0;
$quota = 0;
foreach($dept_capacity_summery as $dck=>$dcv){
?>
dep_capacity[<?php echo $j_count++; ?>] = <?php echo $dcv['R']; ?>;
<?php
$quota+=$dcv['Q'];
    }
?>
<?php
$g_count = 0;
$gr_total = 0;
foreach($summeryresultcategorystudent as $k=>$v){
$gr_total += $v;
?>
gr_capacity[<?php echo $g_count++; ?>] = <?php echo $v; ?>;

<?php
    }
?>  
var gr_total = <?php echo $gr_total; ?>;
var quota = <?php echo $quota; ?>;
function adjustSum(x, y)
    {
    run_once = true;
    var d_sum = 0;
    var g_sum = 0;
    var d_remain = 0;
    var gr_assigned_total = 0;
    var non_assigned = 0;
    for(i = 0; i < gr_capacity.length; i++)
      {
          ref = window.document.getElementById(i+''+y);
          if(!isNaN(ref.value) & ref.value >= 0)
            d_sum += Number (ref.value);
          else
            {
                alert("Please usse only valid numbers");
                ref.focus();
                ref.select();
            }
      }
        
      d_remain = dep_capacity[y]-d_sum;
      ref=window.document.getElementById('d'+y);
      
      if(d_remain < 0) {
            ref.innerHTML="Over! Deduct "+(d_remain*-1);
            ref.style.color="red";      
      }
      else if(d_remain == 0){
            ref.innerHTML="Full!";
            ref.style.color="green";
      }
      else {
            ref.innerHTML="Left with "+d_remain;
            ref.style.color="red";
      }
      //alert(gr_assigned_total);
      for(i = 0; i < gr_capacity.length; i++){
          for(j = 0; j < dep_capacity.length; j++){
            ref = window.document.getElementById(i+''+j);
            if(!isNaN(ref.value))
                gr_assigned_total += Number(ref.value);
          }
      }
      //alert(gr_assigned_total);
      for(i = 0; i < gr_capacity.length; i++){
           gr_dept_sum = 0;
           gr_diff=gr_total-gr_assigned_total;
           
           for(j=0; j<dep_capacity.length;j++){
                gr_dept_sum+=Number (window.document.getElementById(i+''+j).value);
           } 
           ref = window.document.getElementById('g'+i);
           if (gr_capacity[i]<gr_dept_sum) {
                    ref.innerHTML="Deduct "+(gr_dept_sum-gr_capacity[i]);
                    ref.style.color="red";
           } else if(gr_diff-quota==0) {
                    ref.innerHTML="Full";
                    ref.style.color="green";
           } else if(gr_diff-quota>0) {
                    //
                    if(gr_dept_sum==gr_capacity[i]){
                        ref.innerHTML="Full";
                        ref.style.color="green";
                    } else if ((gr_diff - quota)>=(gr_capacity[i]-gr_dept_sum)) {
                        ref.innerHTML="Left with "+(gr_capacity[i]-gr_dept_sum);
                        ref.style.color="red";
                    
                    } else {
                        ref.innerHTML="Left with "+(gr_diff - quota)+'-'+(gr_capacity[i]-gr_dept_sum);
                        ref.style.color="red";
                    
                    }
           } else {
                if(gr_diff < 0) {
                     if(gr_capacity[i]>quota)
                        min_value=gr_capacity[i]-quota;
                     else
                       min_value=0;
                }
                else {
                min_value = (quota-gr_diff) > gr_capacity[i] ? 0 : (gr_capacity[i]-(quota-gr_diff));
                }
                ref.innerHTML='You can deduct '+(gr_capacity[i]-min_value);
                ref.style.color="green";
                     
           }
      }
    }
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	<h3>Reserved Place View/Edit/Add Setting for  Student Auto Placement to Deprtment</h3>
	
<?php 
//only visible to allow the user to search by academic year.
if(!isset($selected_academic_year)){
  echo '<table><tbody>';
	   echo '<tr><td>';  
		echo $this->Form->input('academicyear',array('id'=>'admissionyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected)?$selected:''));
		echo '</td></tr>';
		
		 echo '<tr><td>';    
		    echo $this->Form->Submit('Continue',array('div'=>false,
 'name'=>'prepandacademicyear','class'=>'tiny radius button bg-blue'));
		 echo '</td></tr>';
		 echo '</tbody></table>';
} else {
          if(isset($check_auto_placement_already_run_not_allow_adding_or_edit)
		    && $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
		      echo '<div class="info-box info-message"><span></span>Reserved Place for result category for  '.$selectedAcademicYear.' academic year to student auto placement to department for '.$college_name.'.It is only for view,you can  add or edit reserved place before running the auto placement or after proper cancellation.</div>';
		  }
?>
<p class="fs16">
    <?php echo $college_name.' has <strong> '.$total_students_college_academicyear.
	   '</strong> accepted regular students but not assigned to departments in '.$selectedAcademicYear.' academic year.';?>
</p>
<table>
<tr>
    <td style="width:25%">
          <?php 
         echo "<table><tbody><tr><th>Category</th><th>Total Allowed.</th></tr>";
          foreach($summeryresultcategorystudent as $k=>$v){
	            echo '<tr><td>'.$k.'</td> <td>'.$v.'</td></tr>';
	    }
	    echo '</tbody></table>';
        ?>
    </td>
    <td style="width:75%">
         <?php 
        
            echo "<table ><tbody><tr><th>Department</th><th>Privilaged Quota Reserved.</th><th>

            Remaining Department Capacity</th></tr>";
            foreach($dept_capacity_summery as $dck=>$dcv){
                    echo "<tr><td>".$dck.'</td><td>'.$dcv['Q'].'</td><td>'.$dcv['R'].'</td></tr>';
            }
            echo "</tbody></table>";
        
        ?>
    </td>
</tr>
</table>
<table>
<tbody>
    <tr>
    <td>
           <table style="table-layout:fixed"><tbody><tr>
		    <?php 
		        $count=0;$width=25;
		       if(!empty($placementsResultsCriterias)){
		        $count=count($placementsResultsCriterias);
		        $width=100/$count;
		        foreach($placementsResultsCriterias as $k =>$v){
		            echo '<th style="width:12%;padding:0px; margin:0px;">'.$v.'</th>';
		        }
		       }
		    ?>
		    </tr>
		    <tr>
		 
		      <?php
	      $x = -1; $y = -1;
	      $count_for_status = 0;
		  //debug($placementsResultsCriterias);
	       foreach($placementsResultsCriterias as $k=>$v){
	           $count_for_status++;
	           $x++;
	           $y = -1;
	            echo '<td><table><tbody>';
	           //debug($departments);
	           
		        foreach($departments as $key=>$value) {
		        $y++;
		        //debug($value);
		        echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.placements_results_criteria_id',
		        array('value'=>$k));
	            //echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.id');
	            echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.college_id',array('value'=>$value['College']['id']));
		        echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.participating_department_id', array('value'=>$value['Department']['id']));
		         echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.academicyear', array('value'=>$selectedAcademicyear));
		
	    ?>
	
	        <?php 
	     
		    if(!empty($reservedplacess)){

		            foreach($reservedplacess as $rk=>$rv){
		                    if($rv['ReservedPlace']['placements_results_criteria_id']
		                    ==$k && $rv['ReservedPlace']['participating_department_id']==$value['Department']['id']){
		                   
		                      echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.id',
		                      array('value'=>$rv['ReservedPlace']['id']));
		                    
							 echo '<tr><td>'.$value['Department']['shortname'].'';
							   if(isset($check_auto_placement_already_run_not_allow_adding_or_edit)
		    && $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
							   	echo '<br />'.$rv['ReservedPlace']['number'];
		                     /*
		                     echo $this->Form->input('ReservedPlace.'.$k.$key.'.number',
							 array('style'=>'width:22px','value'=>(isset(
							 $this->request->data['ReservedPlace'][$k.$key]['number']) &&
							 !empty($this->request->data['ReservedPlace'][$k.$key]['number'])) ? 
							 $this->request->data['ReservedPlace'][$k.$key]['number'] : (
							 isset($rv['ReservedPlace']['number']) ? 
							 $rv['ReservedPlace']['number'] : ''),
							 'id'=>$x.$y,'onBlur'=>'adjustSum('.$x.','.$y.');','label'=>false,'readOnly'=>true));
		                 	*/
							 
							 } else {
							 echo $this->Form->input('ReservedPlace.'.$k.$key.'.number',
							 array('style'=>'width:60px;','value'=>(isset(
							 $this->request->data['ReservedPlace'][$k.$key]['number']) &&
							 !empty($this->request->data['ReservedPlace'][$k.$key]['number'])) ? 
							 $this->request->data['ReservedPlace'][$k.$key]['number'] : (
							 isset($rv['ReservedPlace']['number']) ? 
							 $rv['ReservedPlace']['number'] : ''),
							 'id'=>$x.$y,'onBlur'=>'adjustSum('.$x.','.$y.');'));
							 
							 }
							 echo '</td>';
		    if(count($placementsResultsCriterias) == $count_for_status)
		        echo '<td rowspan=2 id="d'.$y.'" style="vertical-align:top; padding-top:20px; width:90px"></td>';
		    echo '</tr>';
		                     //break;
		                    }
		            }
		    } else {
		       echo $this->Form->hidden('ReservedPlace.'.$k.$key.'.id');
		                     echo '<tr><td>'.$value['Department']['shortname'].''.$this->Form->input('ReservedPlace.'.$k.$key.'.number'
		    ,array('style'=>'width:60px','label'=>'No','id'=>$x.$y,'onBlur'=>'adjustSum('.$x.','.$y.');')).'</td>';
		    if(count($placementsResultsCriterias) == $count_for_status)
		        echo '<td rowspan=2 id="d'.$y.'" style="vertical-align:top; padding-top:20px; width:90px"></td>';
		    echo '</tr>';
		    }
		 
		     if(isset($preference_count)){
		                 $count=0;
		                 echo '<tr><td><table style="border: #CCC solid 1px; padding:0px; margin:0px;

		                 width:100px;">

						 <tr><td class="font" style="border-right: #CCC solid 1px; padding:0px; margin:0px">Pref.</td>

						 <td class="font" style="padding:0px; margin:0px">No.</td>
<td class="font" style="padding:0px; margin:0px">Female</td>

						 </tr>';
						
						 foreach($preference_count as $dp_id=>$pv){
		                        if($dp_id == $value['Department']['id']){
		                        	/*
		                             foreach($pv[$k] as $o=>$oc){
		                                echo '<tr><td style="border-right: #CCC solid 1px; padding:0px; margin:0px">'.$o.
		                                '</td><td style="padding:0px; margin:0px">'.$oc.'</td></tr>';	       
		                                 $count++;
		                                 if($count==3){
		                                  break;
		                                 }
		                             }
		                             */
		                         foreach($pv[$k]['pref'] as  $o=>$occ){
		                          echo '<tr><td style="border-right: #CCC solid 1px; padding:0px; margin:0px">'.$o.'</td><td style="padding:0px; margin:0px">'.$occ.'</td>
<td style="padding:0px; margin:0px">'.$pv[$k]['female'][$o].'</td></tr>';	       
		                                 $count++;
		                                 if($count==3){
		                                  break;
		                                 }
                                   
		                           }
		                           
		                        }
		                 
						 }
						 echo '</table></td></tr>';
		         
		   }
	    ?>
	
	    <?php
		     }
		     echo '<tr><td id="g'.$x.'">&nbsp;</td></tr>';
		     echo '</tbody></table></td>';
	      }
	    
	       echo $this->Form->hidden('academicyear');
	      ?>
           	
		    </tr>
		    <tr><td><?php 
		    if(isset($check_auto_placement_already_run_not_allow_adding_or_edit)
		    && $check_auto_placement_already_run_not_allow_adding_or_edit>0) {
		        
		    } else {
		       echo '<span>'.$this->Form->Submit('Submit',array('div'=>false,
		    'name'=>'reservedplaces','class'=>'tiny radius button bg-blue')).'</span>';  
                    if(!empty($reservedplacess)){
				 echo '&nbsp;&nbsp;&nbsp;&nbsp;<span>'.$this->Form->Submit('Delete',array('div'=>false,
		    'name'=>'deleteReservedplaces','class'=>'tiny radius button bg-blue')).'</span>';  
                     }
		    }
		  
		    
		    
		    ?></td></tr>
	      </tbody>
	     </table>
    </td>
   </tr>
</tbody>
</table>
<?php } ?>
         
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
    </div> <!-- end of box-body -->
</div><!-- end of box -->

<script type="text/javascript">
for(k = 0; k < dep_capacity.length; k++){
  adjustSum(0, k);
  }
</script>
