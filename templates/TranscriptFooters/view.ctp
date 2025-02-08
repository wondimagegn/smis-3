<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="transcriptFooters view">
<div class="smallheading"><?php echo __('Transcript Footer View');?></div>
<table>
	<tr>
		<td style="width:15%">Footer Line 1</td>
		<td style="width:85%"><?php echo $transcriptFooter['TranscriptFooter']['line1']; ?></td>
	</tr>
	<tr>
		<td>Footer Line 2</td>
		<td><?php echo $transcriptFooter['TranscriptFooter']['line2']; ?></td>
	</tr>
	<tr>
		<td>Footer Line 3</td>
		<td><?php echo $transcriptFooter['TranscriptFooter']['line3']; ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $transcriptFooter['Program']['name']; ?></td>
	</tr>
	<tr>
		<td>Admission Year:</td>
		<td><?php echo $transcriptFooter['TranscriptFooter']['academic_year']; ?></td>
	</tr>
	
</table>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
