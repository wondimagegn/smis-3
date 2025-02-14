<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class VotesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('votes');
        $this->setPrimaryKey('id');

        // Define associations
        $this->belongsTo('Requesters', [
            'className' => 'Users',
            'foreignKey' => 'requester_user_id',
        ]);

        $this->belongsTo('ApplicableOn', [
            'className' => 'Users',
            'foreignKey' => 'applicable_on_user_id',
        ]);

        $this->belongsTo('ConfirmedBy', [
            'className' => 'Users',
            'foreignKey' => 'confirmed_by',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('requester_user_id', 'Requester ID is required')
            ->notEmptyString('applicable_on_user_id', 'Applicable User ID is required')
            ->notEmptyString('confirmation', 'Confirmation status is required');

        return $validator;
    }

    public function getListOfTaskForConfirmation($userId)
    {
        $validDateFrom = FrozenTime::now()->subHours(72);
        return $this->find()
            ->where([
                'requester_user_id !=' => $userId,
                'confirmation' => 0,
                'created >=' => $validDateFrom,
            ])
            ->contain(['Requesters', 'ApplicableOn.Staff.Department', 'ApplicableOn.Staff.College', 'ConfirmedBy'])
            ->order(['created' => 'DESC'])
            ->all();
    }

    public function getListOfMyTaskForConfirmation($userId)
    {
        $validDateFrom = FrozenTime::now()->subDays(7);
        return $this->find()
            ->where([
                'requester_user_id' => $userId,
                'created >=' => $validDateFrom,
            ])
            ->contain(['Requesters', 'ApplicableOn.Staff.Department', 'ApplicableOn.Staff.College', 'ConfirmedBy'])
            ->order(['created' => 'DESC'])
            ->all();
    }

    public function getListOfConfirmedTasks($userId)
    {
        $validDateFrom = FrozenTime::now()->subDays(7);
        return $this->find()
            ->where([
                'confirmed_by' => $userId,
                'created >=' => $validDateFrom,
            ])
            ->contain(['Requesters', 'ApplicableOn.Staff.Department', 'ApplicableOn.Staff.College', 'ConfirmedBy'])
            ->order(['created' => 'DESC'])
            ->all();
    }

    public function getListOfOtherAdminTasks($userId)
    {
        $validDateFrom = FrozenTime::now()->subDays(30);
        return $this->find()
            ->where([
                'requester_user_id !=' => $userId,
                'confirmed_by !=' => $userId,
                'confirmed_by IS NOT' => null,
                'created >=' => $validDateFrom,
            ])
            ->contain(['Requesters', 'ApplicableOn.Staff.Department', 'ApplicableOn.Staff.College', 'ConfirmedBy'])
            ->order(['created' => 'DESC'])
            ->all();
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
        $rules->add($rules->existsIn(['requester_user_id'], 'RequesterUsers'));
        $rules->add($rules->existsIn(['applicable_on_user_id'], 'ApplicableOnUsers'));

        return $rules;
    }
}
