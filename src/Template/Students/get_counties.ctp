<?php
if (!empty($countries) && count($countries) > 0) { ?>
    <option value="">[ Select Country ]</option>
    <?php
    foreach($countries as $countryId => $countryName){
        echo '<option value="'.$countryId.'">'.$countryName.'</option>';
    } 
} else { ?>
    <option value="">[ No Country Found]</option>
    <?php
}