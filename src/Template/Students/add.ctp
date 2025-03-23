<div class="students form" style="align:center">
<?php echo $this->Form->create('Student',array('type'=>'file'));?>
	
		<div class="smallheading" style="padding:20px;"><?php echo __('Add Student'); ?></div>
		<div id="basic_fields" style="display:block">	
		 <?php
            echo $this->element('user_tab_menu',
                array('current_tab' => 'basic_fields'));
            
            echo "<div class=\"AddTab\">\n";
                echo '<table cellspacing="0" cellpading="0"><tbody>';
                echo "<tr><td><table><tbody>";
                 echo '<tr><td colspan=2><hr/></td></tr>';
                 echo '<tr><td colspan=2><strong>Demographic Information</strong></td></tr>';
                  echo '<tr><td colspan=2><hr/></td></tr>';
		        
	            // save name in the basic information
	            echo $this->Form->hidden('Student.id');
		        echo '<tr><td>'.$this->Form->input('first_name',array('label'=>'First name')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('middle_name',array('label'=>'Middle name')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('last_name',array('label'=>'Last name')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('estimated_grad_date').'</td></tr>';
		        //echo '<tr><td>'. $this->Form->input('gender',array('label'=>'Gender')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('ethnicity',array('label'=>'Ethnicity')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('lanaguage',array('label'=>'Primary Lanaguage')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('email',array('label'=>'Email')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('phone',array('label'=>'Phone')).'</td></tr>';
		      
		        echo '<tr><td>'.$this->Form->input('birthdate',array('label'=>'Birth date')).'</td></tr>';
		        //echo '<tr><td>'.$this->Form->input('Attachment',array('label'=>'Upload profile picture','type'=>'file')).'</td></tr>';
		      // echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media','label'=>'Upload profile picture')).'</td></tr>';
		       echo '<tr><td>'. $this->Form->input('marital_status').'</td></tr>';
		        $options=array('male'=>'Male','female'=>'Female');
		        $attributes=array('legend'=>false,'label'=>false);
		        //echo $this->Form->radio('gender',$options,$attributes);
		        echo '<tr><td style="padding-left:350px;">'. $this->Form->input('gender',array('options'=>$options,'type'=>'radio','legend'=>false,'separator'=>'<br/>','label'=>false)).'</td></tr>';
		        //echo '<tr><td><ul></li>'. $this->Form->radio('gender',$options,$attributes).'</li></ul></td></tr>';
		        
		        //echo '<tr><td>'. $this->Form->input('gender',array('label'=>'Gender')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('nationality',array('label'=>'Nationality')).'</td></tr>';
		     
		        echo "</tbody></table></td>";
		
		          // save account information in the user table
		        echo '<td><table><tbody>';
		        
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Access Information</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
                echo $this->Form->hidden('User.id');
		        echo '<tr><td>'.$this->Form->input('User.username',array('label'=>'Login Name/Student Number')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('User.password',array('label'=>'Choose a password','id'=>'password')).'</td></tr>';
		         
		        echo "<tr><td>".$this->Form->input('Generated Password',array('id'=>'text','name'=>'text'))."</td></tr>"; 
		        echo "<tr><td><input type='button' id='button_generate_password' value='Generate' onclick='issuePassword(this.form)'></td></tr>";
		       
		        echo '</tbody></table></td></tr>';
		        echo '<tr><table><tbody><td>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        
		        echo '<tr><td colspan=2><strong>Classification of Admission</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
		        //echo '<tr><td>'.$this->Form->input('dormitory_id').'</td></tr>';
		         echo "<tr><td>". $this->Form->input('program_id')."</td></tr>";
				echo "<tr><td>". $this->Form->input('program_type_id')."</td></tr>";
				
		        echo "<tr><td>". $this->Form->input('college_id')."</td></tr>";
		        echo '<tr><td>'.$this->Form->input('department_id').'</td></tr>';
		        //echo '<tr><td>'.$this->Form->input('grade_level',array('label'=>'Grade Level')).'</td></tr>';
		       
		        echo '</td></tbody></table></tr>';
		        echo '</tbody></table>';
		    echo "</div>"; // end add tab div
		    
	    ?>
	   </div> <!-- end basic info block -->
	 <div id="education_background" style="display:none">	
		 <?php
            echo $this->element('user_tab_menu',
                array('current_tab' => 'education_background'));
            
            echo "<div class=\"AddTab\">\n";
             // save account information in the user table
		        echo '<table><tbody><tr><td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Senior Secondary/Preparatory school attended</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
                echo $this->Form->hidden('HighSchoolEducationBackground.0.id');
		        echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.0.name',array('label'=>' Name of school')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.0.town',array('label'=>'Town')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.0.zone',
            array('label'=>'Zone')).'</td></tr>';
          
               echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.0.region',
			array('label'=>'Region')).'</td></tr>';
			
			     echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.0.school_level',
			array('label'=>'Grade 11')).'</td></tr>';
			echo $this->Form->hidden('HighSchoolEducationBackground.1.id');
			   echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.1.name',array('label'=>' Name of school')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.1.town',array('label'=>'Town')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.1.zone',
            array('label'=>'Zone')).'</td></tr>';
          
               echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.1.region',
			array('label'=>'Region')).'</td></tr>';
			
			     echo '<tr><td>'.$this->Form->input('HighSchoolEducationBackground.1.school_level',
			array('label'=>'Grade 12')).'</td></tr>';
                echo "</tbody></table>";
      
		        echo '</td>';
		        echo '<td><table><tbody>';
		         echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Higher Education Attended</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
                echo $this->Form->hidden('HigherEducationBackground.0.id');
		        echo '<tr><td>'.$this->Form->input('HigherEducationBackground.0.name',array('label'=>' Name of institution you have attended')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('HigherEducationBackground.0.field_of_study',array('label'=>'Field of Study')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('HigherEducationBackground.0.diploma_awarded',
            array('label'=>'Diploma/Degree Awarded')).'</td></tr>';
          
               echo '<tr><td>'.$this->Form->input('HigherEducationBackground.0.date_graduated',
			array('label'=>'Date Graduated','empty'=>'--select date --','selected'=>'')).'</td></tr>';
			
			     echo '<tr><td>'.$this->Form->input('HigherEducationBackground.0.cgpa_at_graduation',
			array('label'=>'CGPA at Graduation')).'</td></tr>';
			 
            
		        echo '</td></tbody></table>';
		        echo '</tr>';
		        
		        echo '</tbody></table>';
            echo "</div>";
            ?>
     </div>
	<div id="add_address" style="display:none">
	<?php		
        echo $this->element('user_tab_menu',
                array('current_tab' => 'add_address'));

        echo "<div class=\"AddTab\">\n";
          echo '<table cellspacing="0" cellpading="0"><tbody>';
                echo "<tr><td><table><tbody>";
                 echo '<tr><td colspan=2><hr/></td></tr>';
                 echo '<tr><td colspan=2><strong>Address & Contact </strong></td></tr>';
                  echo '<tr><td colspan=2><hr/></td></tr>';
		        
	            // save student primary address 
	            echo $this->Form->hidden('Contact.0.id');
		        echo '<tr><td>'.$this->Form->input('Contact.0.id',array('type'=>'hidden')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('country_id',array('value'=>68)).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('region_id').'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('city_id').'</td></tr>';
				echo '<tr><td>'. $this->Form->input('zone_subcity',array('label'=>'Zone/Subcity')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('woreda').'</td></tr>';
		       	      
		        echo '<tr><td>'.$this->Form->input('kebele').'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('house_number',array('label'=>'House Number')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('address1',array('label'=>'Address1')).'</td></tr>';
		        
		        echo "</tbody></table></td>";
		
		          // save account information in the user table
		        echo '<td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Primary Emergency Contact</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td>'.$this->Form->input('Contact.0.first_name',array('label'=>'First Name')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('Contact.0.middle_name',array('label'=>'Middle Name')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('Contact.0.last_name',
            array('label'=>'Last Name')).'</td></tr>';
            echo '<tr><td>'.$this->Form->input('Contact.0.country_id',
            array('label'=>'Country','value'=>68)).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.region_id',
			array('label'=>'Region')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.city_id',
            array('label'=>'City')).'</td></tr>';
           
            echo '<tr><td>'.$this->Form->input('Contact.0.email',
            array('label'=>'Email')).'</td></tr>';
              echo '<tr><td>'.$this->Form->input('Contact.0.alternative_email',
            array('label'=>'Alternative Email')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.phone_home',
            array('label'=>'Phone Home')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.phone_office',
            array('label'=>'Phone Office')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.phone_mobile',
            array('label'=>'Phone Mobile')).'</td></tr>';
            
             echo '<tr><td>'.$this->Form->input('Contact.0.address1',
            array('label'=>'Address1')).'</td></tr>';
            echo '<tr><td>'.$this->Form->input('Contact.0.primary_contact',
            array('label'=>'Primary Contact')).'</td></tr>';
            
		        echo '</tbody></table></td></tr>';
		       
		        echo '</tbody></table>';
		echo '</div>'; // End add tab div
		?>
	 </div> <!-- End address block -->
	
<?php echo $this->Form->end(__('Save'));?>

</div>

<script type="text/javascript">

/*jQuery(document).ready(function() {
        if(jQuery.cookie("selectedCountry") == null){
          // default to Ethiopia 
            jQuery("#country_id").val( 68).attr('selected',true);
            //jQuery('#region_id').load('/regions/getRegions/68');
        } else {
           jQuery("#country_id").val( jQuery.cookie("selectedCountry") ).attr('selected',true);
           //jQuery('#region_id').load('/regions/getRegions/'+jQuery.cookie("selectedCountry"));
        }

        jQuery('#country_id').change(function() {
          // load last selected country using a cookie, in case page was refreshed...this also makes sure the region dropdown is populated with the last selected country's region/city 
                //var country_data = jQuery(this).val();
               // jQuery.cookie("selectedCountry", country_data);
             //   var country_select = jQuery.cookie("selectedCountry");
                jQuery('#region_id').load('/regions/getRegions/'+jQuery(this).val());
            });
    });
*/

jQuery(document).ready(function (){
 jQuery('#country_id').change(function() {
     jQuery('#region_id').load('/regions/getRegions/'+jQuery(this).val())
 });
});


/*jQuery(document).ready(function() {

    populateLists("country_id");

    jQuery("#country_id, #city_id, #region_id").change(function(data) {
        populateLists(jQuery(this).attr("id"),data);
    });

    function populateLists(listType,data) {

       

        var list;

        if(listType == "country_id") {
            // populate regions
            list; = jQuery("#region_id");
        } else if(listType == "region_id") {
            // populate cities
            list; = jQuery("#city_id");
        }

        for(var i = 0; i < data.length; ++i) {
         list.append("<option value='" + data[i] + "'>" + data[i] + "</option>");
        }
    }
});
*/


</script>
