<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;

/**
 * Announcements Table
 */
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

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
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
            ->scalar('headline')
            ->requirePresence('headline', 'create')
            ->notEmptyString('headline', 'Please provide a headline.')
            ->scalar('story')
            ->requirePresence('story', 'create')
            ->notEmptyString('story', 'Please provide a story.')
            ->numeric('is_published')
            ->requirePresence('is_published', 'create')
            ->notEmptyString('is_published', 'Please specify publication status.')
            ->dateTime('announcement_start')
            ->requirePresence('announcement_start', 'create')
            ->notEmptyDateTime('announcement_start', 'Please provide a valid start date.')
            ->dateTime('announcement_end')
            ->requirePresence('announcement_end', 'create')
            ->notEmptyDateTime('announcement_end', 'Please provide a valid end date.')
            ->uuid('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id', 'Please provide a valid user ID.');

        return $validator;
    }

    /**
     * Retrieves non-expired, published announcements
     *
     * @return array Announcements
     */
    public function getNotExpiredAnnouncements()
    {
        $today = Time::now()->format('Y-m-d');

        return $this->find()
            ->where([
                'Announcements.announcement_start <=' => $today,
                'Announcements.announcement_end >=' => $today,
                'Announcements.is_published' => 1
            ])
            ->order(['Announcements.announcement_start' => 'DESC'])
            ->contain(['Users'])
            ->toArray();
    }
}
