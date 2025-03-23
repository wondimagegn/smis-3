<div class="universities view">
<h2><?php echo __('University Name');?></h2>
		
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $university['University']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amharic Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $university['University']['amharic_name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Short Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $university['University']['short_name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Amharic Short Name '); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $university['University']['amharic_short_name']; ?>
			&nbsp;
		</dd>
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Year Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $university['University']['academic_year']; ?>
			&nbsp;
		</dd>
	</dl>
<?php 
if (!empty($university['Attachment'])) {
    echo "<table>";
    foreach ($university['Attachment'] as $cuk=>$cuv) {
		echo '<tr><td>File uploaded on: '.$this->Format->humanize_date($cuv['created']). '</td></tr>';
		/*
		echo '<tr><td>';
		echo '<a href='.$this->Media->url($cuv['dirname'].DS.$cuv['basename'],true).'
		target=_blank>View Attachment</a>';
		echo '</td></tr>';
		*/

		if (strcasecmp($cuv['group'], 'background') == 0) {
		echo '<tr><td valign="top" align="right"> Transparent Background <br/>'.$this->Media->embedAsObject($cuv['dirname'].DS.$cuv['basename'])."</td></tr>";
		}

		if (strcasecmp($cuv['group'], 'logo') == 0) {
			 echo '<tr><td valign="top" align="right"> Small Logo <br/> '.$this->Media->embedAsObject($cuv['dirname'].DS.$cuv['basename'])."</td></tr>";
		}
    } 
    echo "</table>";
}

?>

</div>
