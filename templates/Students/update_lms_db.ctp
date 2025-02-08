<?php echo $this->Form->create('Student', array('id' => 'update_lms_form')); ?>
<script>
function toggleViewFullId(id) {
    if ($('#' + id).css("display") ==
        'none') {
        $('#' + id + 'Img').attr("src",
            '/img/minus2.gif');
        $('#' + id + 'Txt').empty();
        $('#' + id + 'Txt').append(
            'Hide Filter');
    } else {
        $('#' + id + 'Img').attr("src",
            '/img/plus2.gif');
        $('#' + id + 'Txt').empty();
        $('#' + id + 'Txt').append(
            'Display Filter');
    }
    $('#' + id).toggle("slow");
}
</script>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">
                <div
                    onclick="toggleViewFullId('UpdateKoha')">
                    <?php
                    if (!empty($acceptedStudents)) {
                        echo $this->Html->image('plus2.gif', array('id' => 'UpdateKohaImg'));
                    ?><span
                        style="font-size:10px; vertical-align:top; font-weight:bold"
                        id="UpdateKohaTxt">Display
                        Filter</span><?php
                                        } else {
                                            echo $this->Html->image('minus2.gif', array('id' => 'UpdateKohaImg'));
                                            ?><span
                        style="font-size:10px; vertical-align:top; font-weight:bold"
                        id="UpdateKohaTxt">Hide
                        Filter</span><?php
                                        }
                                            ?>
                </div>
                <div id="UpdateKoha"
                    style="display:<?php echo (!empty($acceptedStudents) ? 'none' : 'display'); ?>">
                    <div
                        class="smallheading">
                        Please select
                        the academic
                        year, program
                        and program
                        type, you want
                        to update LMS
                        database .</div>

                    <table
                        cellspacing="0"
                        cellpadding="0"
                        class="fs14">
                        <tr>
                            <td> Academic
                                Year:
                            </td>
                            <td><?php echo $this->Form->input('Search.academicyear', array(
                                    'id' => 'academicyear',
                                    'label' => false, 'type' => 'select', 'options' => $acyear_array_data,
                                    'empty' => "--Select Academic Year--", 'selected' => isset($defaultacademicyear) ? $defaultacademicyear : ''
                                )); ?>
                            </td>
                            <td>Semester:
                            </td>
                            <td><?php echo $this->Form->input(
                                    'Search.semester',
                                    array('label' => false, 'options' => array(
                                        'I' => 'I', 'II' => 'II',
                                        'III' => 'III'
                                    ), 'empty' => '--select semester--')
                                ); ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Program:
                            </td>
                            <td><?php echo $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?>
                            </td>
                            <td>Program
                                Type:
                            </td>
                            <td><?php echo $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programTypes, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?>
                            </td>
                        </tr>

                        <tr>

                            <td
                                style="width:15%">
                                College:
                            </td>
                            <td
                                style="width:50%">
                                <?php
                                echo $this->Form->input('Search.college_id', array('label' => false, 'type' => 'select', 'empty' => '---Select College --'));
                                ?>
                            </td>
                        </tr>


                        <tr>
                            <td
                                colspan="3">
                                <?php echo $this->Form->Submit('Update LMS', array(
                                    'div' => false, 'id' => 'submitBtn', 'name' => 'updateLMSDB',
                                    'class' => 'tiny radius button bg-blue'
                                )); ?>
                            </td>

                            <td
                                colspan="3">
                                <?php echo $this->Form->Submit('Clean LMS Database', array(
                                    'div' => false, 'id' => 'submitBtnDelete', 'name' => 'deleteLMSDB',
                                    'class' => 'tiny radius button bg-blue'
                                )); ?>
                            </td>

                        </tr>
                    </table>
                </div>
                <div id="ProcessingLMS">

                </div>
                <?php

                echo $this->Form->end();
                ?>
            </div>
            <!-- end of columns 12 -->
        </div> <!-- end of row --->
    </div> <!-- end of box-body -->
</div><!-- end of box -->

<script>
/*
	$(window).load(
		function(){
			$(".se-pre-con").fadeOut("slow");
		}
	);
	*/
/*
$('#update_lms_form').submit(function(){
		var image = new Image();
		image.src = '/img/busy.gif';
		//$('#submitBtn').value="Processing ...";
		$('#submitBtn').attr("disabled",true);
		$('#submitBtn').attr("value","Processing...");
		$("#ProcessingLMS").empty().html('<img src="/img/busy.gif" class="displayed" >');
		return true;
});
*/
</script>