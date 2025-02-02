<?= $this->Form->create('ExamResult', array('type' => 'file')); ?>

<script type="text/javascript">

    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }

    $(document).ready(function () {

        $(".AYS").change(function () {
            //serialize form data
            $("#flashMessage").remove();
            var ay = $("#AcadamicYear").val();
            $("#PublishedCourse").empty();
            $("#AcadamicYear").attr('disabled',  true);
            $("#PublishedCourse").attr('disabled', true);
            $("#Semester").attr('disabled', true);
            $("#ExamResultDiv").empty();
            $("#ExamResultDiv").append('<p>Loading ...</p>');
            //get form action
            loadCourseAssignment();
        });

        function loadCourseAssignment() {

            var ay = $("#AcadamicYear").val();
            var formUrl = '/course_instructor_assignments/get_assigned_courses_of_instructor_by_section_for_combo/' + ay + '/' + $("#Semester").val();

            if (ay && formUrl) {
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: ay,
                    success: function (data, textStatus, xhr) {
                        $("#PublishedCourse").empty();
                        $("#PublishedCourse").append(data);
                        // call the function
                        downloadTemplate();
                        //End of items list
                    },
                    error: function (xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            }
            return false;
        }

        //Students list
        $("#PublishedCourse").change(function () {
            //serialize form data
            $("#flashMessage").remove();
            $("#AcadamicYear").attr('disabled', true);
            $("#PublishedCourse").attr('disabled', true);
            $("#Semester").attr('disabled', true);
            var pc = $("#PublishedCourse").val();
            $("#ExamResultDiv").empty();
            $("#ExamResultDiv").append('<p>Loading ...</p>');
            downloadTemplate();
        });

        function downloadTemplate() {
            //Items list
            var pc = $("#PublishedCourse").val();
            //get form action
            var formUrl = '/examResults/download_exam_template/' + pc;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: pc,
                success: function (data, textStatus, xhr) {
                    $("#AcadamicYear").attr('disabled', false);
                    $("#PublishedCourse").attr('disabled', false);
                    $("#Semester").attr('disabled', false);
                    $("#ExamResultDiv").empty();
                    $("#ExamResultDiv").append(data);
                },
                error: function (xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
        }
        downloadTemplate();
    });
</script>

<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-upload" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Import Course Exam Result from CSV'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <div class="examResults form" ng-app="resultEntryForm">

                    <div style="margin-top: -30px;">
                        <hr>
                    	<fieldset style="padding-bottom: 5px;padding-top: 5px;">
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : $defaultacademicyear))); ?>
								</div>
								<div class="large-2 columns">
									<?= $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'label' => 'Semester: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => Configure::read('semesters'), 'default' => $selected_semester)); ?>
								</div>
								<div class="large-7 columns">
									<?= $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'label' => 'Assigned Course: ', 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id, 'style' => 'width:95%;')); ?>
								</div>
							</div>
						</fieldset>
					</div>

                    <!-- AJAX LOADING EXAM RESULTS -->
                    <div id="ExamResultDiv">

                    </div>
                    <!-- AJAX LOADING EXAM RESULTS -->

                    <div>
                        <?php
                        if (isset($non_valide_rows)) {
                            echo "<ul style='color:red'>";
                            foreach ($non_valide_rows as $k => $v) {
                                echo "<li>" . $v . "</li>";
                            }
                            echo "</ul>";
                        } ?>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>