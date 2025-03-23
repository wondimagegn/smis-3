<?php
# /app/views/preferences/get_preference.ctp
?>

<?php
foreach($remaining_departments as $departmentid=>$departmentname){
echo '
<option value="'.$departmentid.'">'.$departmentname.'</option>'."\n";
}
?>
