	
<table style="width:100%;border:0px;" class="condence" id="AutoMessage">
	<?php
	if(empty($auto_messages)) { ?>
		<tr>
			<td style="border:0px solid #ffffff" colspan="2"><p style="padding-bottom:17px">No new messages for now.</p></td>
		</tr>
		<?php
	} else {
		foreach($auto_messages as $key => $auto_message) { ?>
			<tr id="<?= $auto_message['AutoMessage']['id']; ?>1">
				<td style="font-size:10px; font-weight:bold"><?= $this->Time->format("M j, Y g:i:s A", $auto_message['AutoMessage']['created'], NULL, NULL); ?> (<span style="color:red; cursor:url('../img/error.ico'), default" onclick="closeMessage('<?= $auto_message['AutoMessage']['id']; ?>')">close</span>)</td>
			</tr>
			<tr id="<?= $auto_message['AutoMessage']['id']; ?>2">
				<td style="padding-left:10px"><?= $auto_message['AutoMessage']['message']; ?></td>
			</tr>
			<?php
		}
	} ?>
</table>
