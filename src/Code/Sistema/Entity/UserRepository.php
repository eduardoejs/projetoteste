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
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles('ROLE_ADMIN');
        
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
        $user = new User();
        $user = $this->findByUsername($username);
        
        if(!$user){
            throw new UsernameNotFoundException(sprintf('Usuário %s não existe', $username));
        }
        
        return $this->arrayToObject($user->toArray());
    }

    public function refreshUser(UserInterface $user) {
        if(!$user instanceof  User){
            throw new UnsupportedUserException(sprintf('Instances of %s are not supported', get_class($user)));
        }
    }

    public function supportsClass($class) {
        return $class === 'Code\Sistema\Entity\User';
    }

    public function encodePassword(User $user) {
        if($user->plainPassword){
            $user->password = $this->passwordEncoder->encodePassword($user->plainPassword, $user->getSalt());
        }
    }
    
    public function objectToArray(User $user){
        return array(
            'id' => $user->id,
            'username' => $user->username,
            'password' => $user->password,
            'roles' => implode(',', $user->roles),
            'created_at' => $user->createdAt->format(self::DATE_TIME)
        );
    }
    
    /**
     * 
     * @param type $userArr
     * @param \Code\Sistema\Entity\User $user
     * @return \Code\Sistema\Entity\User
     */
    public function arrayToObject($userArr, $user = null){
        
        if(!$user){
            $user = new User();
            $user->id = isset($userArr['id']) ? $userArr['id'] : null;
        }
        
        $username = isset($userArr['username']) ? $userArr['username'] : null;
        $password = isset($userArr['password']) ? $userArr['password'] : null;
        $roles = isset($userArr['roles']) ? explode(',', $userArr['username']) : array();
        $createdAt = isset($userArr['created_at']) ? \Datetime::createFromFormat(self::DATE_FORMAT, $userArr['created_at']) : null;
        
        if($username){
            $user->username = $username;
        }
        
        if($password){
            $user->password = $password;
        }
        
        if($roles){
            $user->setRoles($roles);
        }
        
        if($createdAt){
            $user->createdAt = $createdAt;
        }
        
        return $user;
    }
}
