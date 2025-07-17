<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassRoomBlocks Table
 */
class ClassRoomBlocksTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('class_room_blocks');
        $this->setDisplayField('block_code');
        $this->setPrimaryKey('id');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('Campuses', [
            'foreignKey' => 'campus_id',
            'joinType' => 'LEFT'
        ]);

        $this->hasMany('ClassRooms', [
            'foreignKey' => 'class_room_block_id',
            'dependent' => false
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('block_code')
            ->requirePresence('block_code', 'create')
            ->notEmptyString('block_code', 'Block code should not be empty, Please provide valid Block code.');

        return $validator;
    }

    /**
     * Sends class room block data to child models for validation
     *
     * @return array Class room block data
     */
    public function sendClassRoomBlockData(): array
    {
        return $this->getData() ?: [];
    }
}
