<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentNameHistoriesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('student_name_histories');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->scalar('to_first_name')
            ->requirePresence('to_first_name', 'create')
            ->notEmptyString('to_first_name', 'Please enter first name');

        $validator
            ->scalar('to_middle_name')
            ->requirePresence('to_middle_name', 'create')
            ->notEmptyString('to_middle_name', 'Please enter middle name');

        $validator
            ->scalar('to_last_name')
            ->requirePresence('to_last_name', 'create')
            ->notEmptyString('to_last_name', 'Please enter last name');

        $validator
            ->scalar('minute_number')
            ->requirePresence('minute_number', 'create')
            ->notEmptyString('minute_number', 'Please enter minute number');

        return $validator;
    }
    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }


    function reformat($data = null)
    {
        $reformated_data = array();
        if (isset($data['Student']['id']) && !empty($data['Student']['id'])) {

            ///////////////////////////////amharic/////////////////////////////////////
            $reformated_data['StudentNameHistory']['to_amharic_first_name'] = $data['Student']['amharic_first_name'];
            $reformated_data['StudentNameHistory']['to_amharic_middle_name'] = $data['Student']['amharic_middle_name'];
            $reformated_data['StudentNameHistory']['to_amharic_last_name'] = $data['Student']['amharic_last_name'];

            $reformated_data['StudentNameHistory']['from_amharic_first_name'] = $data['Student']['amharic_first_name'];
            $reformated_data['StudentNameHistory']['from_amharic_middle_name'] = $data['Student']['amharic_middle_name'];
            $reformated_data['StudentNameHistory']['from_amharic_last_name'] = $data['Student']['amharic_last_name'];

            ///////////////////////////////english///////////////////////////////////
            $reformated_data['StudentNameHistory']['to_first_name'] = trim($data['Student']['first_name']);
            $reformated_data['StudentNameHistory']['to_middle_name'] = trim($data['Student']['middle_name']);
            $reformated_data['StudentNameHistory']['to_last_name'] = trim($data['Student']['last_name']);

            $reformated_data['StudentNameHistory']['from_first_name'] = trim($data['Student']['first_name']);
            $reformated_data['StudentNameHistory']['from_middle_name'] = trim($data['Student']['middle_name']);
            $reformated_data['StudentNameHistory']['from_last_name'] = trim($data['Student']['last_name']);
            $reformated_data['StudentNameHistory']['student_id'] = $data['Student']['id'];

            return $reformated_data;
        } else {
            return $data;
        }
    }


}
