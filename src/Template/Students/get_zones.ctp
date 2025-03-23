<?php
if (!empty($zones) && count($zones) > 0) { ?>
    <option value="">[ Select Zone ]</option>
    <?php
    foreach($zones as $zoneId => $zoneName){
        echo '<option value="'.$zoneId.'">'.$zoneName.'</option>';
    } 
} else { ?>
    <option value="">[ No Zone Found ]</option>
    <?php
}