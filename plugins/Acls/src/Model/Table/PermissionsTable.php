<?php
// plugins/Acls/src/Model/Table/PermissionsTable.php

namespace Acls\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class PermissionsTable extends Table
{
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('aro_id', 'create')
            ->notEmptyString('aro_id', 'Please select user or role.');

        return $validator;
    }
}
