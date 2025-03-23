<?php
if (!empty($graduateLists)) {
    
    $line = $graduateLists[0]['GraduateList'];
    $this->CSV->addRow(array_keys($line));

    foreach ($graduateLists as $graduateList) {
        $line = $graduateList['GraduateList'];
        $this->CSV->addRow($line);
    }
    echo  $this->CSV->render($filename);
}

