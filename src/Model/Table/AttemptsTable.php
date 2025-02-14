<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;

class AttemptsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('attempts');
        $this->setPrimaryKey('id');

        // Optional timestamps if needed
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('ip', 'IP address is required')
            ->notEmptyString('username', 'Username is required')
            ->notEmptyString('action', 'Action is required')
            ->notEmptyDateTime('expires', 'Expiration date is required');

        return $validator;
    }

    public function countAttempts(string $ip, string $username, string $action): int
    {
        return $this->find()
            ->where([
                'ip' => $ip,
                'username' => $username,
                'action' => $action,
                'expires >' => FrozenTime::now()
            ])
            ->count();
    }

    public function isLimitReached(string $ip, string $username, string $action, int $limit): bool
    {
        return ($this->countAttempts($ip, $username, $action) < $limit);
    }

    public function recordFailure(string $ip, string $username, string $action, string $duration): bool
    {
        $attempt = $this->newEntity([
            'ip' => $ip,
            'username' => $username,
            'action' => $action,
            'expires' => FrozenTime::now()->modify($duration)
        ]);

        return (bool) $this->save($attempt);
    }

    public function resetAttempts(string $ip, string $username, string $action): int
    {
        return $this->deleteAll([
            'ip' => $ip,
            'username' => $username,
            'action' => $action
        ]);
    }

    public function cleanupOldAttempts(): int
    {
        return $this->deleteAll([
            'expires <' => FrozenTime::now()
        ]);
    }
}
