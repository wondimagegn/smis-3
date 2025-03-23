<?php echo $this->Form->create('Specialization'); ?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">

                <h3> <?php echo __('Edit Specialization.'); ?>
                </h3>
            </div>
        </div>

        <div class="row">
            <div
                class="large-6 columns">
                echo
                $this->Form->input('id');
                echo
                $this->Form->input('college_id',
                array(
                'label' => '', 'class'
                => 'form-control',
                'placeholder' =>
                'College/Institution',
                'required' =>
                'required',
                'empty' =>
                '--College/Institution--',
                'id' => 'college_id_1',
                'onload' =>
                "updateDepartmentCollege(1)",
                'onchange' =>
                'updateDepartmentCollege(1)',
                'style' => 'width:250px'
                ));
            </div>

            <div
                class="large-6 columns">
                echo
                $this->Form->input('department_id',
                array(
                'label' => '', 'class'
                => 'form-control',
                'placeholder' =>
                'Department',
                'required' =>
                'required',
                'empty' => '--Select
                Department--',
                'id' =>
                'department_id_1'
                ));
            </div>


        </div>

        <div class="row">
            <div
                class="large-6 columns">
                echo
                $this->Form->input('name');
            </div>
            <div
                class="large-6 columns">
                echo
                $this->Form->input('active');
            </div>
        </div>
        <div class="row">
            <div
                class="large-12 columns">
                <?php
				echo $this->Form->submit(__('Submit'), array('name' => 'add', 'class' => 'tiny radius button bg-blue', 'id' => 'add', 'div' => false));
				?>
            </div>
        </div>


    </div>
</div>

<script>
function updateDepartmentCollege(id) {

    //serialize form data
    var formData = $("#college_id_" +
        id).val();
    $("#college_id_" + id).attr(
        'disabled', true);
    $("#department_id_" + id).attr(
        'disabled', true);

    //get form action
    var formUrl =
        '/pages/get_department_combo/' +
        formData;
    $.ajax({
        type: 'get',
        url: formUrl,
        data: formData,
        success: function(data,
            textStatus, xhr
        ) {
            $("#department_id_" +
                    id)
                .attr(
                    'disabled',
                    false);
            $("#college_id_" +
                    id)
                .attr(
                    'disabled',
                    false);
            $("#department_id_" +
                    id)
                .empty();
            $("#department_id_" +
                    id)
                .append(
                    data);


        },
        error: function(xhr,
            textStatus,
            error) {
            alert(
                textStatus
            );
        }
    });

    return false;

}
</script>