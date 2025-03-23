<div class="box">
   <div class="box-header bg-transparent">
  	 		<h6 class="box-title">
				<?php echo __('Staff Study');?>
	     	</h6>
   </div>
   <div class="box-body">
     <div class="row">
	  <div class="large-12 columns">
	  <dl>
		<dt><?php echo __('Staff'); ?></dt>
		<dd>
			<?php echo $this->Html->link(ucwords($staffStudy['Staff']['full_name']), array('controller' => 'staffs', 'action' => 'staff_profile', $staffStudy['Staff']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Education'); ?></dt>
		<dd>
			<?php echo h($staffStudy['StaffStudy']['education']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Leave Date'); ?></dt>
		<dd>
			<?php echo h($staffStudy['StaffStudy']['leave_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Return Date'); ?></dt>
		<dd>
			<?php echo h($staffStudy['StaffStudy']['return_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Committement Signed'); ?></dt>
		<dd>
			<?php 
echo $staffStudy['StaffStudy']['committement_signed']==true ? 'Yes':'No';

			?>
			&nbsp;
		</dd>
		<dt><?php echo __('Specialization'); ?></dt>
		<dd>
			<?php echo h($staffStudy['StaffStudy']['specialization']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Country'); ?></dt>
		<dd>
			<?php echo $staffStudy['Country']['name'];  ?>
			&nbsp;
		</dd>
		<dt><?php echo __('University Joined'); ?></dt>
		<dd>
			<?php echo h($staffStudy['StaffStudy']['university_joined']); ?>
			&nbsp;
		</dd>
		
	</dl>
	<?php 
if (!empty($staffStudy['Attachment'])) {
    echo "<table width='100%'>";
    foreach ($staffStudy['Attachment'] as $cuk=>$cuv) {
		echo '<tr><td >File uploaded on: '.$this->Format->humanize_date($cuv['created']). '</td></tr>';
	
		if (strcasecmp($cuv['group'], 'Commitement') == 0) {
		echo '<tr><td style="width:600px;" > Commitement<br/>'.$this->Media->embedAsObject($cuv['dirname'].DS.$cuv['basename'], array('width'=>'900px','height'=>"400px"))."</td></tr>";
		}

    } 
    echo "</table>";
}

?>
</div>
</div>
</div>
</div>
