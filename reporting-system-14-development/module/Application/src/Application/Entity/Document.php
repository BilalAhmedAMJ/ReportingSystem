<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
 
 /**
 *
 * A Dcoument that is saved in system to be shared with users
 * 
 * @ORM\Entity
 * @ORM\Table(name="documents")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class Document 
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
     * @ORM\Column(type="string")
     */
    protected $title;
    

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $file_name;

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
    */
    protected $expiry_date;

    /**
     * @var string
     * @ORM\Column(type="string")
    */
    protected $document_ext;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $access_rules;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $document_type;
    
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="LAZY")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=false)
    */
    protected $created_by;
    
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="LAZY")
     * @ORM\JoinColumn(name="modified_by_id", referencedColumnName="id", nullable=false)
     */
    protected $last_modified_by;
    

    
    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    protected $document_date;

    
    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    protected $date_created;


    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    protected $date_modified;
    
    
    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="Application\Entity\Branch")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    protected $branch;   
    
    
        
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
     * Set title
     *
     * @param string $title
     * @return Document
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Document
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set file_name
     *
     * @param string $fileName
     * @return Document
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set expiry_date
     *
     * @param \DateTime $expiryDate
     * @return Document
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiry_date = $expiryDate;

        return $this;
    }

    /**
     * Get expiry_date
     *
     * @return \DateTime 
     */
    public function getExpiryDate()
    {
        return $this->expiry_date;
    }

    /**
     * Set document_ext
     *
     * @param string $documentExt
     * @return Document
     */
    public function setDocumentExt($documentExt)
    {
        $this->document_ext = $documentExt;

        return $this;
    }

    /**
     * Get document_ext
     *
     * @return string 
     */
    public function getDocumentExt()
    {
        return $this->document_ext;
    }

    /**
     * Set access_rules
     *
     * @param string $accessRules
     * @return Document
     */
    public function setAccessRules($accessRules)
    {
        $this->access_rules = $accessRules;

        return $this;
    }

    /**
     * Get access_rules
     *
     * @return string 
     */
    public function getAccessRules()
    {
        return $this->access_rules;
    }

    /**
     * Set document_type
     *
     * @param string $documentType
     * @return Document
     */
    public function setDocumentType($documentType)
    {
        $this->document_type = $documentType;

        return $this;
    }

    /**
     * Get document_type
     *
     * @return string 
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * Set document_date
     *
     * @param \DateTime $documentDate
     * @return Document
     */
    public function setDocumentDate($documentDate)
    {
        $this->document_date = $documentDate;

        return $this;
    }

    /**
     * Get document_date
     *
     * @return \DateTime 
     */
    public function getDocumentDate()
    {
        return $this->document_date;
    }

    /**
     * Set date_created
     *
     * @param \DateTime $dateCreated
     * @return Document
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_modified
     *
     * @param \DateTime $dateModified
     * @return Document
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
     * Set created_by
     *
     * @param \Application\Entity\User $createdBy
     * @return Document
     */
    public function setCreatedBy(\Application\Entity\User $createdBy)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get created_by
     *
     * @return \Application\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set last_modified_by
     *
     * @param \Application\Entity\User $lastModifiedBy
     * @return Document
     */
    public function setLastModifiedBy(\Application\Entity\User $lastModifiedBy)
    {
        $this->last_modified_by = $lastModifiedBy;

        return $this;
    }

    /**
     * Get last_modified_by
     *
     * @return \Application\Entity\User 
     */
    public function getLastModifiedBy()
    {
        return $this->last_modified_by;
    }



    /**
     * Set branch
     *
     * @param \Application\Entity\Branch $branch
     *
     * @return Document
     */
    public function setBranch(\Application\Entity\Branch $branch = null)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \Application\Entity\Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }
}
