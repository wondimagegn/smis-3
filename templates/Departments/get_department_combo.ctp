<?php
$options = "";
if (isset($departments) && !empty($departments)) {
    $options .= "<option value=0>[ Select Department ]</option>";
    if ($this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT && $this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN) {
        $options .= "<option value=-1 style='font-weight: bold'> Freshman </option>";
    }
    foreach ($departments as $department_id => $departmentName) {
        $options .= "<option value=\"" . $department_id . "\">" . $departmentName . "</option>";
    }
}

if (count($departments) == 0) {
    $options = "<option value=''>[ No Department Found ]</option>";
}
echo $options;
