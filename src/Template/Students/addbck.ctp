<div class="students form" style="align:center">
<?php echo $this->Form->create('Student',array('type'=>'file'));?>
	<fieldset>
		<legend><?php echo __('Add Student'); ?></legend>
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
		        echo '<tr><td>'.$this->Form->input('firstname',array('label'=>'First name')).'</td></tr>';
		        
		        echo '<tr><td>'.$this->Form->input('middlename',array('label'=>'Middle name')).'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('lastname',array('label'=>'Last name')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('estimated_grad_date').'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('gender',array('label'=>'Gender')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('ethnicity',array('label'=>'Ethnicity')).'</td></tr>';
		        echo '<tr><td>'. $this->Form->input('lanaguage',array('label'=>'Primary Lanaguage')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('Address.0.email',array('label'=>'Email')).'</td></tr>';
		         echo '<tr><td>'. $this->Form->input('Address.0.phone',array('label'=>'Phone')).'</td></tr>';
		      
		        echo '<tr><td>'.$this->Form->input('birthdate',array('label'=>'Birth date')).'</td></tr>';
		        //echo '<tr><td>'.$this->Form->input('Attachment',array('label'=>'Upload profile picture','type'=>'file')).'</td></tr>';
		       echo '<tr><td>'.$this->element('attachments', array('plugin' => 'media','label'=>'Upload profile picture')).'</td></tr>';
		        echo "</tbody></table></td>";
		
		          // save account information in the user table
		        echo '<td><table><tbody>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td colspan=2><strong>Access Information</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td>'.$this->Form->input('User.username',array('label'=>'Desired Login Name')).'</td></td>';
		        echo '<tr><td>'.$this->Form->input('User.password',array('label'=>'Choose a password')).'</td></tr>';
		         echo '<tr><td>'.$this->Form->input('password_confirm',
            array('type'=>'password','label'=>'Confirm password')).'</td></tr>';
		        echo '</tbody></table></td></tr>';
		        echo '<tr><table><tbody><td>';
		        echo '<tr><td colspan=2><hr/></td></tr>';
		        
		        echo '<tr><td colspan=2><strong>School Information</strong></td></tr>';
                echo '<tr><td colspan=2><hr/></td></tr>';
		        echo '<tr><td>'.$this->Form->input('dormitory_id').'</td></tr>';
		       
		        echo "<tr><td>". $this->Form->input('college_id')."</td></tr>";
		        echo '<tr><td>'.$this->Form->input('department_id').'</td></tr>';
		        echo '<tr><td>'.$this->Form->input('grade_level',array('label'=>'Grade Level')).'</td></tr>';
		       
		        echo '</td></tbody></table></tr>';
		        echo '</tbody></table>';
		    echo "</div>"; // end add tab div
		    
	    ?>
	   </div> <!-- end basic info block --->
	<div id="add_address" style="display:none">
	<?php		
        echo $this->element('user_tab_menu',
                array('current_tab' => 'add_address'));

        echo "<div class=\"AddTab\">\n";
       
              echo $this->Form->input('Address.id',array('type'=>'hidden'));
            
              echo $this->Form->input('country_id',array('id'=>'country_id','label'=>'Country','error'=>false,'empty'=>null));
              
              echo "<br/>";
             
              echo $this->Form->input('region_id',array('id' => 'region_id','label' => 'Region/City','error' => false,'empty' => 'Select Country First'));
             
              echo "<br/>";
              echo $this->Form->input('city_id',array('id' => 'city_id','label' => 'City','error' => false,'empty' => 'Select Region First'));
              echo "<br/>";
              echo $this->Form->input('Address.zone/subcity');
              echo "<br/>";
              echo $this->Form->input('Address.woreda');
              echo "<br/>";
              echo $this->Form->input('Address.kebele');
              echo "<br/>";
              echo $this->Form->input('Address.house_number');
              echo "<br/>";
              echo $this->Form->input('Address.pobox',array('label'=>'P.O.Box'));
		echo '</div>'; // End add tab div
		?>
	 </div> <!-- End address block -->
	</fieldset>
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
