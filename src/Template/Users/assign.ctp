<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-users" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Assign Responsibility'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <?= $this->Form->create('User'); ?>
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <blockquote class="fs16">
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <ol class="fs15" style="padding-top:0px; margin-top:0px">
                        <li> Assignment to department will make the user to view and manage only those students in the assigned department. </li>
                        <li> Assignment to college will make the user to view and manage only those students assigned to college but without department(Pre-Engineering or Freshman) </li>
                    </ol>
                </blockquote>
                <hr>
            </div>
        </div>

        <?php
        if (empty($this->data['StaffAssigne']['college_id']) && empty($this->data['StaffAssigne']['department_id'])) { ?>
            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Start by choosing either department level or college level permissions are going to be assigned<?= (isset($basic_data['Staff'][0]['full_name']) ? ' for '. $basic_data['Staff'][0]['full_name'] . '  (' . $basic_data['User']['username'] . ')' : ''); ?></div>
            <?php
        } ?>

        <fieldset>
            <legend style="color: #4d4d4d;"> &nbsp; <?= isset($basic_data['Staff'][0]['full_name']) ? $basic_data['Staff'][0]['full_name'] . '  ' : $basic_data['User']['username'] . '  '; ?> &nbsp; </legend>
            <div class="row">
                <div class="large-6 columns" style="color: #4d4d4d;">
                    <span style="font-weight: bold;">
                        <?php
                        if (isset($basic_data['User']['username'])) {
                            echo "Username: " . $basic_data['User']['username'] . '<br/>';
                        }
                        if (isset($basic_data['Role']['name'])) {
                            echo "Role: " . $basic_data['Role']['name'] . '<br/>';
                        }
                        if (isset($basic_data['Staff'][0]['email'])) {
                            echo "Email: " . (!empty($basic_data['Staff'][0]['email']) ? $basic_data['Staff'][0]['email'] : '---') . '<br/>';
                        } ?>
                    </span>
                </div>

                <div class="large-6 columns">
                    <div class="row">
                        <div class="large-12 columns">
                            <b>Assignment Level: </b><br><br>
                            <span>
                                <?php
                                echo $this->Form->hidden('id', array('value' => $id));
                                echo $this->Form->hidden('StaffAssigne.id');

                                if (isset($collegelevel)) {
                                    echo $this->Form->input('StaffAssigne.collegelevel', array('id' => 'college', 'label' => 'Assign to college', 'type' => 'checkbox', 'checked' => 'checked'));
                                } else {
                                    echo $this->Form->input('StaffAssigne.collegelevel', array('id' => 'college', 'label' => 'Assign to college', 'type' => 'checkbox'));
                                }

                                if (isset($departmentlevel)) {
                                    echo $this->Form->input('StaffAssigne.departmentlevel', array('id' => 'department', 'label' => 'Assign to department', 'type' => 'checkbox', 'checked' => 'checked'));
                                } else {
                                    echo $this->Form->input('StaffAssigne.departmentlevel', array('id' => 'department', 'label' => 'Assign to department', 'type' => 'checkbox'));
                                } ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <hr>
                    <b>Program: </b><br><br>
                    <?= $this->Form->input('StaffAssigne.program_id', array('label' => false, 'type' => 'select', 'multiple' => 'checkbox')); ?>
                </div>
                <div class="large-6 columns">
                    <hr>
                    <b>Program Type:</b><br><br>
                    <?= $this->Form->input('StaffAssigne.program_type_id', array('label' => false, 'type' => 'select', 'multiple' => 'checkbox')); ?>
                </div>
            </div>
        </fieldset>

        <div class="row">
            <div class="large-12 columns">
                <div id="collegeshow">
                    <hr><br>
                    <?php

                    $options = array();
                    $ccc[] = $this->data['StaffAssigne']['college_id'];

                    if (!empty($colleges)) {
                        foreach ($colleges as $value => $label) {
                            $options[] = array(
                                'name' => $label,
                                'value' => $value,
                                'selected' => (isset($this->data['StaffAssigne']['college_id']) && in_array($value, $ccc)) ? true : false,
                                'div' => false,
                                //'onClick' => 'showIncludePre(this.id)',
                                'class' => 'checkboxcoll',
                            );
                        }
                    } ?>

                    <table cellpadding="0" cellspacing="0" class="table">
                        <tr>
                            <td>
                                <h6>Check college(s) that the user is responsible</h6>
                            </td>
                        </tr>
                        <tr>
                            <td> <?= $this->Form->checkbox("SelectAllColl", array('id' => 'select-all-coll', 'checked' => '')); ?> <label>Select/ Unselect All</label></td>
                        </tr>
                        <tr>
                            <td> <?= $this->Form->input('StaffAssigne.college_id', array('multiple' => 'checkbox', 'label' => false, 'options' => $options)); ?> </td>
                        </tr>
                    </table>
                    <br>

                    <table cellpadding="0" cellspacing="0" class='onlyPre table' style='display:"<?= isset($this->data['StaffAssigne']['collegepermission']) ? 'block' : 'none'; ?> "'>
                        <tr><td> Only responsible for Pre/Freshman (Department unassigned) </td></tr>
                        <tr><td><?= $this->Form->input('StaffAssigne.collegepermission', array('label' => ' Only Pre/Freshman')); ?></td></tr>
                    </table>

                </div>

                <div id="departmentshow">
                    <hr><br>
                    <table cellspacing="0" cellpading="0" class="table table-borderless fs16" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td><h6> Check department(s) that the user is responbile</h6></td>
                            </tr>
                            <tr>
                                <td><?= $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?> <label> Select/ Unselect All </label> </td>
                            </tr>
                            <tr>
                                <td style="background: #fff;">
                                    <?php
                                    if (!empty($colleges)) {
                                        foreach ($colleges as $college_id => $college_name) {
                                            if (isset($college_department[$college_id]) && count($college_department[$college_id]) > 0) { ?>
                                                <fieldset style="width: 100%; padding: 10px;">
                                                    <legend> &nbsp;&nbsp; <?= $college_name; ?> &nbsp;&nbsp; </legend>
                                                    <table cellspacing="0" cellpading="0" class="table">
                                                        <tbody>
                                                            <?php
                                                            if (!empty($college_department[$college_id])) {
                                                                $i = 0;
                                                                foreach ($college_department[$college_id] as $department_id => $department_name) {
                                                                    if (isset($this->data['StaffAssigne']['department_id']) && !empty($this->data['StaffAssigne']['department_id']) && in_array($department_id, $this->data['StaffAssigne']['department_id'])) { ?>
                                                                        <tr>
                                                                            <td <?= ($i++ % 2 == 0 ? 'style="background: #fff;"' : 'style="background: #f5f5f5;"'); ?>><input type="checkbox" class="checkbox1" checked="checked" name="data[StaffAssigne][department_id][]" value='<?= $department_id ?>' id="StaffAssigneDepartmentId<?= $department_id ?>"> <label for="StaffAssigneDepartmentId<?= $department_id ?>"> <?= $department_name; ?> </label></td>
                                                                        </tr>
                                                                        <?php
                                                                    } else { ?>
                                                                        <tr>
                                                                            <td <?= ($i++ % 2 == 0 ? 'style="background: #fff;"' : 'style="background: #f5f5f5;"'); ?>><input type="checkbox" class="checkbox1" name="data[StaffAssigne][department_id][]" value='<?= $department_id ?>' id="StaffAssigneDepartmentId<?= $department_id ?>"> <label for="StaffAssigneDepartmentId<?= $department_id ?>"> <?= $department_name; ?> </label></td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </fieldset>
                                                <?php
                                            }
                                        } 
                                    } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="large-12 columns" id="assignShow">
                        <hr>
                        <?php //echo $this->Form->end(array('label' => __('Assign'), 'class' => 'tiny radius button bg-blue')); ?>
                        <?= $this->Form->end(array('label' => 'Assign Responsibility', 'name' => 'assignResponsibility', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function showIncludePre(id) {
        if ($("#" + id).is(":checked") && $(".onlyPre").css("display") == 'none') {
            $(".onlyPre").css("display", "block");
        } else {
            $(".onlyPre").css("display", "none");
        }
    }

    $(document).ready(function() {
        /************Others College Checkbox*******************/
        if ($("#department").is(":checked")) {
            $("#departmentshow").css("display", "block");
        } else {
            $("#departmentshow").css("display", "none");
        }

        if ($("#college").is(":checked")) {
            $("#collegeshow").css("display", "block");
        } else {
            $("#collegeshow").css("display", "none");
        }

        if ($("#college").is(":checked")) {
            $("#assignShow").css("display", "block");
        } else if ($("#department").is(":checked")) {
            $("#assignShow").css("display", "block");
        } else {
            $("#assignShow").css("display", "none");
        }

        // Add onclick handler to checkbox w/id checkme
        // Department Level Assignment

        $("#department").click(function() {
            // If checked
            if ($("#department").is(":checked")) {
                //show the hidden div
                $("#departmentshow").show("fast");
                $("#collegeshow").hide("fast");
                
                $("#assignShow").show("fast");

                if ($("#college").is(":checked")) {
                    //alert('dsfsd');
                    $('#college').attr('checked', false);
                }
            } else {
                //otherwise, hide it 
                $("#departmentshow").hide("fast");
                $("#assignShow").hide("fast");
            }
        });

        // College Level Assignment
        $("#college").click(function() {
            // If checked
            if ($("#college").is(":checked")) {
                //show the hidden div
                $("#departmentshow").hide("fast");
                $("#collegeshow").show("fast");

                $("#assignShow").show("fast");

                if ($("#department").is(":checked")) {
                    //alert('dsfsd');
                    $('#department').attr('checked', false);
                }
            } else {
                //otherwise, hide it 
                $("#collegeshow").hide("fast");
                $("#assignShow").hide("fast");
            }
        });
        
        $('#select-all-coll').click(function(event) {
            if (this.checked) {
                $('.checkboxcoll').each(function() {
                    this.checked = true;
                });
            } else {
                $('.checkboxcoll').each(function() {
                    this.checked = false;
                });
            }
        });

        $('.checkboxcoll').click(function(event) {
            if (!this.checked) {
                $('#select-all-coll').attr('checked', false);
            }
        });
    });
</script>