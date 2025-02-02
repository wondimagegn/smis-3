<div id="sidebar">
<div class="flashmessage"> <?php //echo $this->Session->flash(); ?></div>
<?php echo $this->Form->create('User', array('action' => 'login'));?>
<table style="border-width:0px; width:60%; padding:0px; margin:0px">
<tr>
	<td style="border-bottom:#689fd8 solid 1px; background-color:#689fd8; padding:0px; margin:0px; background-image:url(/img/box-top-left-corner.gif); background-repeat:no-repeat; background-position:top left">&nbsp;</td>
	<td style="border-bottom:#689fd8 solid 1px; background-color:#689fd8; color:white; font-weight:bold; font-size:25px; margin:0px; padding:0px">Sign In</td>
	<td style="border-bottom:#689fd8 solid 1px; background-color:#689fd8; padding:0px; margin:0px; background-image:url(/img/box-top-right-corner.gif); background-repeat:no-repeat; background-position:top right">&nbsp;</td>
</tr>
<tr>
<td colspan="3" class="sign-in-box" style="padding:0px; margin:0px; background-color:#fff; border-right:#689fd8 solid 1px; border-bottom:#689fd8 solid 1px">
<table border="0" style="margin:0px; padding:0px; width:100%; background:transparent">
	<tr>
		<td colspan="2" style="background:transparent; border-width:0px; padding-left:60px">&nbsp;</td>
	</tr>
	<tr>
		<td style="background:transparent; border-width:0px; font-weight:bold; font-size:15px; width:125px; text-align:right; color:#0c0691">Username:</td>
		<td style="background:transparent; border-width:0px"><?php echo $this->Form->input('username', array('style' => 'height:18px; width:220px', 'class' => 'fullwidth','label'=>false)); ?></td>
	</tr>
	<tr>
		<td style="background:transparent; border-width:0px; font-weight:bold; font-size:15px; text-align:right; color:#0c0691">Password:</td>
		<td style="background:transparent; border-width:0px"><?php echo $this->Form->input('password', array('style' => 'height:18px; width:220px', 'class' => 'fullwidth','label'=>false)); ?></td>
	</tr>
	<?php

		if(isset($mathCaptcha)){
	?>
	<tr>
		<td colspan="2" style="padding-left:50px; text-align:left; padding-top:20px; color:#0c0691; background:transparent; border-width:0px; font-weight:bold; font-size:15px">Please enter the sum of: <?php echo $mathCaptcha; ?></td>
	</tr>
	<tr>
		<td colspan="2" style="padding-left:50px; background:transparent; border-width:0px"><?php echo $this->Form->input('security_code', array('style' => 'height:18px; width:220px', 'label' => false)); ?></td>
	</tr>
	<?php
		}
	?>
	<tr>
		<td style="background:transparent; border-width:0px">&nbsp;</td>
		<td colspan="2" style="background:transparent; border-width:0px"><?php echo $this->Form->Submit('Sign In', array('div' => false,'class' => 'submitbutton', 'style' => 'width:70px; height:31px; background-color:#689fd8; border-color:#f7fafd; color:#FFFFFF; font-weight:bold')); ?></td>
	</tr>
	<tr>
		<td colspan="3" style="padding-top:10px; padding-bottom:10px; text-align:left; background:transparent; border-width:0px"><?php echo $this->Html->link(__('Forget Your Password').' ?', array('action' => 'forget')); ?></td>
	</tr>
</table>
</td>
</tr>
<!--				<tr>
					<td style="padding:0px; margin:0px; background-image:url(/img/bottom-left-curve.gif); background-repeat:no-repeat; background-position:bottom left">&nbsp;</td>
					<td></td>
					<td style="padding:0px; margin:0px; background-image:url(/img/bottom-right-curve.gif); background-repeat:no-repeat; background-position:bottom right">&nbsp;</td>
				</tr>-->
			</table>

<?php echo $this->Form->end(); ?>
</div>

