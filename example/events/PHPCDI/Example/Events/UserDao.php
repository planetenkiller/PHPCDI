<?php

namespace PHPCDI\Example\Events;

class UserDao {
    private $data;
    private $nextFreeId = 1;
    
    /**
     * @Inject
     * @Event("PHPCDI\Example\Events\UserEvent")
     * @var PHPCDI\API\Event\Event
     */
    private $userEvent;

    public function insert(User $user) {
        $user->setId($this->nextFreeId);
        $this->nextFreeId++;
        $this->data[$user->getId()] = $user;
        
        $qualifier = array(Insert::newInstance());
        if($user->isAdmin()) {
            $qualifier[] = Admin::newInstance();
        }
        $this->userEvent->select($qualifier)->fire(new UserEvent($user));
    }

    public function delete(User $user) {
        if(isset($this->data[$user->getId()])) {
            unset($this->data[$user->getId()]);
            $qualifier = array(Delete::newInstance());
            if($user->isAdmin()) {
                $qualifier[] = Admin::newInstance();
            }
            $this->userEvent->select($qualifier)->fire(new UserEvent($user));
        } else {
            throw new \InvalidArgumentException('unknown user ' . $user->getId());
        }
    }

    public function update(User $user) {
        if(isset($this->data[$user->getId()])) {
            $this->data[$user->getId()] = $user;
            $qualifier = array(Update::newInstance());
            if($user->isAdmin()) {
                $qualifier[] = Admin::newInstance();
            }
            $this->userEvent->select($qualifier)->fire(new UserEvent($user));
        } else {
            throw new \InvalidArgumentException('unknown user ' . $user->getId());
        }
    }

    /**
     * @return PHPCDI\Example\Events\User
     */
    public function get($id) {
        return $this->data[$id];
    }
}
