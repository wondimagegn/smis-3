<?php
if (isset($staff_basic_data) && !empty($staff_basic_data)) { ?>
    <table cellspacing="0" cellpading="0" class="table fs14">
        <tbody>
            <tr>
                <td style="width:20%"> Name: </td>
                <td style="width:80%"> 
                    <?= (isset($staff_basic_data['Staff'][0]['Title']['title']) ? $staff_basic_data['Staff'][0]['Title']['title'] : ''); ?>
                    <?= (isset($staff_basic_data['Staff'][0]['full_name']) ? ' ' . $staff_basic_data['Staff'][0]['full_name'] : ''); ?>
                </td>
            </tr>
            <tr>
                <td> Position: </td>
                <td> <?= (isset($staff_basic_data['Staff'][0]['Position']) && !empty($staff_basic_data['Staff'][0]['Position']) ? ' ' . $staff_basic_data['Staff'][0]['Position']['position'] : '---'); ?> </td>
            </tr>
            <tr>
                <td> Email: </td>
                <td> <?= (!empty($staff_basic_data['Staff'][0]['email']) ? $staff_basic_data['Staff'][0]['email'] : '---'); ?> </td>
            </tr>
            <tr>
                <td> Mobile: </td>
                <td> <?= (!empty($staff_basic_data['Staff'][0]['phone_mobile']) ? $staff_basic_data['Staff'][0]['phone_mobile'] : '---'); ?> </td>
            </tr>
            <tr>
                <td> Office Phone: </td>
                <td> <?= (!empty($staff_basic_data['Staff'][0]['phone_office']) ? $staff_basic_data['Staff'][0]['phone_office'] : '---'); ?> </td>
            </tr>
        </tbody>
    </table>
    <?php
} ?>