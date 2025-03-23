
<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Upload Mass Profile Pictures'); ?></span>
		</div>
	</div>
    <div class="box-body pad-forty">
        <div class="row">
            <div class="large-6 columns">
                <?= $this->Form->create('Student', array('controller' => 'students', 'action' => 'mass_import_profile_picture', 'type' => 'file')); ?>
                <label>
                    <strong>Student List : </strong>
                    <?= $this->Form->file('File', array('label' => 'Excel', 'name' => 'data[Student][xls]')); ?>
                </label>
                <br />

                <label><strong>Upload File : </strong>
                    <input type="file" name="data[Student][File][]" id="multiplefilesfilter" multiple="multiple" accept="image/*" />
                </label>
                <br />

                <?= $this->Form->submit('Upload', array('class' => 'tiny radius button bg-blue')); ?>
                <?= $this->Form->end(); ?>
            </div>
            <div class="large-6 columns">
                <div class="your-account">
                    <div class="row">
                        <div class="medium-3 columns">
                            <!-- <div class="circle-progress"></div> -->
                            <div class="circlestat" data-dimension="90" data-text="<?= number_format((($profilePictureUploaded / $totalStudentCount) * 100), 2, '.', '') . '' . '%'; ?>" data-width="8" data-fontsize="16" data-percent="<?= ((($profilePictureUploaded * 100) / $totalStudentCount)); ?>" data-fgcolor="#222" data-border="5" data-bgcolor="#D5DAE6" data-fill="#FFF"></div>
                        </div>
                        <div class="medium-9 columns ">
                            <div style="margin:0 10px;padding:0 0 0 20px" class="summary-border-left">
                                <h4>Profile picture upload isn't complete!</h4>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

<script>
    // Created by remi on 17/01/15. 
    (function() {

        //simple function to display file informations 
        
        function showFileInfo(file) {
            console.log("name : " + file.name);
            console.log("size : " + file.size);
            console.log("type : " + file.type);
            console.log("date : " + file.lastModified);
        }

        var fileInput3 = document.querySelector('#multiplefilesfilter');

        fileInput3.addEventListener('change', function() {
            var files = this.files;
            for (var i = 0; i < files.length; i++) {
                console.group('File ' + i);
                showFileInfo(files[i]);
                console.groupEnd();
            }
        }, false);

    }());
</script>