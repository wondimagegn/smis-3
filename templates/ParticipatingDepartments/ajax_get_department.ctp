<?php
// file: /views/page/ajax_get_users_pages.ctp
//debug($departments);
if(!empty($departments)) {
echo $this->Form->input('department_id',array(
	'type'=>'select',
	'options'=>$departments,
	'div'=>false,
	'name'=>'data[Department][Department]'
));
?>
