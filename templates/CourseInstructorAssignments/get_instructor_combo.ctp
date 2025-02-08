<?php
# /app/views/course_instructor_assignments/get_department.ctp

    /* $options .= "<optgroup label=\"" . $warehouse_id . "\">";
    foreach ($warehouse_name as $key => $value) {
        $options .= "<option value=\"" . $key . "\">" . $value . "</option>";
    }
    $options .= "</optgroup>"; */

    $options = null;

    if (!empty($instructors) && count($instructors) > 0) {

        foreach ($instructors as $pos => $instructor_value) {
            $options .= "<optgroup label=\"" . $pos . "\">";
            foreach ($instructor_value as $staff_id => $staff_value) {
                $options .= "<option value=\"" . $staff_id . "\">" . $staff_value . "</option>";
            }
            $options .= "</optgroup>";
        }
    }

    if (empty($options)) { ?>
        <option>No Instructor Found</option>
        <?php
    } else {
        echo $options;
    } ?>