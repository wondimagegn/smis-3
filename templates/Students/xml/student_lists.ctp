<root>
<students>
	<?php echo $xml->serialize($students) ?>
</students>
<colleges>
	<?php echo $xml->serialize($colleges) ?>
</colleges>
<departments>
	<?php echo $xml->serialize($departments) ?>
</departments>
<sections>
    <?php echo $xml->serialize($sections);?>
</sections>
</root>
