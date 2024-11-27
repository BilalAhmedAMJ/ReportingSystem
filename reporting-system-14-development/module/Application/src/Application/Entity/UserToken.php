<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
 
use JMS\Serializer\Annotation\MaxDepth;

 
 /**
 *
 * An Answer represents a "response" from auser for a question for a particular report
 * 
 * @ORM\Entity
 * @ORM\Table(name="user_tokens")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class UserToken 
{
    
    const ACTION_CONFIRM='confirm';
    const ACTION_RESET_PASSWORD='reset_password';
        
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $action;
    
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $expiry;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="issued_user_id", referencedColumnName="id", nullable=false)
     * 
     * @MaxDepth(1)
     */
    protected $user;   
    
    /*
     * Sets default expiry values
     * Default expiry value depends on 
     */
    public function setDefaultExpiry(){
        $this->expiry=new \DateTime();
        
        if($this->action == $this::ACTION_RESET_PASSWORD){
            //expiry for password reset is 2hrs
            $this->expiry->modify(DEFAULT_PASSWORD_RESET_EXPIRY);    
        }else{
            //default expiry is 2 days
            $this->expiry->modify(DEFAULT_USER_TOKEN_EXPIRY);                
        }        
    }

    public function expireToken(){
        $this->expiry = new \DateTime();
        $this->expiry->modify('-1 sec');
        
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return UserToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return UserToken
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set expiry
     *
     * @param \DateTime $expiry
     * @return UserToken
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }

    /**
     * Get expiry
     *
     * @return \DateTime 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\User $user
     * @return UserToken
     */
    public function setUser(\Application\Entity\User $user)
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
    
    public function isExpired(){
        $now=new \DateTime();
        return $now > $this->expiry;
    }
}
