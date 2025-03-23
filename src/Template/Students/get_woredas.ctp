<?php
if (!empty($woredas) && count($woredas) > 0) { ?>
    <option value="">[ Select Woreda ]</option>
    <?php
    foreach($woredas as $woredaId => $woredaName){
        echo '<option value="'.$woredaId.'">'.$woredaName.'</option>';
    } 
} else { ?>
    <option value="">[ No Woreda Found ]</option>
    <?php
}