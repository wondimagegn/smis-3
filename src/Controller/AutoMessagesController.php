<?php
class AutoMessagesController extends AppController {
	var $name = 'AutoMessages';
	var $menuOptions = array(
			'controllerButton' => false,
			'exclude' => array('*')
	);
	public $components =array('RequestHandler');
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->Allow('delete');
                if($this->Session->check('Message.auth')){
               		$this->Session->delete('Message.auth');
            	}
		if ($this->Auth->user() && in_array($this->request->params['action'], array('login'))) {
			return $this->redirect($this->Auth->logout());
		}
	}
	
	function delete($id = null) {
		$this->layout = 'ajax';
		$this->AutoMessage->id = $id;
		if($id && $this->AutoMessage->exists() && $this->Auth->user('id')) {
			$am_count = $this->AutoMessage->find('count',
				array(
					'conditions' =>
					array(
						'AutoMessage.user_id' => $this->Auth->user('id'),
						'AutoMessage.id' => $id
					)
				)
			);
			if($am_count > 0) {
				$auto_message_update['id'] = $id;
				$auto_message_update['read'] = 1;
				$this->AutoMessage->save($auto_message_update);
				$auto_messages = ClassRegistry::init('AutoMessage')->getMessages($this->Auth->user('id'));
			}
		}
	        debug($auto_messages);
		debug($id);
		$this->set('auto_messages',$auto_messages);
		$this->set('_serialize', array('auto_messages'));
	}
}
