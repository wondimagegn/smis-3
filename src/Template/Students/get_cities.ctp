<?php
if (!empty($cities) && count($cities) > 0) { ?>
    <option value="">[ Select City or Leave, if not listed ]</option>
    <?php
    foreach($cities as $cityId => $cityName){
        echo '<option value="'.$cityId.'">'.$cityName.'</option>';
    } 
} else { ?>
    <option value="">[ No City Found ]</option>
    <?php
}