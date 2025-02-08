<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('View Graduation Cetrificate Template Details'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<dl>
					<dt><?= __('Program'); ?></dt>
					<dd><?= $graduationCertificate['Program']['name']; ?></dd>
					<dt><?= __('Program Type'); ?></dt>
					<dd><?= $graduationCertificate['ProgramType']['name']; ?></dd>
					<dt><?= __('Amharic Title'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['amharic_title']; ?></dd>
					<dt><?= __('Title Font Size (Amharic)'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['am_title_font_size']; ?></dd>
					<dt><?= __('Amharic Content'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['amharic_content']; ?></dd>
					<dt><?= __('Content Font Size (Amharic)'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['am_content_font_size']; ?></dd>
					<dt><?= __('English Title'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['english_title']; ?></dd>
					<dt><?= __('Title Font Size (English)'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['en_title_font_size']; ?></dd>
					<dt><?= __('English Content'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['english_content']; ?></dd>
					<dt><?= __('Content Font Size (Amharic)'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['en_content_font_size']; ?></dd>
					<dt><?= __('Academic Year'); ?></dt>
					<dd><?= $graduationCertificate['GraduationCertificate']['academic_year']; ?></dd>
					<dt><?= __('Applicable For Current Student'); ?></dt>
					<dd><?= ($graduationCertificate['GraduationCertificate']['applicable_for_current_student'] == 1 ? 'Yes' : 'No'); ?></dd>
					<dt><?= __('Created'); ?></dt>
					<dd><?= $this->Format->humanize_date($graduationCertificate['GraduationCertificate']['created']); ?></dd>
					<dt><?= __('Modified'); ?></dt>
					<dd><?= $this->Format->humanize_date($graduationCertificate['GraduationCertificate']['modified']); ?></dd>
				</dl>
			</div>
		</div>
	</div>
</div>