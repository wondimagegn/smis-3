<div class="box">
    <div class="box-body">
        <?= $this->Form->create('Page', array(
            'controller' => 'pages', 
            'action' => 'admission', 
            'method' => 'post', 
            'id' => 'MyForm',
            'enctype' => 'multipart/form-data',
            'type' => 'file',
        ));
        ?>
        <div class="row">

            <div class="large-12 columns">
                <h3> <?= __('Online Admission.'); ?> </h3>
            </div>

            <?php
            if (empty($academicCalendars)) { ?>

                <div class="large-12 columns">

                    <h5> <?=  __('The date for online admission is closed.'); ?>
                    </h5>
                </div>

                <?php
            } else { ?>

                <div class="large-12 columns">

                    <ul id="ListOfTab" class="tabs" data-tab>
                        <li class="tab-title active">
                            <a data-toggle="tab" href="#panel1b">Admission Choice</a>
                        </li>
                        <li class="tab-title">
                            <a data-toggle="tab" href="#panel2b">Previous Study</a>
                        </li>
                        <li class="tab-title">
                            <a data-toggle="tab" href="#panel3b">Financial Support</a>
                        </li>
                        <li class="tab-title">
                            <a data-toggle="tab" href="#panel4b">Basic Information</a>
                        </li>
                    </ul>

                    <div class="tabs-content edumix-tab-horz">
                        <div class="content tab-pane active" id="panel1b">
                            <div class="row">
                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-6 columns">
                                            <label>Study Level
                                                <?= $this->Form->input('OnlineApplicant.program_id', array(
                                                    'label' => '', 'class' => 'form-control', 'placeholder' => 'Study Level',
                                                    'required' => 'required'
                                                )); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Admission Type
                                                <?= $this->Form->input('OnlineApplicant.program_type_id', array(
                                                    'label' => '', 'class' => 'form-control', 
                                                    'placeholder' => 'Admission Type', 'required' => 'required'
                                                )); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>College
                                                <?= $this->Form->input('OnlineApplicant.college_id', array(
                                                    'label' => '', 'class' => 'form-control', 
                                                    'placeholder' => 'College/Institution', 'required' => 'required',
                                                    'empty' => '--College/Institution--', 'id' => 'college_id_1',
                                                    'onload' => "updateDepartmentCollege(1)",
                                                    'onchange' => 'updateDepartmentCollege(1)', 'style' => 'width:250px'
                                                )); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Department
                                                <?= $this->Form->input('OnlineApplicant.department_id', array(
                                                    'label' => '', 'class' => 'form-control', 'placeholder' => 'Department',
                                                    'required' => 'required',
                                                    'empty' => '--Select Department--',
                                                    'id' => 'department_id_1'
                                                )); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Academic Year
                                                <?= $this->Form->input('OnlineApplicant.academic_year', array('id' => 'academicyear', 'label' => '', 'type' => 'select', 'options' => $acyeardatas)); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Semester
                                                <?= $this->Form->input('OnlineApplicant.semester', array('options' => $semester, 'type' => 'select',  'class' => 'form-control', 'label' => '')); ?>
                                            </label>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="large-6 columns">
                                            <button class="btn tiny btnNext" type="button">Next</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="content tab-pane " id="panel2b">
                            <div class="row">
                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-4 columns">
                                            <label>Undergraduate University Name
                                                <?php echo $this->Form->input('OnlineApplicant.undergraduate_university_name', array('label' => '')); ?>
                                            </label>
                                        </div>

                                        <div class="large-4 columns">
                                            <label>Undergraduate University CGPA
                                                <?= $this->Form->input('OnlineApplicant.undergraduate_university_cgpa', array('label' => '', 'min' => 0, 'max' => 10, 'style' => 'width:100px')); ?>
                                            </label>
                                        </div>

                                        <div class="large-4 columns">
                                            <label>Undergraduate University Field of Study
                                                <?php echo $this->Form->input('OnlineApplicant.undergraduate_university_field_of_study', array('label' => '')); ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-4 columns">
                                            <label>Postgraduate University Name
                                                <?= $this->Form->input('OnlineApplicant.postgraduate_university_name', array('label' => '')); ?>
                                            </label>
                                        </div>

                                        <div class="large-4 columns">
                                            <label>Postgraduate University CGPA
                                                <?= $this->Form->input('OnlineApplicant.postgraduate_university_cgpa', array('label' => '', 'min' => 0, 'max' => 10, 'style' => 'width:100px')); ?>
                                            </label>
                                        </div>

                                        <div class="large-4 columns">
                                            <label>Postgraduate University Field of Study
                                                <?= $this->Form->input('OnlineApplicant.postgraduate_university_field_of_study', array('label' => '')); ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="large-6 columns">
                                    <button class="btn tiny btnPrevious" type="button">Previous</button>
                                    <button class="btn tiny btnNext" type="button">Next</button>
                                </div>
                            </div>

                        </div>

                        <div class="content tab-pane" id="panel3b">
                            <div class="row">
                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-6 columns">
                                            <label>Financial  Support
                                                <?= $this->Form->input('OnlineApplicant.financial_support', array('label' => '')); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Sponsor Name
                                                <?= $this->Form->input('OnlineApplicant.name_of_sponsor', array('label' => '')); ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="large-4 columns">
                                    <label>
                                        Year of experience
                                        <?= $this->Form->input('OnlineApplicant.year_of_experience', array('label' => '')); ?>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="large-6 columns">
                                    <button class="btn tiny btnPrevious" type="button">Previous</button>
                                    <button class="btn tiny btnNext" type="button">Next</button>
                                </div>
                            </div>
                        </div>

                        <div class="content tab-pane" id="panel4b">

                            <div class="row">
                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-4 columns">
                                            <label>First  Name
                                                <?= $this->Form->input('OnlineApplicant.first_name', array('label' => '')); ?>
                                            </label>
                                        </div>

                                        <div class="large-4 columns">
                                            <label>Father Name
                                                <?= $this->Form->input('OnlineApplicant.father_name', array('label' => '')); ?>
                                            </label>
                                        </div>

                                        <div class="large-4 columns">
                                            <label> Grandfather Name
                                                <?= $this->Form->input('OnlineApplicant.grand_father_name', array('label' => '' )); ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-6 columns">
                                            <label>Gender
                                                <?= $this->Form->input('OnlineApplicant.gender', array(
                                                    'label' => '', 'type' => 'select',
                                                    'options' => array('female' => 'Female', 'male' => 'Male')
                                                )); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Date of birth
                                                <?= $this->Form->input('OnlineApplicant.date_of_birth', array(
                                                    'label' => '',
                                                    'minYear' => date('Y') - Configure::read('Calendar.birthdayInPast'), 
                                                    'maxYear' => date('Y') - 14, 
                                                    'orderYear' => 'desc', 
                                                    'type' => 'date', 
                                                    'style' => 'width:100px;'
                                                ));
                                                ?>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="large-12 columns">
                                    <div class="row">

                                        <div class="large-6 columns">
                                            <label>Email
                                                <?= $this->Form->input('OnlineApplicant.email', array('label' => '')); ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Mobile Phone
                                                <?= $this->Form->input('OnlineApplicant.mobile_phone', array('label' => '')); ?>
                                            </label>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="large-6 columns">
                                            <label>
                                                Combined Application Files Attached as for the announcement
                                                <?= $this->Form->input('Attachment.0.file', array(
                                                    'type' => 'file', 'label' => '', 
                                                    'required' => 'required',
                                                    'id' => 'ApplicationFormAttachment',
                                                    'onchange' => "return fileValidation(this)"
                                                ));
                                                ?>
                                            </label>
                                        </div>

                                        <div class="large-6 columns">
                                            <label>Receipt Attachment
                                                <?= $this->Form->input('Attachment.1.file', array(
                                                        'type' => 'file', 'label' => '',
                                                        'required' => 'required',
                                                        'id' => 'ReceiptFormAttachment',
                                                        'onchange' => "return fileValidation(this)",
                                                    ));
                                                ?>
                                            </label>
                                        </div>

                                    </div>

                                </div>

                                <div class="large-12 columns">
                                    <?= $this->Form->submit(__('Submit the application'), array('name' => 'applyOnline', 'class' => 'tiny radius button bg-blue', 'id' => 'applyOnline', 'div' => false));  ?>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <?php 
            } ?>
        </div>
    </div>
</div>

<?= $this->Form->end(); ?>

<script>

    function fileValidation(obj) {
        var fileInput = document.getElementById(obj.id);
        var filePath = fileInput.value;
        // Allowing file type
        var allowedExtensions = /(\.pdf)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid file type: Please upload only pdf file');
            fileInput.value = '';
            return false;
        }
        return true;
    }

    function updateDepartmentCollege(id) {
        //serialize form data
        var formData = $("#college_id_" + id).val();
        $("#college_id_" + id).attr('disabled', true);
        $("#department_id_" + id).attr('disabled', true);

        //get form action
        var formUrl = '/pages/get_department_combo/' + formData;
        $.ajax({
            type: 'get',
            url: formUrl,
            data: formData,
            success: function(data, textStatus, xhr) {
                $("#department_id_" + id).attr('disabled',false);
                $("#college_id_" + id).attr('disabled', false);
                $("#department_id_" + id).empty();
                $("#department_id_" + id).append(data);
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });
        return false;
    }
</script>