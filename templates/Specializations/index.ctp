<?php
echo $this->Form->Create('Specialization', array('action' => 'search'));

?>
<div class="box">
    <div class="box-body">

        <div class="row">
            <div
                class="large-12 columns">
                <h5 class="box-title">
                    <?php echo __('Specialization  Search'); ?>
                </h5>
            </div>
        </div>
        <div class="row">
            <div
                class="large-6 columns">
                <?php

                echo $this->Form->input(
                    'Search.college_id',
                    array(
                        //'label' => '',
                        'class'
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
                    )
                );

                ?>
            </div>
            <div
                class="large-6 columns">
                <?php

                echo $this->Form->input(
                    'Search.department_id',
                    array(
                        //'label' => '',
                        'class'
                        => 'form-control',
                        'placeholder' =>
                        'Department',
                        'required' =>
                        'required',
                        'empty' => '--Select
                Department--',
                        'id' =>
                        'department_id_1',
                        'style' => 'width:250px'
                    )
                );

                ?>
            </div>
        </div>

        <div class="row">
            <div
                class="large-6 columns">
                <?php

                echo $this->Form->input('Search.name');

                ?>
            </div>

        </div>


        <div class="row">
            <div
                class="large-12 columns">
                <?php

                echo $this->Form->submit('Search', array('class' => 'tiny radius button bg-blue'));

                ?>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">

                <?php if (
                    isset($specializations) &&
                    !empty($specializations)
                ) { ?>

                <div
                    class="specializations index">
                    <h2><?php echo __('Specializations'); ?>
                    </h2>
                    <table
                        cellpadding="0"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th><?php echo 'S.No'; ?>
                                </th>
                                <th><?php echo $this->Paginator->sort('department_id'); ?>
                                </th>
                                <th><?php echo $this->Paginator->sort('name'); ?>
                                </th>
                                <th><?php echo $this->Paginator->sort('active'); ?>
                                </th>

                                <th
                                    class="actions">
                                    <?php echo __('Actions'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $start = $this->Paginator->counter('%start%');
                                foreach ($specializations as $specialization) : ?>
                            <tr>
                                <td><?php echo $start++; ?>&nbsp;
                                </td>
                                <td>
                                    <?php echo $this->Html->link($specialization['Department']['name'], array('controller' => 'departments', 'action' => 'view', $specialization['Department']['id'])); ?>
                                </td>
                                <td><?php echo h($specialization['Specialization']['name']); ?>&nbsp;
                                </td>
                                <td><?php echo h($specialization['Specialization']['active']); ?>&nbsp;
                                </td>

                                <td
                                    class="actions">
                                    <?php echo $this->Html->link(__('View'), array('action' => 'view', $specialization['Specialization']['id'])); ?>
                                    <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $specialization['Specialization']['id'])); ?>
                                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $specialization['Specialization']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $specialization['Specialization']['id']))); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p>
                        <?php
                            echo $this->Paginator->counter(array(
                                'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                            ));
                            ?> </p>
                    <div class="paging">
                        <?php
                            echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
                            echo $this->Paginator->numbers(array('separator' => ''));
                            echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
                            ?>
                    </div>
                </div>

                <?php } ?>

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
        '/departments/get_department_combo/' +
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