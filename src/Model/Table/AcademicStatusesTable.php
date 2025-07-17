<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AcademicStatuses Table
 */
class AcademicStatusesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('academic_statuses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('AcademicStands', [
            'foreignKey' => 'academic_status_id',
            'dependent' => false,
        ]);

        $this->hasMany('StudentExamStatuses', [
            'foreignKey' => 'academic_status_id',
            'dependent' => false,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name', 'Please provide status name.')
            ->add('name', 'unique', [
                'rule' => function ($value, $context) {
                    $conditions = ['name' => $value];
                    if (!empty($context['data']['id'])) {
                        $conditions['id !='] = $context['data']['id'];
                    }
                    return !$this->exists($conditions);
                },
                'message' => 'You already have this status.'
            ])
            ->numeric('order', 'Please provide order of status in number.')
            ->notEmptyString('order', 'Please provide order of status in number.');

        return $validator;
    }

    /**
     * Checks if an academic status can be deleted
     *
     * @param int|null $id Academic status ID
     * @return bool True if it can be deleted, false otherwise
     */
    public function canItBeDeleted(?int $id = null): bool
    {
        if ($id === null) {
            return false;
        }

        if ($this->StudentExamStatuses->find()->where(['academic_status_id' => $id])->count() > 0) {
            return false;
        }

        if ($this->AcademicStands->find()->where(['academic_status_id' => $id])->count() > 0) {
            return false;
        }

        return true;
    }
}
