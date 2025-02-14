<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class HelpsTable extends Table
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

        $this->setTable('helps');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('title', 'Please provide the title.')
            ->date('document_release_date', ['ymd'], 'Please provide a valid help release date.')
            ->numeric('version', 'Please provide a help version number.');

        return $validator;
    }


    public function preparedAttachment($data = null)
    {
        if (isset($data['Attachment']) && !empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $in => &$dv) {
                if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
                    unset($data['Attachment'][$in]);
                } else {
                    $dv['model'] = 'Help';
                    $dv['group'] = 'attachment';
                }
            }
        }
        return $data;
    }
}
