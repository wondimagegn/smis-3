<?php
$options = '';
if (!empty($departments)) {
    $options .= '<option value="0">[ Select Department ]</option>';
    if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT &&
        $this->request->getSession()->read('Auth.User.role_id') != ROLE_SYSADMIN) {
        $options .= '<option value="-1" style="font-weight: bold"> Freshman </option>';
    }
    foreach ($departments as $department_id => $departmentName) {
        $options .= '<option value="' . h($department_id) . '">' . h($departmentName) . '</option>';
    }
} else {
    $options = '<option value="">[ No Department Found ]</option>';
}
echo $options;
