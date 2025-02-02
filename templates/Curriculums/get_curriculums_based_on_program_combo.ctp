<?php
if (isset($curriculums) && !empty($curriculums)) {
    $options = '';
    $options .= "<option value=''>[ Select Curriculum ]</option>";
    foreach ($curriculums as $curriculum_id => $curriculum_name) {
        $options .= "<option value=\"" . $curriculum_id . "\">" . $curriculum_name . "</option>";
    }
}

if (count($curriculums) == 0) {
    $options = "<option value=''>[ No Active Curriculum Found ]</option>";
}
echo $options;
