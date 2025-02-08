<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
function updateClassRoomCheckBox() {
            //serialize form data

            var subCat = $("#ajax_program_type").val()+'~'+$("#ajax_program").val();
$("#ajax_class_room_checkboxs").attr('disabled', true);
$("#ajax_class_room_checkboxs").empty().html('<img src="'+image.src+'" class="displayed" />');
//get form action
            var formUrl = '/classRoomBlocks/class_rooms_checkboxs/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_class_room_checkboxs").attr('disabled', false);
$("#ajax_class_room_checkboxs").empty();
$("#ajax_class_room_checkboxs").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
        }
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classRoomBlocks form">
<?php echo $this->Form->create('ClassRoomBlock'); ?>
	<div class="smallheading"><?php echo __('Assign/Edit Class Rooms Program Types'); ?></div>
	<table>
	<?php
		echo '<tr><td>'.$this->Form->input('program_id',array('id'=>'ajax_program')).'</td>';
		echo '<td>'.$this->Form->input('program_type_id',array('id'=>'ajax_program_type','empty'=>'--Select Program Type ---','onchange'=>'updateClassRoomCheckBox()','selected'=>isset($seleted_program_type_id)?$seleted_program_type_id:"")).'</td></tr>';
		?>
		<tr><td colspan="2"><div id="ajax_class_room_checkboxs">
		<?php
			if(!empty($fromadd_organized_classRooms_blocks_data)){
				foreach($fromadd_organized_classRooms_blocks_data as $ocrbdk =>$ocrbdv){
					echo '<table>';
					foreach($ocrbdv as $crbk =>$crbdv){
						echo '<tr><td class="font" width="260PX">'. $ocrbdk .' - Block '.$crbk.' - Class Rooms: </td>';
						//echo '<tr><td><table><tr>';
						foreach($crbdv as $crk=>$crv){
						 echo '<td>'.$this->Form->input('ClassRoomBlock.Selected.'.$crk,array('label'=>$crv,'type'=>'checkbox', 'value'=>$crk)).'</td>';
						}
						echo '</tr>';
						//echo '</tr></table></td></tr>';
					}
				}
					echo '<tr><td colspan="2">'.$this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue')).'</td></tr>';
					echo '</table>';
				}
				if(isset($fromadd_already_assign_class_rooms)) {
					?><div class="smallheading">Already Assign Class Rooms For This Program Type</div>
					<table style='border: #CCC solid 1px'>
					<tr><th style='border-right: #CCC solid 1px'>No.</th><th style='border-right: #CCC solid 1px'>Room
						</th><th style='border-right: #CCC solid 1px'>Block</th><th style='border-right: #CCC solid 1px'>Campus</th><th style='border-right: #CCC solid 1px'>Action</th></tr>
					<?php
					$count = 1;
					foreach($fromadd_already_assign_class_rooms as $aacrk=>$aacrv){
						echo "<tr><td style='border-right: #CCC solid 1px'>".$count++. "</td><td style='border-right: #CCC solid 1px'>".
							$aacrv['ClassRoom']['room_code']."</td><td style='border-right: #CCC solid 1px'>".
							$aacrv['ClassRoom']['ClassRoomBlock']['block_code']."</td><td style='border-right: #CCC solid 1px'>".
							$aacrv['ClassRoom']['ClassRoomBlock']['Campus']['name'].
						"</td><td style='border-right: #CCC solid 1px'>".
						$this->Html->link(__('Delete'), array('action' => 'delete_assign_program_program_type', $aacrv['ProgramProgramTypeClassRoom']['id']),null, sprintf(__('Are you sure you want to delete  %s?'), $aacrv['ClassRoom']['room_code'])).
						"</td></tr>";
					}
					?></table><?php
				}
			//}
		?>
		</div></td></tr>
		
	</table>
<?php //echo $this->Form->end(__('Submit'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
