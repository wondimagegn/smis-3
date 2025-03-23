<div id="flashMessage" class='<?php echo $class ?>'>
<?php

echo $message;
echo $this->Html->link($link_text, $link_url, array("escape" => false));
?>
</div>
