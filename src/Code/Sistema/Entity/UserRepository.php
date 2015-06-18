<?php

namespace Code\Sistema\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserProviderInterface{
    
    private $passwordEncoder;
    
    public function createAdminUser($username, $password){
        $user = new User();
        $user->username = $username;
        $user->plainPassword = $password;
        $user->roles = 'ROLE_ADMIN';
        
        return $this->insert($user);
    }
    
    public  function insert($user){
        $this->encodePassword($user);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }


    public function setPasswordEncoder(PasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadUserByUsername($username) {
        
    }

    public function refreshUser(UserInterface $user) {
        
    }

    public function supportsClass($class) {
        return $class === 'Code\Sistema\Entity\User';
    }

    public function encodePassword(User $user) {
        if($user->plainPassword){
            $user->password = $this->passwordEncoder->encodePassword($user->plainPassword, $user->getSalt());
        }
    }
    
    7.6
}
