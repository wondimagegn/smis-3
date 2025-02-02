<script language="javascript">

    var totalRow = <?= (!empty($this->request->data) ? (count($this->request->data['PlacementResultSetting'])) : (!empty($types) ? (count($types)) : 2)); ?>;

    function updateSequence(tableID) {
        var s_count = 1;
        for (i = 1; i < document.getElementById(tableID).rows.length; i++) {
            document.getElementById(tableID).rows[i].cells[0].childNodes[0].data = s_count++;
        }
    }

    function addRow(tableID, model, no_of_fields, all_fields) {
        var elementArray = all_fields.split(',');
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);
        totalRow++;
        row.id = model + '_' + totalRow;

        if (table.rows.length <= <?= (count($types) + 1 ); ?>) {

            var cell0 = row.insertCell(0);
            cell0.innerHTML = rowCount;
            cell0.classList.add("center");

            //construct the other cells
            for (var j = 1; j <= no_of_fields; j++) {
                var cell = row.insertCell(j);
                if (elementArray[j - 1] == 'percent') {
                    var element = document.createElement("input");
                    //element.size = "4";
                    element.type = "number";
                    element.required = "required";
                    element.min = "1";
                    element.max = "70";
                    element.style = "width:90%;";
                    cell.classList.add("center");
                    element.id = "PlacementPercent_" + rowCount;
                } else if (elementArray[j - 1] == 'max_result') {
                    var element = document.createElement("input");
                    //element.size = "4";
                    element.type = "number";
                    element.required = "required";
                    element.min = "1";
                    element.max = "700";
                    element.style = "width:90%;";
                    cell.classList.add("center");
                    element.id = "PlacementMaxResult_" + rowCount;
                } else if (elementArray[j - 1] == "result_type") {
                    var element = document.createElement("select");
                    string = "";
                    string += '<option value="freshman_result"> Freshman Result</option>';
                    string += '<option value="EHEECE_total_results"> Preparatory Exam Result</option>';
                    string += '<option value="entrance_result"> Entrance Exam Result For the Field</option>';
                    element.id = "PlacementType_" + rowCount;
                    element.innerHTML = string;
                    element.required = "required";
                    cell.classList.add("vcenter");
                    element.style = "width:90%";
                } else if (elementArray[j - 1] == "edit") {
                    var element = document.createElement("a");
                    element.innerText = "Delete";
                    element.textContent = "Delete";
                    element.setAttribute('href', 'javascript:deleteSpecificRow(\'' + model + '_' + totalRow + '\')');
                    cell.classList.add("vcenter");
                    element.style = "width:90%";
                }

                element.name = "data[" + model + "][" + rowCount + "][" + elementArray[j - 1] + "]";
                cell.appendChild(element);
            }
        }

        updateSequence(tableID);
    }

    function deleteRow(tableID) {
        try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            if (rowCount > 2) {
                table.deleteRow(rowCount - 1);
                updateSequence(tableID);
            } else {
                alert('No more rows to delete');
            }
        } catch (e) {
            alert(e);
        }
    }

    function deleteSpecificRow(id) {
        try {
            var row = document.getElementById(id);
            //var table = row.parentElement;
            var table = row.parentNode;
            if (table.rows.length > 2) {
                row.parentNode.removeChild(row);
                updateSequence('participant_setup');
                //row.parentElement.removeChild(row);
            } else {
                alert('There must be at least one participant type.');
            }
        } catch (e) {
            alert(e);
        }
    }
</script>

