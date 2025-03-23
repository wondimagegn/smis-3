<div class="users form">
    <?php echo $this->Form->create('User'); ?>
    <div class="headerfont"><?php echo __('Assign Responsibility'); ?></div>
    <div class="basic_data">
        <?php
        if (isset($basic_data['Staff'][0]['full_name'])) {
            echo "Name:" . $basic_data['Staff'][0]['full_name'] . '<br>';
        }
        if (isset($basic_data['Staff'][0]['email'])) {
            echo "Email: " . $basic_data['Staff'][0]['email'] . '<br/>';
        }
        if (isset($basic_data['User']['username'])) {
            echo "Username: " . $basic_data['Staff'][0]['full_name'] . '<br/>';
        }
        if (isset($basic_data['Role']['name'])) {
            echo "Role: " . $basic_data['Role']['name'] . '<br/>';
        }
        ?>
    </div>
    <table class="fs13 small_padding">
        <tr>
            <td>
                <?php
                echo $this->Form->hidden('id', array('value' => $id));
                echo $this->Form->hidden('StaffAssigne.id');
                if (isset($collegelevel)) {
                    echo $this->Form->input('StaffAssigne.collegelevel', array('id' => 'college', 'label' => 'Assign to college', 'type' => 'checkbox', 'checked' => 'checked'));
                } else {
                    echo $this->Form->input('StaffAssigne.collegelevel', array('id' => 'college', 'label' => 'Assign to college', 'type' => 'checkbox'));
                }
                ?>
            </td>
            <td>
                <?php
                if (isset($departmentlevel)) { //departmentlevel
                    echo $this->Form->input('StaffAssigne.departmentlevel', array('id' => 'department', 'label' => 'Assign to department', 'type' => 'checkbox', 'checked' => 'checked'));
                } else {
                    echo $this->Form->input('StaffAssigne.departmentlevel', array('id' => 'department', 'label' => 'Assign to department', 'type' => 'checkbox'));
                }
                ?>
            </td>
        </tr>
    </table>
    <div id="collegeshow">
        <?php
        echo $this->Form->input('StaffAssigne.program_id');
        echo "<table><tr><td>Check the college the user is responsible for</td></tr><tr><td>" . $this->Form->input('StaffAssigne.college_id', array('multiple' => 'checkbox', 'value' => isset($college_id) ? $college_id : '', 'selected' => isset($this->request->data['StaffAssigne']['college_id']) ? $this->request->data['StaffAssigne']['college_id'] : '', 'div' => false)) . "</td></tr></table>";
        ?>
    </div>
    <div id="departmentshow">
        <?php
        echo "<table>";
        echo $this->Form->input('StaffAssigne.program_id');
        foreach ($colleges as $college_id => $college_name) {
            if (isset($college_department[$college_id]) && count($college_department[$college_id]) > 0) {
                echo "<tr><td><div class='smallheading'>Check the departments the user is responbile  </div></td></tr>";
                echo "<tr><td ><div class='smallheading'>" . $college_name . '</div>&nbsp;&nbsp;&nbsp;';
                echo "<table><tbody>";
                if (!empty($college_department[$college_id])) {
                    foreach ($college_department[$college_id] as $department_id => $department_name) {
                        if (isset($this->request->data['StaffAssigne']['department_id']) && !empty($this->request->data['StaffAssigne']['department_id']) && in_array($department_id, $this->request->data['StaffAssigne']['department_id'])) {
                            //checked=in_array($department_id,$this->request->data['Staff'][0]['assigned_to'] ? 'checked':'unchecked'.'
                            echo '<tr><td ><input type="checkbox" checked="checked" name="data[StaffAssigne][department_id][]" value=' . $department_id . ' id="StaffAssigneDepartmentId' . $department_id . '">' . $department_name . '</td></tr>';
                        } else {
                            // echo '<tr><td ><input type="checkbox"  name="data["StaffAssigne"]["department_id"][]" value='.$department_id.' id="StaffAssigneDepartmentId'.$department_id.'">'.$department_name.'</td></tr>';
                            // debug(in_array($department_id,$this->request->data['Staff'][0]['assigned_to']));
                            //debug($this->request->data);
                            echo '<tr><td ><input type="checkbox"  name="data[StaffAssigne][department_id][]" value=' . $department_id . ' id="StaffAssigneDepartmentId' . $department_id . '">' . $department_name . '</td></tr>';
                        }
                    }
                }
                echo "</tbody></table></td></tr>";
            }
        }
        echo "</table>";
        ?>
    </div>
    <?= $this->Form->end(__('Assign')); ?>
</div>

<script type="text/javascript">
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
        // Add onclick handler to checkbox w/id checkme
        /**
         *Department Level Assignment
         */
        $("#department").click(function() {
            // If checked
            if ($("#department").is(":checked")) {
                //show the hidden div
                $("#departmentshow").show("fast");
                $("#collegeshow").hide("fast");

                if ($("#college").is(":checked")) {
                    //alert('dsfsd');
                    $('#college').attr('checked', false);
                }
            } else {
                //otherwise, hide it 
                $("#departmentshow").hide("fast");

            }
        });
        /**
         *College Level Assignment
         */
        $("#college").click(function() {
            // If checked
            if ($("#college").is(":checked")) {
                //show the hidden div
                $("#departmentshow").hide("fast");
                $("#collegeshow").show("fast");
                if ($("#department").is(":checked")) {
                    //alert('dsfsd');
                    $('#department').attr('checked', false);
                }
            } else {
                //otherwise, hide it 
                $("#collegeshow").hide("fast");

            }
        });
    });
</script>