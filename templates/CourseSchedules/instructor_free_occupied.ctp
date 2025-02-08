<?php
if (isset($instructors) && $instructors==1) {
        echo '<strong class="rejected">Instructor occupied.</strong>';
} else if (isset($instructors) && $instructors==2) {
      echo '<strong class="accepted">Instructor Free.</strong>';
}

?>
