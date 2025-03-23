<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProgramProgramTypeClassRoomsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('program_program_type_class_rooms');
        $this->setPrimaryKey('id');
        $this->setDisplayField('class_room_id');

        // Add associations
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ClassRooms', [
            'foreignKey' => 'class_room_id',
            'joinType' => 'INNER',
        ]);

        // Add timestamp behavior
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('program_id', 'Program ID must be an integer.')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Program ID is required.');

        $validator
            ->integer('program_type_id', 'Program Type ID must be an integer.')
            ->requirePresence('program_type_id', 'create')
            ->notEmptyString('program_type_id', 'Program Type ID is required.');

        $validator
            ->integer('class_room_id', 'Class Room ID must be an integer.')
            ->requirePresence('class_room_id', 'create')
            ->notEmptyString('class_room_id', 'Class Room ID is required.');

        return $validator;
    }
}
