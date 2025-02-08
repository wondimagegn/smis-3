<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Event\Event;
use ArrayObject;

class MailersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('messages'); // Mapping to 'messages' table
        $this->setPrimaryKey('id'); // Define primary key
    }

    /**
     * This function takes an array of email message and saves it to the database.
     * @return bool Returns true if saved successfully.
     */
    public function logMessage(array $message): bool
    {
        $entity = $this->newEntity($message);
        return (bool)$this->save($entity);
    }

    /**
     * Event Management System: Dispatching Event after Save
     */
    public function afterSave(Event $event, $entity, ArrayObject $options)
    {
        if ($entity->isNew()) {
            $event = new Event('Model.Mailer.created', $this, [
                'id' => $entity->id,
                'data' => $entity->toArray(),
            ]);
            $this->getEventManager()->dispatch($event);
        }
    }
}
