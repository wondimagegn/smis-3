<?php
$options = "";
if (isset($colleges) && !empty($colleges)) {
    $options .= "<option value=0>--Select College--</option>";
    foreach ($colleges as $college_id => $collegeName) {
        $options .= "<option value=\"" . $college_id . "\">" . $collegeName . "</option>";
    }
}

if (count($colleges) == 0){
    $options = "<option value=\"\"></option>";
}
echo $options;
