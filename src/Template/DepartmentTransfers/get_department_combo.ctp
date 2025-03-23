<?php
$options = "";
if (!empty($departments)) {
    $options .= "<option value=''>[ Select Department ]</option>";
    foreach ($departments as $department_id => $departmentName) {
        $options .= "<option value=\"" . $department_id . "\">" . $departmentName . "</option>";
    }
}

if (count($departments) == 0) {
    $options = "<option value=''>[ No Department Found ]</option>";
}
echo $options;
