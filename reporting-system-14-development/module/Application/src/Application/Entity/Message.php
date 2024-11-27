<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
 
use JMS\Serializer\Annotation\MaxDepth;

use DoctrineEncrypt\Configuration\Encrypted;

 
 /**
 *
 * An MEssage represents a "notification" from system or any user to a user
 * 
 * @ORM\Entity
 * @ORM\Table(name="messages")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class Message 
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
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="requested_by_user_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $sender;   

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $sent_as;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     */
    protected $subject;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     * @Encrypted
     */
    protected $text_body;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     * @Encrypted
     */
    protected $html_body;


    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
	protected $date_modified;
	
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
	protected $date_sent;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('DRAFT','SENT','UNSENT')")
     */
     protected $status='DRAFT';//initial status on creation of a report is draft

   /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('internal','email')")
     */
     protected $message_type='internal';//initial status on creation of a report is draft
     

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Application\Entity\UserMessage", 
     *                 mappedBy="message", cascade={"persist","refresh", "merge"}, orphanRemoval=true,fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="user_messages",
     *                joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *               )
     * @MaxDepth(1)
     */
     protected $user_messages;



    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Application\Entity\ReportMessage", 
     *                 mappedBy="message", cascade={"persist"}, orphanRemoval=true,fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="report_messages",
     *                joinColumns={@ORM\JoinColumn(name="message_id", referencedColumnName="id")},
     *               )
     * @MaxDepth(1)
     */
     protected $report_messages;
          

    /**
     * 
     * @var json
     * @ORM\Column(type="json_array", nullable=true)
     *  
     */
    protected $attachments;
    
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user_messages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set sent_as
     *
     * @param string $sentAs
     * @return Message
     */
    public function setSentAs($sentAs)
    {
        $this->sent_as = $sentAs;

        return $this;
    }

    /**
     * Get sent_as
     *
     * @return string 
     */
    public function getSentAs()
    {
        return $this->sent_as;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set text_body
     *
     * @param string $textBody
     * @return Message
     */
    public function setTextBody($textBody)
    {
        $this->text_body = $textBody;

        return $this;
    }

    /**
     * Get text_body
     *
     * @return string 
     */
    public function getTextBody()
    {
        return $this->text_body;
    }

    /**
     * Set html_body
     *
     * @param string $htmlBody
     * @return Message
     */
    public function setHtmlBody($htmlBody)
    {
        $this->html_body = $htmlBody;

        return $this;
    }

    /**
     * Get html_body
     *
     * @return string 
     */
    public function getHtmlBody()
    {
        return $this->html_body;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Message
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set message_type
     *
     * @param string $messageType
     * @return Message
     */
    public function setMessageType($messageType)
    {
        $this->message_type = $messageType;

        return $this;
    }

    /**
     * Get message_type
     *
     * @return string 
     */
    public function getMessageType()
    {
        return $this->message_type;
    }

    /**
     * Set sender
     *
     * @param \Application\Entity\User $sender
     * @return Message
     */
    public function setSender(\Application\Entity\User $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Application\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Add user_messages
     *
     * @param \Application\Entity\UserMessage $userMessages
     * @return Message
     */
    public function addUserMessage(\Application\Entity\UserMessage $userMessages)
    {
        $this->user_messages[] = $userMessages;

        return $this;
    }

    /**
     * Remove user_messages
     *
     * @param \Application\Entity\UserMessage $userMessages
     */
    public function removeUserMessage(\Application\Entity\UserMessage $userMessages)
    {
        $this->user_messages->removeElement($userMessages);
    }

    /**
     * Get user_messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserMessages()
    {
        return $this->user_messages;
    }

    /**
     * Set date_modified
     *
     * @param \DateTime $dateModified
     * @return Message
     */
    public function setDateModified($dateModified)
    {
        $this->date_modified = $dateModified;

        return $this;
    }

    /**
     * Get date_modified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set date_sent
     *
     * @param \DateTime $dateSent
     * @return Message
     */
    public function setDateSent($dateSent)
    {
        $this->date_sent = $dateSent;

        return $this;
    }

    /**
     * Get date_sent
     *
     * @return \DateTime 
     */
    public function getDateSent()
    {
        return $this->date_sent;
    }


    /**
     * Add reportMessage
     *
     * @param \Application\Entity\ReportMessage $reportMessage
     *
     * @return Message
     */
    public function addReportMessage(\Application\Entity\ReportMessage $reportMessage)
    {
        $this->report_messages[] = $reportMessage;

        return $this;
    }

    /**
     * Remove reportMessage
     *
     * @param \Application\Entity\ReportMessage $reportMessage
     */
    public function removeReportMessage(\Application\Entity\ReportMessage $reportMessage)
    {
        $this->report_messages->removeElement($reportMessage);
    }

    /**
     * Get reportMessages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReportMessages()
    {
        return $this->report_messages;
    }
    
    
    public function getReportMessageType(){
        
        if($this->getReportMessages() && count($this->getReportMessages())>0){
            $report_message = $this->getReportMessages();
            return $report_message[0]->getLinkType();
        }
        return null;
    }

    /**
     * Set attachments
     *
     * @param array $attachments
     *
     * @return Message
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Get attachments
     *
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
