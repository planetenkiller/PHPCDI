<?php

namespace PHPCDI\Example\Doctrine;

class Main {
    /**
     * @Inject 
     * @var PHPCDI\Example\Doctrine\Entities\UserRepository
     */
    private $userRepository;
    
    /**
     * @Inject
     * @var Doctrine\ORM\EntityManager
     */
    private $em;
    
    public function main() {
        echo "create user \n\n";
        
        $user = new \PHPCDI\Example\Doctrine\Entities\User();
        $user->setName('admin');
        $user->setPassword(md5('admin'));
        $this->em->persist($user);
        $this->em->flush();
        $this->em->clear();
        
        echo "find user by id \n";
        $user = $this->userRepository->find($user->getId());
        echo "found user : " . $user->getName() . "\n\n";
        
        echo "find user by name \n";
        $user = $this->userRepository->findOneByName("admin");
        echo "found user : " . $user->getName() . "\n\n";
        
        echo "delete user \n\n";
        $this->em->remove($user);
        $this->em->flush();
        $this->em->clear();
        
        echo "test remove: find user by name \n";
        $user = $this->userRepository->findOneByName("admin");
        echo "found user?: " . ($user != null?'tru':'false') . "\n";
    }
}

