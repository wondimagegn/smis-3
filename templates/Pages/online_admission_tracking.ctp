<?php echo $this->Form->create('Page', array('controller' => 'pages', 'action' => 'online_admission_tracking', 'method' => 'post','id' => 'MyForm',
        'enctype' => 'multipart/form-data',
        'type' => 'file')); ?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">

                <h3> <?php echo __('Online Application  Status.'); ?>
                </h3>
            </div>
            <div
                class="large-12 columns">
                <?php echo $this->Form->input('OnlineApplicant.trackingnumber', array('label' => '', 'placeholder' => 'Application number')); ?>

            </div>
	
	    <div class="large-12 columns">

	  		 <label>Incase if you didnt submit payment slip attach here 
                                            <?php
                                                echo $this->Form->input(
                                                    'Attachment.1.file',
                                                    array(
                                                        'type' => 'file', 'label' => '',
                                                       
                                                        'id' => 'ReceiptFormAttachment',

                                                        'onchange' =>
                                                        "return fileValidation(this)",

                                                    )
                                                );
                                                ?>
                          </label>
	    </div>

            <div
                class="large-12 columns">
                <?php
                echo $this->Form->end(
                    array('label' => __('Search/Submit', true), 'class' => 'tiny radius button bg-blue')
                );

                ?>
            </div>
            <?php if (isset($request) && !empty($request)) { ?>
            <div
                class="large-12 columns">
                <table>
                    <thead>
                        <tr>
                            <th>Name
                            </th>
                            <th
                                colspan="3">
                                <?php echo $request['OnlineApplicant']['first_name'] . ' ' . $request['OnlineApplicant']['father_name'] . ' ' . $request['OnlineApplicant']['grand_father_name']; ?>
                            </th>
                        </tr>
                        <tr>
                            <th>ID</th>
                            <th
                                colspan="3">
                                <?php
                                    echo $request['OnlineApplicant']['applicationnumber']; ?>
                            </th>
                        </tr>
                        <tr>
                            <th>Status
                            </th>
                            <th>Request
                                Date
                            </th>
                            <th>Remark
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($request['OnlineApplicantStatus']) && !empty($request['OnlineApplicantStatus'])) {
                                foreach ($request['OnlineApplicantStatus'] as $kk => $kv) {
                            ?>
                        <tr>

                            <td>
                                <?php


                                            echo $kv['status'];
                                            ?>
                            </td>
                            <td>
                                <?php


                                            echo date(
                                                "F j, Y, g:i a",
                                                strtotime($kv['created'])
                                            );
                                            ?>
                            </td>
                            <td>
                                <?php

                                            echo $kv['remark'];
                                            ?>
                            </td>
                        </tr>
                        <?php
                                }
                            } else {
                                ?>
                        <tr>

                            <td>
                                Pending
                            </td>
                            <td>
                                <?php
                                        echo date(
                                            "F j, Y, g:i a",
                                            strtotime(date('Y-m-d'))
                                        );
                                        ?>
                            </td>
                            <td>
                                Your
                                status
                                will be
                                updated
                                soon,
                                please
                                come
                                back

                            </td>
                        </tr>


                        <?php
                            } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?>

<script>
function fileValidation(obj) {

    var fileInput =
        document.getElementById(obj.id);

    var filePath = fileInput.value;

    // Allowing file type
    var allowedExtensions =
        /(\.pdf)$/i;

    if (!allowedExtensions.exec(
            filePath)) {
        alert(
            'Invalid file type: Please upload only pdf file'
        );
        fileInput.value = '';
        return false;
    }
    return true;
}
</script>
