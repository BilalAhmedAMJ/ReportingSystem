<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\MaxDepth;
 
 /**
 *
 * A Dcoument that is saved in system to be shared with users
 * 
 * @ORM\Entity
 * @ORM\Table(name="user_logins")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class UserLogin 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var datetime
     * @ORM\Column(type="datetime")
    */
    protected $login_time;

        /**
     * @var string
     * @ORM\Column(type="string")
    */
    protected $auth_method;

    /**
     * @var string
     * @ORM\Column(type="string")
    */
    protected $auth_result;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * 
     * @MaxDepth(1)
     * 
     */
    protected $user;   
    

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    protected $logout_time;
        
    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id=$id;
        return $this;
    }

    /**
     * Set expiry_date
     *
     * @param \DateTime $expiryDate
     * @return UserLogin
     */
    public function setLoginTime($login_time)
    {
        $this->login_time = $login_time;

        return $this;
    }

    /**
     * Get expiry_date
     *
     * @return \DateTime 
     */
    public function getLoginTime()
    {
        return $this->login_time;
    }

    /**
     * Set access_rules
     *
     * @param string $auth_result
     * @return UserLogin
     */
    public function setAuthResult($auth_result)
    {
        $this->auth_result = $auth_result;

        return $this;
    }

    /**
     * Get auth_result
     *
     * @return string 
     */
    public function getAuthMethod()
    {
        return $this->auth_result;
    }

    /**
     * Set access_rules
     *
     * @param string $auth_method
     * @return UserLogin
     */
    public function setAuthMethod($auth_method)
    {
        $this->auth_method = $auth_method;

        return $this;
    }

    /**
     * Get auth_result
     *
     * @return string 
     */
    public function getAuthResult()
    {
        return $this->auth_result;
    }

    /**
     * Set logout_time
     *
     * @param \DateTime $dateModified
     * @return UserLogin
     */
    public function setLogoutTime($logout_time)
    {
        $this->logout_time = $logout_time;

        return $this;
    }

    /**
     * Get logout_time
     *
     * @return \DateTime 
     */
    public function getLogoutTime()
    {
        return $this->logout_time;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\User $createdBy
     * @return UserLogin
     */
    public function setUSer(\Application\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set logged_in_as
     *
     * @param \Application\Entity\User $logged_in_as
     * @return UserLogin
     */
    public function setLoggedInAs(\Application\Entity\User $logged_in_as)
    {
        $this->logged_in_as = $logged_in_as;

        return $this;
    }

    /**
     * Get logged_in_as
     *
     * @return \Application\Entity\User 
     */
    public function getLoggedInAs()
    {
        return $this->logged_in_as;
    }

}

/*

SELECT * FROM  `user_logins`;
DROP TABLE if exists `user_logins`;
CREATE TABLE `user_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `auth_method` varchar(255) DEFAULT NULL,
  `auth_result` varchar(255) DEFAULT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_user_login_user` (`user_id`),
  CONSTRAINT `FK_user_login_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

*/