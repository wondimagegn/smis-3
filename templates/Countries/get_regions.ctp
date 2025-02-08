<option>Select Region</option>
<?php
foreach($regions as $regionId=>$regionName){
echo '
<option value="'.$regionId.'">'.$regionName.'</option>'."\n";
}
?>
