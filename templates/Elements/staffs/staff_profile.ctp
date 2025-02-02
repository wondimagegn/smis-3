<?php ?>
<div class="row">
<div class="large-12 columns">
	<!-- tabs -->
	<ul class="tabs" data-tab>
	    <li class="tab-title active"><a href="#basicinformation">Basic</a>
	    </li>
		 
	    <li class="tab-title"><a href="#study">Study</a>
	    </li>
	   
	    
	</ul>
	<div class="tabs-content edumix-tab-horz">
	    <div class="content active" id="basicinformation">
	        <?php 
			
           if (!empty($staff_profile)) 
           {
           	debug($staff_profile);
                 
             echo "<div class=\"AddTab\">\n";
             echo '<table cellspacing="0" cellpading="0">';
             echo "<tr><td><table>";
             echo '<tr><td colspan=2><strong>Demographic Information</strong></td></tr>';

              echo '<tr><td style="padding-left:10px;">First Name:<strong> '.ucwords($staff_profile['Staff']['first_name']).'</strong></td></tr>';

               echo '<tr><td style="padding-left:10px;">Middle Name:<strong> '.ucwords($staff_profile['Staff']['middle_name']).'</strong></td></tr>';
               echo '<tr><td style="padding-left:10px;">Last Name:<strong> '.ucwords($staff_profile['Staff']['last_name']).'</strong></td></tr>';

               echo '<tr><td style="padding-left:10px;">Gender:<strong> '.ucwords($staff_profile['Staff']['gender']).'</strong></td></tr>';

               echo '<tr><td style="padding-left:10px;">BirthDate:<strong> '.$this->Time->format("M j, Y", $staff_profile['Staff']['birthdate'],NULL,NULL).'</strong></td></tr>';


              echo "</table></td>";
		      // echo '<tr><td>'.$this->element('Media.attachments').'</td></tr>';
             echo "<td><table>";
             echo '<tr><td colspan=2><strong>Profile Picture and Address</strong></td></tr>';
              
              /*
             $this->Html->link(__('Delete Picture', true), 
                           array('controller'=>'attachments','action' => 'delete',
                            $av['id'],$action_controller_id), null, sprintf(__('Are you sure you want to delete picture ?', true)))
			*/
                if(isset($staff_profile['Attachment']) && !empty($staff_profile['Attachment'])){
                    foreach($staff_profile['Attachment'] as $ak=>$av){
					     if($av['group']=="Profile"){
	                       if(!empty($av['dirname']) && !empty($av['basename']) ){
	                    
	                       echo '<tr><td>'.$this->Media->embed($this->Media->file($av['dirname'].DS.$av['basename']),
	                       array('width'=>'144')).'</td></tr>';
	                       
	                       }
	                       break;
                    	}
				    }
                } else {
                    echo '<tr><td valign="top" align="right"><img src="/img/noimage.jpg" 
                    width="144" class="profile-picture"></td></tr>';
                }

              echo '<tr><td style="padding-left:10px;">Country:<strong> '.ucwords($staff_profile['Country']['name']).'</strong></td></tr>';

               echo '<tr><td style="padding-left:10px;">Email:<strong> '.$staff_profile['Staff']['email'].'</strong></td></tr>';
                echo '<tr><td style="padding-left:10px;">Mobile:<strong> '.ucwords($staff_profile['Staff']['phone_mobile']).'</strong></td></tr>';


              echo '</table></td>';
    
		     

		     echo '</tr>';

             echo "<tr>";


		          // save account information in the user table
		        echo '<td><table>';
		        echo '<tr><td colspan=2 ><strong>Department and Service  </strong></td></tr>';

		        echo '<tr><td style="padding-left:10px;">Service Wing:<strong> '.$staff_profile['Staff']['servicewing'].'</strong></td></tr>';

		        echo '<tr><td style="padding-left:10px;">College:<strong> '.$staff_profile['College']['name'].'</strong></td></tr>';


		        echo '<tr><td style="padding-left:10px;">Department:<strong> '.$staff_profile['Department']['name'].'</strong></td></tr>';

		        echo '<tr><td style="padding-left:10px;">Highest Degree:<strong> '.$staff_profile['Staff']['education'].'</strong></td></tr>';

		        echo '<tr><td style="padding-left:10px;">Academic Rank:<strong> '.$staff_profile['Position']['position'].'</strong></td></tr>';

		        echo '</tbody></table></td>';

              echo '</tr>';
             echo "</table>";


          	 echo "</div>";    // end of tab
		    }        
		  
		   ?>

	    </div>

       <div class="content" id="study">
	        <?php 
	        if (!empty($staff_profile)) 
           {
             debug($staff_profile); 
             echo "<div class=\"AddTab\">\n";
				echo '<table cellspacing="0" cellpading="0">';
				  echo '<tr><th>Education</th><th>Country Studied</th><th>Specialization</th><th>Committement Signed</th><th>Action</th></tr>';
					foreach ($staff_profile['StaffStudy'] as $stk => $stvalue) {
						  echo '<tr>';
						  	echo '<td>'.ucwords($stvalue['education']).'</td>';
						  	echo '<td>'.$countries[$stvalue['country_id']].'</td>';
						  	echo '<td>'.ucwords($stvalue['specialization']).'</td>';
						  		echo '<td>';
						  		echo $stvalue['committement_signed']==true ? 'Yes':'No';
						  		echo '</td>';
						  		echo '<td>';
						  		/*
						  		echo $this->Html->link('View', array('controller' => 'staffStudies', 'action' => 'view', $stvalue['id'])).'  '.$this->Html->link('Edit','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalAddStudy',
'data-reveal-ajax'=>'/staffs/ajax_add_study/'
.$staff_profile['Staff']['id'].'/'.$stvalue['id']));
						  		*/
echo $this->Html->link('View','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalCommitmentDetail',
'data-reveal-ajax'=>'/staffStudies/view/'
.$stvalue['id'])).'  '.$this->Html->link('Edit','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalAddStudy',
'data-reveal-ajax'=>'/staffs/ajax_add_study/'
.$staff_profile['Staff']['id'].'/'.$stvalue['id']));
						  		echo '</td>';

						  echo '</tr>';
					}
				echo '</table>';
          	 echo "</div>";    // end of tab
		    }        
		    ?>
<?php 
 echo '<table>';
 echo '<tr><td>'.$this->Html->link('Add Study','#',array('data-animation'=>"fade",'data-reveal-id'=>'myModalAddStudy',
'data-reveal-ajax'=>'/staffs/ajax_add_study/'
.$staff_profile['Staff']['id'])).'</td></tr>';
 echo '</table>';
?>
	    </div>

	   
	   

	</div>
<!-- end of tabs -->

</div>
</div>


<div class="row">
	<div class="large-12 columns">
		
		<div id="myModalAddStudy" class="reveal-modal" data-reveal>

		</div>
		

		<div id="myModalCommitmentDetail" class="reveal-modal" data-reveal>

		</div>
	</div>
</div>