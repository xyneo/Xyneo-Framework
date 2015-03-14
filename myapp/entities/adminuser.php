<?php

/**
 * @author AnarchyChampion
 * @version 1.0.0
 */
class Adminuser
{

    /**
     * User's ID
     *
     * @var integer
     */
    private $id;

    /**
     * User's name
     *
     * @var string
     */
    private $name;

    /**
     * User's email
     *
     * @var string
     */
    private $email;

    /**
     * Hash of user's password
     *
     * @var string
     */
    private $passwordHash;

    /**
     * User's role
     *
     * @var string
     */
    private $role;

    /**
     * User's activity status
     *
     * @var boolean
     */
    private $active;

    /**
     * User's system admin status
     *
     * @var boolean
     */
    private $sysadmin;

    /**
     * Load user data to entity class.
     *
     * @param stdClass $userData            
     * @return Adminuser
     */
    public function __construct($userData)
    {
        foreach ($userData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
            if ($key == "password") {
                $this->passwordHash = $value;
            }
        }
        
        return $this;
    }

    /**
     * Get user's ID.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get user's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get user's email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Match the user's password hash with the given password hash.
     *
     * @param string $passwordHash            
     * @return boolean
     */
    public function matchPassword($passwordHash)
    {
        return $this->passwordHash == $passwordHash;
    }

    /**
     * Check if the user is an admin.
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->role == "admin";
    }

    /**
     * Get user's role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Check if the user is active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return (boolean) $this->active;
    }

    /**
     * Check if the user is a system admin.
     *
     * @return boolean
     */
    public function isSysAdmin()
    {
        return (boolean) $this->sysadmin;
    }

    /**
     * Get user datas
     *
     * @return array
     */
    public function getDatas()
    {
        return array(
            "id" => $this->getId(),
            "name" => $this->getName(),
            "email" => $this->getEmail(),
            "role" => $this->getRole(),
            "active" => (int) $this->isActive(),
            "sysadmin" => (int) $this->isSysAdmin()
        );
    }
}

?>