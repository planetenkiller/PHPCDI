<?php

namespace PHPCDI\Example\Events;

class User {
    private $id;
    private $name;
    private $isAdmin;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function isAdmin() {
        return $this->isAdmin;
    }

    public function setAdmin($isAdmin) {
        $this->isAdmin = $isAdmin;
    }
}
