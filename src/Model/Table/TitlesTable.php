<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TitlesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('titles');
        $this->setPrimaryKey('id');
        $this->setDisplayField('title');

        $this->hasMany('Staffs', [
            'foreignKey' => 'title_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('title', 'create')
            ->notEmptyString('title', 'Provide title name.')
            ->maxLength('title', 255)
            ->add('title', 'unique', [
                'rule' => [$this, 'isUniqueTitle'],
                'message' => 'Title name already recorded. Use another.'
            ]);

        return $validator;
    }

    public function isUniqueTitle($value, $context)
    {
        $conditions = ['title' => trim($value)];
        if (!empty($context['data']['id'])) {
            $conditions['id !='] = $context['data']['id'];
        }

        return !$this->exists($conditions);
    }
}
