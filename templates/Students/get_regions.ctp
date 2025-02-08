<?php
# /app/views/students/get_regions.ctp
if (count($regions) > 0) { ?>
    <option value="">[ Select Region ]</option>
    <?php
    foreach ($regions as $regionId => $regionName) {
        echo '<option value="' . $regionId . '">' . $regionName . '</option>';
    } 
} else { ?>
    <option value="">[ No Regions Found ]</option>
    <?php
}
