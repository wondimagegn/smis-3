<?php
$options = "";
if (isset($studyPrograms) && !empty($studyPrograms)) {
    $options .= "<option value=''>[ Select Study Program ]</option>";
    foreach ($studyPrograms as $study_program_id => $study_program_name) {
        $options .= "<option value=\"" . $study_program_id . "\">" . $study_program_name . "</option>";
    }
}

if (count($studyPrograms) == 0) {
    $options = "<option value=''>[ No Study Program Found]</option>";
}
echo $options;