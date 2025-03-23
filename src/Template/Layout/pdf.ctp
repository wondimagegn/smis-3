<?php
$this->response = $this->response->withType('pdf');
?>
<?= $this->fetch('content'); ?>
