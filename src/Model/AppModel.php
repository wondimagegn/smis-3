<?php
App::uses('Model', 'Model');
App::uses('Utility', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('BehaviorCollection', 'Model');
App::uses('ModelBehavior', 'Model');
class AppModel extends Model
{
   // We can log all actions by calling this here, but it is also possible to call the loggable behavior in selected models. 
   // public $recursive = -1;
   // public $invalidatesFromController = array();
   public $actsAs = array(
      'Containable',
      //'Tools.Logable'
      //'Logable'
      'Tools.Logable' => array(
			'change' => 'full',
			//'change' => 'list',
			'description_ids' => 'true',
			'displayField' => 'username',
			'foreignKey' => 'foreign_key'
		)
   );

   public function emptyTable()
   {
      $table = $this->tablePrefix . $this->table;
      $result = $this->query("TRUNCATE $table");
      //$this->setDataSource('default');
      return $result;
   }

   /* public function saveField($name, $value, $validate = false)
   {
      //$this->setDataSource('master');
      $this->useDbConfig = 'master';
      $response = parent::saveField($name, $value, $validate);
      //$this->setDataSource('default');
      $this->useDbConfig = 'default';
      return $response;
   }

   public function save($data = null, $validate = true, $fieldList = array())
   {
      //$this->setDataSource('master');
      $this->useDbConfig = 'master';
      $response = parent::save($data, $validate, $fieldList);
      //$this->setDataSource('default');
      $this->useDbConfig = 'default';
      return $response;
   }

   public function updateAll($fields, $conditions = true)
   {
      // $this->setDataSource('master');
      $this->useDbConfig = 'master';
      $response = parent::updateAll($fields, $conditions);
      $this->useDbConfig = 'default';
      // $this->setDataSource('default');
      return $response;
   }
   public function saveAll($data = array(), $options = array())
   {
      //$this->setDataSource('master');
      $this->useDbConfig = 'master';
      $response = parent::save($data, $options);
      // $this->setDataSource('default');
      $this->useDbConfig = 'default';
      return $response;
   }
   public function delete($id = null, $cascade = true)
   {
      // $this->setDataSource('master');
      $this->useDbConfig = 'master';
      $response = parent::delete($id, $cascade);
      //$this->setDataSource('default');
      $this->useDbConfig = 'default';
      return $response;
   }
   public function deleteAll($conditions, $cascade = true, $callbacks = false)
   {
      //$this->setDataSource('master');
      $this->useDbConfig = 'master';
      $response = parent::deleteAll($conditions, $cascade, $callbacks);
      //$this->setDataSource('default');
      $this->useDbConfig = 'default';
      return $response;
   } */

} ?>