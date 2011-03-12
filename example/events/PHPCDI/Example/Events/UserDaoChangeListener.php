<?php

namespace PHPCDI\Example\Events;

class UserDaoChangeListener {
    /**
     * Listen to all user events.
     *
     * @Annos(@Observes $event)
     */
    public function changes(UserEvent $event) {
        echo 'UserDao change for user: ' . $event->getUser()->getId() . '#' . $event->getUser()->getName() . "\n";
    }

    /**
     * Listen to all user insert events.
     *
     * @Annos(@Observes @PHPCDI\Example\Events\Insert $event)
     */
    public function inserts(UserEvent $event) {
        echo 'UserDao insert: ' . $event->getUser()->getId() . '#' . $event->getUser()->getName() . "\n";
    }

    /**
     * Listen to all admin user events.
     *
     * @Annos(@Observes @PHPCDI\Example\Events\Admin $event)
     */
    public function adminChanges(UserEvent $event) {
        echo 'UserDao change of admin user: ' . $event->getUser()->getId() . '#' . $event->getUser()->getName() . "\n";
    }
}
