<?php
if (!empty($otherAdds)) { ?>
    <hr>
    <div class='smallheading fs14 text-gray'> Select courses you want to add.</div>
    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
    <br>
    <div style="overflow-x:auto;">
        <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
            <thead>
                <tr>
                    <th class="center">#</th>
                    <th class="center">&nbsp;</th>
                    <th class="vcenter">Course Title </th>
                    <th class="center">Course Code</th>
                    <th class="center">Credit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                $button_visible = 0;
                
                foreach ($otherAdds as $pk => $pv) {
                    if ($pv['already_added'] == 0) { ?>
                        <tr>
                            <td class="center"><?= ++$count; ?></td>
                            <td class="center"><?= $this->Form->checkbox('CourseAdd.add.' . $pv['PublishedCourse']['id']); ?></td>
                            <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                            <?php
                            $button_visible++;
                        } else {
                            if (isset($pv['prerequiste_failed']) && $pv['prerequiste_failed'] == 1) { ?>
                                <tr style='color:red'>
                                    <td class="center"><?= ++$count; ?></td>
                                    <td class="center">&nbsp;</td>
                                    <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                                    <?php
                            } else { ?>
                                <tr style='color:green'>
                                    <td class="center"><?= ++$count; ?></td>
                                    <td class="center">***</td>
                                    <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                                    <?php
                            }
                        } ?>

                            <td class="center"><?= $pv['Course']['course_code']; ?></td>
                            <td class="center"><?= $pv['Course']['credit']; ?></td>
                        </tr>
                    <?php
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan=6>
                        Note: 
                        <ol style="margin-bottom: 5px;">
                            <li>*** Courses you have already registred or taken, and got pass grade, not allowed to add it.</li>
                            <li> Red marked courses failed to fullfill prerequiste.</li>
                        </ol>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <hr>
    
    <?php
    if ($button_visible > 0) {
        echo $this->Form->submit('Add Selected', array('name' => 'addSelected', 'id' => 'addSelected', 'class' => 'tiny radius button bg-blue'));
        //echo $this->Form->end('Add Selected', array('class' => 'tiny radius button bg-blue'));
    } ?>

    <script type="text/javascript">

        var form_being_submitted = false;

        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

        $('#addSelected').click(function() {
            
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

            //alert(checkedOne);
            if (!checkedOne) {
                alert('At least one course must be selected to add.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to add.';
                return false;
            }

            if (form_being_submitted) {
                alert("Processing the selected course add requests, please wait a moment...");
                $('#addSelected').attr('disabled', true);
                return false;
            }

            var confirmm = confirm('Are you sure you want to add the selected courses?');

            if (confirmm) {
                $('#addSelected').val('Processing Selected Course Adds...');
                form_being_submitted = true;
                return true;
            } else {
                return false;
            }

        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
    </script>
    <?php
} ?>
