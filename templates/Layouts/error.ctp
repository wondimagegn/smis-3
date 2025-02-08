<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?= $this->Html->charset(); ?>
	<title> 404 Error Page</title>

	<link rel="stylesheet" type="text/css" href="/css/reset.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/datePicker.css" media="screen" />

	<link rel="stylesheet" type="text/css" href="/css/common1.css" media="screen" />

	<link rel="stylesheet" type="text/css" href="/css/text.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/960.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/layout.css" media="screen" />

	<link rel="stylesheet" type="text/css" href="/css/nav.css" media="screen" />
	<!--<link rel="stylesheet" type="text/css" href="/css/style.css" media="screen" /> -->

	<!--[if IE 6]><link rel="stylesheet" type="text/css" href="/css/ie6.css" media="screen" /><![endif]-->
	<!--[if IE 7]><link rel="stylesheet" type="text/css" href="/css/ie.css" media="screen" /><![endif]-->

</head>

<body>

	<div class="container_16">

		<div class="clear"></div>

		<div id="ajax_div" class="grid_16" style='text-align:center'>
			<?= $this->Session->flash(); ?>
			<?= $this->fetch('content'); ?>
		</div>

		<div class="clear"></div>
		<div class="clear"></div>

		<div class="grid_16" id="site_info">
			<div class="footerbox">
				<p style="margin:0px; padding:0px"><strong>&copy; <?= (date("Y")); ?> Arba Minch University<br /></strong>Designed and Developed By IT and T Solutions PLC <a href="http://www.merebtechnologies.com" style="color:#ebad05">itandts.com</a> </p>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</body>

</html>