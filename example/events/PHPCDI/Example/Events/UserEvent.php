<?php

namespace PHPCDI\Example\Events;

class UserEvent {
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * @return PHPCDI\Example\Events\User
     */
    public function getUser() {
        return $this->user;
    }
}
