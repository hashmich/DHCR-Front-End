<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Country extends Entity
{
    protected $_accessible = [
        'name' => true,
        'domain_name' => true,
        'alpha_3' => true,
        'stop_words' => true,
        'cities' => true,
        'courses' => true,
        'emails' => true,
        'institutions' => true,
        'subscriptions' => true,
        'users' => true,
    ];
}
