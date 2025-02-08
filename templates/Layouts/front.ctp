<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?= $html->charset(); ?>
	<?=  $scripts_for_layout; ?>
	<?= ($this->Html->css('default')); ?>
	<link rel="stylesheet" type="text/css" href="/css/common1.css" media="screen" />

</head>

<body>
	<!-- <div id="container">
		<div id="header">
			
		</div>
		<div id="content">
			<?= $content_for_layout; ?>
		</div>
		<div id="footer">
			
		</div>
	</div> -->
	<div id="header">
		<div id="logo">
			<h1>Student Management Information System </h1>
			<p>Arba Minch University</p>
		</div>
	</div>
	<div id="page">
		<div id="content">
			<?= $content_for_layout; ?>
		</div>
	</div>
	<div class="clear"></div>
	<div id="footer">
		<?= __('This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.') ?>
	</div>
</body>

</html>