<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WeblinksTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('weblinks');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('title', 'Please provide web link title, it is required.')
            ->url('url_address', 'Please provide a valid URL, it is required.');

        return $validator;
    }

    public function deleteWeblinkList($courseId = null, $data = null)
    {
        $deleteIds = $this->find('list', [
            'keyField' => 'id',
            'valueField' => 'id',
            'conditions' => ['Weblinks.course_id' => $courseId]
        ])->toArray();

        $dontDeleteIds = [];
        if (!empty($data['Weblink'])) {
            foreach ($data['Weblink'] as $webLink) {
                if (!empty($webLink['id']) && in_array($webLink['id'], $deleteIds)) {
                    $dontDeleteIds[] = $webLink['id'];
                }
            }
        }

        $deleteIds = array_diff($deleteIds, $dontDeleteIds);

        if (!empty($deleteIds)) {
            return $this->deleteAll(['id IN' => $deleteIds]);
        }
        return false;
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
        $rules->add($rules->existsIn(['course_id'], 'Courses'));

        return $rules;
    }
}
