<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
 
use JMS\Serializer\Annotation\MaxDepth;

 
 /**
 *
 * An Answer represents a "response" from auser for a question for a particular report
 * 
 * @ORM\Entity
 * @ORM\Table(name="message_templates")
 *
 * @author Haroon
 */
class MessageTemplate 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $template_name;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $subject;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $body_html;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $body_text;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $signature_html;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $signature_text;
    

        /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $access_rule;

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
     * Set template_name
     *
     * @param string $templateName
     * @return MessageTemplate
     */
    public function setTemplateName($templateName)
    {
        $this->template_name = $templateName;

        return $this;
    }

    /**
     * Get template_name
     *
     * @return string 
     */
    public function getTemplateName()
    {
        return $this->template_name;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return MessageTemplate
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
     * Set body_html
     *
     * @param string $bodyHtml
     * @return MessageTemplate
     */
    public function setBodyHtml($bodyHtml)
    {
        $this->body_html = $bodyHtml;

        return $this;
    }

    /**
     * Get body_html
     *
     * @return string 
     */
    public function getBodyHtml()
    {
        return $this->body_html;
    }

    /**
     * Set body_text
     *
     * @param string $bodyText
     * @return MessageTemplate
     */
    public function setBodyText($bodyText)
    {
        $this->body_text = $bodyText;

        return $this;
    }

    /**
     * Get body_text
     *
     * @return string 
     */
    public function getBodyText()
    {
        return $this->body_text;
    }

    /**
     * Set signature_html
     *
     * @param string $signatureHtml
     * @return MessageTemplate
     */
    public function setSignatureHtml($signatureHtml)
    {
        $this->signature_html = $signatureHtml;

        return $this;
    }

    /**
     * Get signature_html
     *
     * @return string 
     */
    public function getSignatureHtml()
    {
        return $this->signature_html;
    }

    /**
     * Set signature_text
     *
     * @param string $signatureText
     * @return MessageTemplate
     */
    public function setSignatureText($signatureText)
    {
        $this->signature_text = $signatureText;

        return $this;
    }

    /**
     * Get signature_text
     *
     * @return string 
     */
    public function getSignatureText()
    {
        return $this->signature_text;
    }

    /**
     * Set access_rule
     *
     * @param string $accessRule
     * @return MessageTemplate
     */
    public function setAccessRule($accessRule)
    {
        $this->access_rule = $accessRule;

        return $this;
    }

    /**
     * Get access_rule
     *
     * @return string 
     */
    public function getAccessRule()
    {
        return $this->access_rule;
    }
}
