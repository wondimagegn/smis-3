<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AnnouncementsTable extends Table
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

        $this->setTable('announcements');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'propertyName' => 'User',
        ]);
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('headline')
            ->maxLength('headline', 255)
            ->requirePresence('headline', 'create')
            ->notEmptyString('headline');

        $validator
            ->scalar('story')
            ->requirePresence('story', 'create')
            ->notEmptyString('story');

        $validator
            ->boolean('is_published')
            ->notEmptyString('is_published');

        $validator
            ->dateTime('annucement_start')
            ->requirePresence('annucement_start', 'create')
            ->notEmptyDateTime('annucement_start');

        $validator
            ->dateTime('annucement_end')
            ->requirePresence('annucement_end', 'create')
            ->notEmptyDateTime('annucement_end');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function getNotExpiredAnnouncements()
    {

        $announcements = $this->find(
            'all',
            array(
                'conditions' => array(
                    'Announcement.annucement_start <=' => date('Y-m-d'),
                    'Announcement.annucement_end >=' => date('Y-m-d'),
                    'Announcement.is_published' => 1,

            ),
                'order' => array('Announcement.annucement_start DESC'),
                'contain' => array('User')
            )
        );
        return $announcements;
    }
}
