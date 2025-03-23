<?php
	if (strcasecmp('Valid', $value)==0) {
		echo '<span style="color:green">'.$value.'</span>';
	}else{
		echo '<span style="color:red">'.$value.'</span>';
	}
?>