<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('SMIS Sign In'); ?>
		<?php //echo $title_for_layout; ?>
	</title>
	<?php echo $scripts_for_layout; ?>
    <link rel="stylesheet" type="text/css" href="/css/reset.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/common1.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/text.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/960.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/css/layout.css" media="screen" />	
	<link rel="stylesheet" type="text/css" href="/css/nav.css" media="screen" />
</head>
<body>
		<div class="container_16">
			<div class="container_16">
			<?php echo $this->Html->image('AMU-SMIS-Banner.jpg', array('id' => 'AMU-SMIS-Banner')); ?>
			</div>
			<div class="clear"></div>
				<div class="prefix_3 suffix_4" style="padding-top:30px">
					<?php echo $this->Session->flash(); ?>
					<?php
					   
					   if ($session->check('Message.auth')) {
		//                     echo $session->flash('auth');
					   }
					   if ($session->check('Message.flash')) {
							 //echo $session->flash();
					   }

					 ?>
				</div>			
			<div  id="ajax_div" class="prefix_4" style="padding-bottom:10px">
						
			            <?php echo $content_for_layout; ?>
			</div>
			<div class="clear"></div>
			<div class="clear"></div>
			<div class="grid_16">
			<?php echo '<p style="text-align:center; font-size:12px">'.__('This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the system administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.', true).'</p>'; ?>
			<?php echo '<p class="info-box info-message" style=""><span></span>Notice: This software is under development and 
			you may face bugs, see incomplete features and the already running features may get changed.
			Please report any bugs you face to <a href="mailto:bugs@mereb.com.et" style="color:#ebad05">bugs@mereb.com.et</a></p>'; ?>
			</div>
			<div class="grid_16" id="site_info">
				 <div class="footerbox">
				   <p style="margin:0px; padding:0px"><strong>&copy; <?php echo (date("Y")); ?> Arba Minch University<br /></strong>

<!--
Designed and Developed By Mereb Technologies <a href="http://www.merebtechnologies.com" style="color:#ebad05">MerebTechnologies.com</a> 
 -->
</p> 
				 </div>
			</div>
			<div class="clear"></div>
			<div class="prefix_3 suffix_3">&nbsp;</div>
		</div>
<?php
    echo $this->Js->writeBuffer();
?>
</body>
</html>
