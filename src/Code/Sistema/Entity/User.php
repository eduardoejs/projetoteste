<?php

namespace Code\Sistema\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Code\Sistema\Entity\UserRepository")
 */
class User implements UserInterface{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $username;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $password;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $plainPassword;
    
    /**
     * @ORM\Column(type="string", length=100)
     */
    public $roles = array('ROLE_USER');
    
    /**
     * @ORM\Column(type="datetime", length=100)
     */
    public $createdAt;
        
    public function __construct() {
        $this->createdAt = new \DateTime();
    }
    
    function getId() {
        return $this->id;
    }

    function getUsername() {
        return $this->username;
    }

    function getPassword() {
        return $this->password;
    }

    function getPlainPassword() {
        return $this->plainPassword;
    }

    function getRoles() {
        return $this->roles;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

    function setRoles($roles) {
        $this->roles = $roles;
    }

    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function eraseCredentials() {
        $this->plainPassword = null;
    }
    
    public function getSalt() {
        return null;
    }
        
    public function __toString() {
        return $this->getUsername();
    }

    public function toArray(){
        return array(
            'id' => $this->id,
            'username' => $this->username,
            'salt' => $this->getSalt(),
            'roles' => $this->getRoles(),
            'password' => $this->getPassword()
        );
    }
}
