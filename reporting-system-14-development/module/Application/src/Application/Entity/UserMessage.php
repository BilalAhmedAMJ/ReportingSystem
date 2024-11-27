<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
 
use JMS\Serializer\Annotation\MaxDepth;

 
 /**
 *
 * An Answer represents a "response" from auser for a question for a particular report
 * 
 * @ORM\Entity
 * @ORM\Table(name="user_messages")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class UserMessage 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $user;
    

    /**
     * @var Message
     * @ORM\ManyToOne(targetEntity="Application\Entity\Message",fetch="EAGER")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $message;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_read;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_deleted;
    

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
     * Set date_read
     *
     * @param \DateTime $dateRead
     * @return UserMessage
     */
    public function setDateRead($dateRead)
    {
        $this->date_read = $dateRead;

        return $this;
    }

    /**
     * Get date_read
     *
     * @return \DateTime 
     */
    public function getDateRead()
    {
        return $this->date_read;
    }

    /**
     * Set date_deleted
     *
     * @param \DateTime $dateDeleted
     * @return UserMessage
     */
    public function setDateDeleted($dateDeleted)
    {
        $this->date_deleted = $dateDeleted;

        return $this;
    }

    /**
     * Get date_deleted
     *
     * @return \DateTime 
     */
    public function getDateDeleted()
    {
        return $this->date_deleted;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\User $user
     * @return UserMessage
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

    /**
     * Set message
     *
     * @param \Application\Entity\Message $message
     * @return UserMessage
     */
    public function setMessage(\Application\Entity\Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Application\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     *  return true if a message is deleted. relies on date of deletion 
     */
    public function isDeleted(){
        return $this->date_deleted!=null;
    }
    
    /**
     *  return true if a message is read or not. relies on date of read 
     */
    public function isUnread(){
        return $this->date_read===null;
    }
    
    
}
