<!doctype html>
<html class="no-js" lang="en">

<head>
	<!-- META CHARS -->
	<?= $this->Html->charset(); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!-- PAGE TITLE -->
	<title><?= Configure::read('ApplicationShortName'); ?> <?= Configure::read('ApplicationVersionShort'); ?></title>
	<link rel="stylesheet" type="text/css" href="/css/es.css" media="screen">
	<link rel="stylesheet" type="text/css" href="/css/ep.css" media="print">
	<!--[if lt IE 7]>			
	<link rel="stylesheet" media="screen" href="/styles/ielt7.css" type="text/css"/>
	<![endif]-->
	<script type="text/javascript" src="/js/es.js"></script>

</head>

<body id="e404">
	<div id="root">
		<?= $this->fetch('content'); ?>
	</div>
</body>

</html>