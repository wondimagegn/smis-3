<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;

class AnnouncementsTable extends Table
{
    /**
     * Initialize method
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('announcements');
        $this->setDisplayField('headline');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        // Associations
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Default validation rules.
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('headline')
            ->requirePresence('headline', 'create')
            ->notEmptyString('headline', 'Please enter a headline.');

        $validator
            ->scalar('story')
            ->requirePresence('story', 'create')
            ->notEmptyString('story', 'Please enter the announcement story.');

        $validator
            ->boolean('is_published')
            ->requirePresence('is_published', 'create')
            ->notEmptyString('is_published');

        $validator
            ->dateTime('annucement_start')
            ->requirePresence('annucement_start', 'create')
            ->notEmptyDateTime('annucement_start', 'Please select a start date and time.');

        $validator
            ->dateTime('annucement_end')
            ->requirePresence('annucement_end', 'create')
            ->notEmptyDateTime('annucement_end', 'Please select an end date and time.')
            ->add('annucement_end', 'compare', [
                'rule' => ['dateTimeComparison', 'annucement_start', '>'],
                'message' => 'End date must be after start date.'
            ]);

        $validator
            ->uuid('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        return $validator;
    }

    /**
     * Custom finder: Get active (not expired) announcements
     */
    public function findActive($query)
    {
        $now = FrozenTime::now()->format('Y-m-d H:i:s');

        return $query
            ->where([
                'Announcements.annucement_start <=' => $now,
                'Announcements.annucement_end >=' => $now,
                'Announcements.is_published' => 1
            ])
            ->contain(['Users'])
            ->orderDesc('Announcements.annucement_start');
    }

    /**
     * Convenience method: getNotExpiredAnnouncements()
     */
    public function getNotExpiredAnnouncements()
    {
        return $this->find('active')->toArray();
    }
}