<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Placement Result Settings.'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div class="examTypes form">
                    <?= $this->Form->create('PlacementSetting', array('onSubmit' => 'return checkForm(this);')); ?>

                    <div style="margin-top: -30px;">
                        <hr>
                        <fieldset style="padding-bottom: 5px;">
                            <legend>&nbsp;&nbsp; Placement Participant College/Department &nbsp;&nbsp;</legend>
                            <div class="row">
                                <div class="large-3 columns">
                                    <?php echo $this->Form->input('PlacementResultSetting.1.academic_year', array('onchange' => 'updateDetails()', 'id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $availableAcademicYears, 'default' => (isset($this->request->data['PlacementResultSetting'][1]['academic_year']) ? $this->request->data['PlacementResultSetting'][1]['academic_year'] : (isset($latestACYRoundAppliedFor) ? $latestACYRoundAppliedFor['academic_year'] : '')))); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PlacementResultSetting.1.round', array('onchange' => 'updateDetails()', 'id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:80%;', 'type' => 'select', 'options' => Configure::read('placement_rounds'), 'default' => (isset($this->request->data['PlacementResultSetting'][1]['round']) ? $this->request->data['PlacementResultSetting'][1]['round'] : (isset($latestACYRoundAppliedFor) ? $latestACYRoundAppliedFor['round'] : '')))); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PlacementResultSetting.1.program_id', array('onchange' => 'updateDetails()', 'id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PlacementResultSetting.1.program_type_id', array('onchange' => 'updateDetails()', 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programTypes)); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('PlacementResultSetting.1.applied_for', array('onchange' => 'updateDetails()', 'options' => $appliedForList /* $allUnits */, 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied for those students currently in:  ', 'required', 'empty' => '[ Select to be Applied Unit ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-6 columns">
                                    &nbsp;
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    
                    <div style="overflow-x:auto;">
                        <table cellspacing="0" cellpadding="0" id="participant_setup" style="margin-bottom:5px" class="table">
                            <thead>
                                <tr>
                                    <th style="width:5%;" class="center">#</th>
                                    <th style="width:35%;" class="vcenter">Type</th>
                                    <th style="width:10%;" class="center">Percent</th>
                                    <th style="width:10%;" class="center">Max Result</th>
                                    <th class="vcenter">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($this->request->data)) { ?>
                                    <tr id="PlacementResultSettingType_1">
                                        <td class="center">1</td>
                                        <td class="vcenter">
                                            <?=  $this->Form->input('PlacementResultSetting.1.result_type', array('label' => false, 'id' => 'PlacementType_1', 'options' => $types, 'style' => 'width:90%;')); ?>
                                        </td>
                                        <td class="center">
                                            <?= $this->Form->input('PlacementResultSetting.1.percent', array('label' => false, 'id' => 'PlacementPercent_1', 'type' => "number", 'min' => "0", 'max' => "100", 'step' => "1",  'style' => 'width:90%;')); ?>
                                        </td>
                                        <td class="center">
                                            <?= $this->Form->input('PlacementResultSetting.1.max_result', array('label' => false, 'id' => 'PlacementMaxResult_1', 'type' => "number", 'min' => "0", 'max' => "100", 'step' => "1",  'style' => 'width:90%')); ?>
                                        </td>
                                        <td class="vcenter"><a href="javascript:deleteSpecificRow('PlacementResultSettingType_1')">Delete</a></td>
                                    </tr>
                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($this->request->data['PlacementResultSetting'] as $key => $placementType) {
                                        if (!$lockEditing) { ?>
                                            <tr id="PlacementResultSettingType_<?= $count; ?>"  class="center">
                                                <td class="center"><?= ($count); ?></td>
                                                <td class="vcenter">
                                                    <?= (isset($placementType['id']) ? $this->Form->input('PlacementResultSetting.' . $key . '.id', array('type' => 'hidden')) : ''); ?>
                                                    <?= (isset($placementType['group_identifier']) ? $this->Form->input('PlacementResultSetting.' . $key . '.group_identifier', array('type' => 'hidden')) : ''); ?>
                                                    <?= $this->Form->input('PlacementResultSetting.' . $key . '.result_type', array('label' => false, 'options' => $types, 'style' => 'width:90%;')); ?>
                                                </td>
                                                <td class="center">
                                                    <?php
                                                    if ($placementType['result_type'] == "freshman_result") {
                                                        echo $this->Form->input('PlacementResultSetting.' . $key . '.percent', array('label' => false, 'type' => "number", 'min' => "0", 'max' => 70, 'step' => "1",  'style' => 'width:90%;')); 
                                                    } else if ($placementType['result_type'] == "entrance_result") {
                                                        echo $this->Form->input('PlacementResultSetting.' . $key . '.percent', array('label' => false, 'type' => "number", 'min' => "0", 'max' => 30, 'step' => "1",  'style' => 'width:90%;')); 
                                                    } else if ($placementType['result_type'] == "EHEECE_total_results") {
                                                        echo $this->Form->input('PlacementResultSetting.' . $key . '.percent', array('label' => false, 'type' => "number", 'min' => "0", 'max' => 30, 'step' => "1",  'style' => 'width:90%;')); 
                                                    } ?>
                                                </td>
                                                <td class="center">
                                                    <?php
                                                    if ($placementType['result_type'] == "freshman_result") {
                                                        echo $this->Form->input('PlacementResultSetting.' . $key . '.max_result', array('label' => false, 'type' => "number", 'min' => "0", 'max' => PREPARATORYMAXIMUM, 'step' => "1",  'style' => 'width:90%;')); 
                                                    } else if ($placementType['result_type'] == "entrance_result") {
                                                        echo $this->Form->input('PlacementResultSetting.' . $key . '.max_result', array('label' => false, 'type' => "number", 'min' => "0", 'max' => ENTRANCEMAXIMUM, 'step' => "1",  'style' => 'width:90%;')); 
                                                    } else if ($placementType['result_type'] == "EHEECE_total_results") {
                                                        echo $this->Form->input('PlacementResultSetting.' . $key . '.max_result', array('label' => false, 'type' => "number", 'min' => "0", 'max' => PREPARATORYMAXIMUM, 'step' => "1",  'style' => 'width:90%;')); 
                                                    } ?>
                                                </td>
                                                <td class="vcenter"><a href="javascript:deleteSpecificRow('PlacementResultSettingType_<?= $count++; ?>')">Delete</a></td>
                                            </tr>
                                            <?php
                                        } else { ?>
                                            <tr id="PlacementResultSettingType_<?= $count; ?>">
                                                <td class="center"><?= ($count); ?></td>
                                                <td class="vcenter"><?= $types[$placementType['result_type']]; ?></td>
                                                <td class="center"><?= $placementType['percent']; ?></td>
                                                <td class="center"><?= $placementType['max_result']; ?></td>
                                                <td class="vcenter">&nbsp;</td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <br>

                    <?php 
                    if (!$lockEditing) { ?>
                        <p><input type="button" value="Add Row" onclick="addRow('participant_setup', 'PlacementResultSetting',4, '<?= $fieldSetups; ?>')" /> </p>
                        <?= $this->Form->submit(__('Submit'), array('name' => 'saveIt', 'id' => 'saveIt', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                        <?php 
                    } ?>

                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var identifier = 0;
        $("#AppliedFor").change(function () {
            var formUrl = '/PlacementSettings/placement_identifier';
            $.ajax({
                type: 'POST',
                url: formUrl,
                data: $('form').serialize(),
                success: function (data, textStatus, xhr) {
                    var x = JSON.parse(data);
                    if (x) {
                        window.location.replace("/placementSettings/placement_result_setting/" + x);
                    }
                },
                error: function ( xhr, textStatus, error) {

                }
            });

            //serialize form data
            /* var x = document.getElementById("UpdatedID").value;
            alert(x); */
        });
    });

    function updateDetails()  {
        var formUrl = '/PlacementSettings/placement_identifier';
        $.ajax({
            type: 'POST',
            url: formUrl,
            data: $('form').serialize(),
            success: function (data, textStatus, xhr) {
                var x = JSON.parse(data);
                if (x) {
                    window.location.replace("/placementSettings/placement_result_setting/" + x);
                }
            },
            error: function ( xhr, textStatus, error) {

            }
        });

        //serialize form data
        /* var x = document.getElementById("UpdatedID").value;
        alert(x); */
    }

    var form_being_submitted = false; /* global variable */

    var checkForm = function(form) {
        if (form_being_submitted) {
            alert("Your request is being processed, please wait a moment...");
            form.saveIt.disabled = true;
            return false;
        }
        form.saveIt.value = 'Submitting...';
        form_being_submitted = true;
        return true; /* submit form */
    };
    // prevent possible form resubmission of a form 
    // and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>