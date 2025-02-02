<?php
$options = "";
if (isset($units) && !empty($units)) {
    $options .= "<option value=''>[ Select Unit/All ]</option>";
    foreach ($units as $unit_id => $unit_name) {
        $options .= "<option value=\"" . $unit_id . "\">" . $unit_name . "</option>";
    }
}

if (count($units) == 0) {
    $options = "<option value=\"\">[ No Units Found ]</option>";
}
echo $options;
