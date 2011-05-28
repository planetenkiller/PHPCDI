<?php

namespace PHPCDI\Example\Doctrine\Entities;

/**
 * @Entity(repositoryClass="PHPCDI\Example\Doctrine\Entities\UserRepository")
 * @Table(name="users")
 */
class User {
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
    
    /**
     * @Column(length=255)
     */
    private $name;
    
    /**
     * @Column(length=32)
     */
    private $password;
    
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
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
}

