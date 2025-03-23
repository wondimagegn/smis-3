<?php

namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Utility\Security;

class LegacyPasswordHasher extends AbstractPasswordHasher
{
    public function hash($password)
    {
        // New passwords will be hashed using Bcrypt
        return (new DefaultPasswordHasher())->hash($password);
    }

    public function check($password, $hashedPassword)
    {
        // First, check if it matches the new Bcrypt hash
        $hasher = new DefaultPasswordHasher();
        debug($hasher);
        debug($hasher->check($password, $hashedPassword));
        if ($hasher->check($password, $hashedPassword)) {
            return true;
        }

        // If Bcrypt check fails, fall back to CakePHP 2.10 SHA1 hash
        debug(Configure::read('Security.salt'));
        $legacyHash = Security::hash($password, 'sha1', Configure::read('Security.salt'));
        debug($password);
        debug($legacyHash);
        debug($hashedPassword);


        return hash_equals($legacyHash, $hashedPassword);
    }
}
