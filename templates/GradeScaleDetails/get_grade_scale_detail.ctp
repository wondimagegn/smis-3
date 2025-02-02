<?php 
if (isset($gradeScaleDetails) && !empty($gradeScaleDetails)) {
    echo "<table>";
    echo "<tr><th>Result From</th><th>Result To </th><th>Grade</th></tr>";
    foreach($gradeScaleDetails as $kk=>$vv) {
           echo "<tr><td>".$vv['GradeScaleDetail']['minimum_result']."</td><td>".$vv['GradeScaleDetail']['maximum_result']."</td><td>".$vv['Grade']['grade']."</td></tr>";
    }
    echo "</table>";
} else {
    echo "<div class='smallheading'> There is no grade scale detail define for the selected scale.</div>";
}
?>

