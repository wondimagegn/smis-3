<?php 
if (isset($result_count)) {
      if (!empty($from) && !empty($to)) {
             echo "<strong> Total students from $from  to $to is ".$result_count."</strong>";
      } else {
            echo "<strong>".$result_count."</strong>";
      }
  }
  
?>

