<div class="users form">
   <div class="smallheading"><?php echo __('Assign Dormitory Block Level Responsibility'); ?></div>
   <?= $this->Form->create('User'); ?>
   <table>
      <tr>
         <td style="width:40%">
            <?php
            echo '<font class="fs16" style="font-weight:bold">Basic Data </font>'; 
            echo $this->element('staff_basic');
            ?>
         </td>
         <td style="width:60%">
            <?php
            echo '<table>';
            echo $this->Form->hidden('id', array('value' => $staff_basic_data['User']['id']));
            echo '<tr><td class="fs16" style="font-weight:bold">Access Data</td></tr>';
            echo '<tr><td class="fs16">Username: ' . $staff_basic_data['User']['username'] . '</td></tr>';
            echo '<tr><td class="fs16">Check the dormitory block the user will be responsible for</td></tr>';
            echo '<tr><td>' . $this->Form->input('dormitory_block_id', array('type' => 'select', 'multiple' => 'checkbox', 'div' => 'input select', 'label' => false)) . '</td></tr>';
            echo '<tr><td>' . $this->Form->end(__('Assign')) . '</td></tr>';
            echo '<tr>';
            echo '<td>';
            echo '<table>';
            echo '<td class="fs13" style="font-weight:bold" colspan="4">Already allocated dormitory blocks.</td>';
            echo '<tr><th>S.N<u>o</u></th><th>Block</th><th>To</th><th>Action</th></tr>';
            foreach ($alreadyAssignedBlocks as $campus => $assignment) {
               echo '<tr><td colspan=4 style="text-align:center">' . $campus . '</td></tr>';
               $count = 1;
               foreach ($assignment as $as => $av) {
                  echo '<tr><td>' . $count++ . '</td><td>' . $av['DormitoryBlock']['block_name'] . '(' . $av['DormitoryBlock']['type'] . ')' . '</td><td>' . $av['User']['full_name'] . '</td><td>';
                  echo $this->Html->link(__('Unassign'), array('action' => 'assign_user_dorm_block', $av['User']['id'], $av['UserDormAssignment']['id']), null, sprintf(__('Are you sure you want to unassigned  %s from ' . $av['DormitoryBlock']['block_name'] . '(' . $av['DormitoryBlock']['type'] . ')' . ' dormitory block ?'), $av['User']['full_name']));
                  echo '</td></tr>';
               }
            }
            echo '</table>';
            echo '</td>';
            echo '</tr>';
            echo '</table>';
            ?>
         </td>
      </tr>
   </table>
</div>