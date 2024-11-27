<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\MaxDepth;

use DoctrineEncrypt\Configuration\Encrypted;

 
 /**
 *
 * An entire election for a Jamaat or Halqa with all departments included
 * 
 * @ORM\Entity
 * @ORM\Table(name="elections")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class Election 
{
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * 
     * @var date
     * @ORM\Column(type="date", nullable=false)
     */
    protected $election_date;

    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="Application\Entity\Branch",fetch="EAGER")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=false)
     */
    protected  $branch;


    /**
     * 
     * @var string
     * @ORM\Column(type="string")
     */
    protected   $election_term;


    /**
     * 
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected   $chanda_payers;
    
        /**
     * 
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected  $defaulters;


    /**
     * Election can be be any one of election types e.g. [office_bearer, shura] 
     * @var string
     * @ORM\Column(type="string", nullable=false,options="default='office_bearer'")
     */
    protected $election_type;     
    
    
    /**
     * Lajna chanda payers required for Shura elections 
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lajna_payers;    
    
    /**
     * Under 18 Moosi and Moosiyat required for Shura elections 
     * @var string
     * @ORM\Column(name="under_18_wassayat",type="integer", nullable=true)
     */
    private $underEighteenWassayat;

    /**
     * Allowed delegates for Majlis Shura 
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $allowed_delegates;

    /**
     * Allowed members of Majlis Intikhab (electors)
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $allowed_electors;


    /**
     * Chanda payers over 60 years of age  
     * @var string
     * @ORM\Column(type="integer", nullable=true)
     */
    private $over_60_payers;



    /**
     * 
     * @var integer
     * @ORM\Column(type="integer")
     */     
    protected  $eligible_voters_present ;

    /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected   $election_call;
    
   /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     *
     */
    protected   $presided_by;
    
   /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected   $presiding_officer_comments ;

   /**
     * document properties of attached eligible voters list 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */

    protected  $eligible_voters_list ;

   /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     *
     */
    protected  $witness_name_one ;
       
   /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     *
     */
    protected  $witness_phone_one ;


   /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     *
    */
    protected   $witness_name_two ;

    /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     *
    */
    protected   $witness_phone_two ;
    
   /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected   $election_status;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $created_by;   

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @MaxDepth(1)
     */
    protected $date_created;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $modified_by;   

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @MaxDepth(1)
     */
    protected $date_modified;

	/**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Application\Entity\ElectionReport", 
     *                 mappedBy="election", cascade={"persist"}, orphanRemoval=true,fetch="EAGER")
     * @ORM\JoinTable(name="election_reports",
     *                joinColumns={@ORM\JoinColumn(name="election_id", referencedColumnName="id")},
     *               )
     */
    protected $election_reports;



    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="submitted_by_user_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $submitted_by_user_id;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @MaxDepth(1)
     */
    protected $date_submitted;
	
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="exported_by_user_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $exported_by_user_id;

    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @MaxDepth(1)
     */
    protected $date_exported;
    
    
    /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
	 * 
    */
    protected $completed_by_name;
	
    /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
	 * 
    */
    protected $completed_by_number;


    /**
     * 
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
	 * 
    */
    protected $election_location;

	
	    
    public function __toString(){
        return sprintf("id=%s,date=%s,presided_by=%s",
        					  $this->id,$this->election_date,($this->presided_by?$this->presided_by:'NULL'));
    }

    public function validate($config){
    
        $result=array();
		
		//validate results for all dept
		$summary=array('type'=>'summary','error'=>0,'warning'=>0,'empty'=>0);
		
        foreach($this->getElectionReports() as $report){
        	$empty=$report->isEmpty();
            $rep_result = $report->validate($config);
            if(!$empty && !empty($rep_result)){
                $result = array_merge($result,$rep_result);
				if(key_exists('error', $rep_result[0])){
					$summary['error']=$summary['error']+$rep_result[0]['error'];
				}
				if(key_exists('warning', $rep_result[0])){
					$summary['warning']=$summary['warning']+$rep_result[0]['warning'];
				}
            }
			if($empty){
				$summary['empty']=$summary['empty']+1;
			}
        }
        return array_merge(array($summary),$result);
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
     * Set electionDate
     *
     * @param \DateTime $electionDate
     *
     * @return Election
     */
    public function setElectionDate($electionDate)
    {
        $this->election_date = $electionDate;

        return $this;
    }

    /**
     * Get electionDate
     *
     * @return \DateTime
     */
    public function getElectionDate()
    {
        return $this->election_date;
    }

    /**
     * Set chandaPayers
     *
     * @param integer $chandaPayers
     *
     * @return Election
     */
    public function setChandaPayers($chandaPayers)
    {
        $this->chanda_payers = $chandaPayers;

        return $this;
    }

    /**
     * Get chandaPayers
     *
     * @return integer
     */
    public function getChandaPayers()
    {
        return $this->chanda_payers;
    }

    /**
     * Set defaulters
     *
     * @param integer $defaulters
     *
     * @return Election
     */
    public function setDefaulters($defaulters)
    {
        $this->defaulters = $defaulters;

        return $this;
    }

    /**
     * Get defaulters
     *
     * @return integer
     */
    public function getDefaulters()
    {
        return $this->defaulters;
    }

    /**
     * Set eligibleVotersPresent
     *
     * @param integer $eligibleVotersPresent
     *
     * @return Election
     */
    public function setEligibleVotersPresent($eligibleVotersPresent)
    {
        $this->eligible_voters_present = $eligibleVotersPresent;

        return $this;
    }

    /**
     * Get eligibleVotersPresent
     *
     * @return integer
     */
    public function getEligibleVotersPresent()
    {
        return $this->eligible_voters_present;
    }

    /**
     * Set electionCall
     *
     * @param string $electionCall
     *
     * @return Election
     */
    public function setElectionCall($electionCall)
    {
        $this->election_call = $electionCall;

        return $this;
    }

    /**
     * Get electionCall
     *
     * @return string
     */
    public function getElectionCall()
    {
        return $this->election_call;
    }

    /**
     * Set presidedBy
     *
     * @param string $presidedBy
     *
     * @return Election
     */
    public function setPresidedBy($presidedBy)
    {
        $this->presided_by = $presidedBy;

        return $this;
    }

    /**
     * Get presidedBy
     *
     * @return string
     */
    public function getPresidedBy()
    {
        return $this->presided_by;
    }

    /**
     * Set presidingOfficerComments
     *
     * @param string $presidingOfficerComments
     *
     * @return Election
     */
    public function setPresidingOfficerComments($presidingOfficerComments)
    {
        $this->presiding_officer_comments = $presidingOfficerComments;

        return $this;
    }

    /**
     * Get presidingOfficerComments
     *
     * @return string
     */
    public function getPresidingOfficerComments()
    {
        return $this->presiding_officer_comments;
    }

    /**
     * Set eligibleVotersList
     *
     * @param string $eligibleVotersList
     *
     * @return Election
     */
    public function setEligibleVotersList($eligibleVotersList)
    {
        $this->eligible_voters_list = $eligibleVotersList;

        return $this;
    }

    /**
     * Get eligibleVotersList
     *
     * @return string
     */
    public function getEligibleVotersList()
    {
        return $this->eligible_voters_list;
    }

    /**
     * Set witnessNameOne
     *
     * @param string $witnessNameOne
     *
     * @return Election
     */
    public function setWitnessNameOne($witnessNameOne)
    {
        $this->witness_name_one = $witnessNameOne;

        return $this;
    }

    /**
     * Get witnessNameOne
     *
     * @return string
     */
    public function getWitnessNameOne()
    {
        return $this->witness_name_one;
    }

    /**
     * Set witnessPhoneOne
     *
     * @param string $witnessPhoneOne
     *
     * @return Election
     */
    public function setWitnessPhoneOne($witnessPhoneOne)
    {
        $this->witness_phone_one = $witnessPhoneOne;

        return $this;
    }

    /**
     * Get witnessPhoneOne
     *
     * @return string
     */
    public function getWitnessPhoneOne()
    {
        return $this->witness_phone_one;
    }

    /**
     * Set witnessNameTwo
     *
     * @param string $witnessNameTwo
     *
     * @return Election
     */
    public function setWitnessNameTwo($witnessNameTwo)
    {
        $this->witness_name_two = $witnessNameTwo;

        return $this;
    }

    /**
     * Get witnessNameTwo
     *
     * @return string
     */
    public function getWitnessNameTwo()
    {
        return $this->witness_name_two;
    }

    /**
     * Set witnessPhoneTwo
     *
     * @param string $witnessPhoneTwo
     *
     * @return Election
     */
    public function setWitnessPhoneTwo($witnessPhoneTwo)
    {
        $this->witness_phone_two = $witnessPhoneTwo;

        return $this;
    }

    /**
     * Get witnessPhoneTwo
     *
     * @return string
     */
    public function getWitnessPhoneTwo()
    {
        return $this->witness_phone_two;
    }

    /**
     * Set electionStatus
     *
     * @param string $electionStatus
     *
     * @return Election
     */
    public function setElectionStatus($electionStatus)
    {
        $this->election_status = $electionStatus;

        return $this;
    }

    /**
     * Get electionStatus
     *
     * @return string
     */
    public function getElectionStatus()
    {
        return $this->election_status;
    }

    /**
     * Set branch
     *
     * @param \Application\Entity\Branch $branch
     *
     * @return Election
     */
    public function setBranch(\Application\Entity\Branch $branch)
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

    /**
     * Set electionTerm
     *
     * @param string $electionTerm
     *
     * @return Election
     */
    public function setElectionTerm($electionTerm)
    {
        $this->election_term = $electionTerm;

        return $this;
    }

    /**
     * Get electionTerm
     *
     * @return string
     */
    public function getElectionTerm()
    {
        return $this->election_term;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Election
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     *
     * @return Election
     */
    public function setDateModified($dateModified)
    {
        $this->date_modified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\User $createdBy
     *
     * @return Election
     */
    public function setCreatedBy(\Application\Entity\User $createdBy)
    {
        $this->created_by = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set modifiedBy
     *
     * @param \Application\Entity\User $modifiedBy
     *
     * @return Election
     */
    public function setModifiedBy(\Application\Entity\User $modifiedBy)
    {
        $this->modified_by = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \Application\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->election_reports = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add electionReport
     *
     * @param \Application\Entity\ElectionReport $electionReport
     *
     * @return Election
     */
    public function addElectionReport(\Application\Entity\ElectionReport $electionReport)
    {
        $this->election_reports[] = $electionReport;

        return $this;
    }

    /**
     * Remove electionReport
     *
     * @param \Application\Entity\ElectionReport $electionReport
     */
    public function removeElectionReport(\Application\Entity\ElectionReport $electionReport)
    {
        $this->election_reports->removeElement($electionReport);
    }

    /**
     * Get electionReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getElectionReports()
    {
        return $this->election_reports;
    }

    /**
     * Set dateSubmitted
     *
     * @param \DateTime $dateSubmitted
     *
     * @return Election
     */
    public function setDateSubmitted($dateSubmitted)
    {
        $this->date_submitted = $dateSubmitted;

        return $this;
    }

    /**
     * Get dateSubmitted
     *
     * @return \DateTime
     */
    public function getDateSubmitted()
    {
        return $this->date_submitted;
    }

    /**
     * Set dateExported
     *
     * @param \DateTime $dateExported
     *
     * @return Election
     */
    public function setDateExported($dateExported)
    {
        $this->date_exported = $dateExported;

        return $this;
    }

    /**
     * Get dateExported
     *
     * @return \DateTime
     */
    public function getDateExported()
    {
        return $this->date_exported;
    }

    /**
     * Set completedByName
     *
     * @param string $completedByName
     *
     * @return Election
     */
    public function setCompletedByName($completedByName)
    {
        $this->completed_by_name = $completedByName;

        return $this;
    }

    /**
     * Get completedByName
     *
     * @return string
     */
    public function getCompletedByName()
    {
        return $this->completed_by_name;
    }

    /**
     * Set completedByNumber
     *
     * @param string $completedByNumber
     *
     * @return Election
     */
    public function setCompletedByNumber($completedByNumber)
    {
        $this->completed_by_number = $completedByNumber;

        return $this;
    }

    /**
     * Get completedByNumber
     *
     * @return string
     */
    public function getCompletedByNumber()
    {
        return $this->completed_by_number;
    }

    /**
     * Set submittedByUserId
     *
     * @param \Application\Entity\User $submittedByUserId
     *
     * @return Election
     */
    public function setSubmittedByUserId(\Application\Entity\User $submittedByUserId)
    {
        $this->submitted_by_user_id = $submittedByUserId;

        return $this;
    }

    /**
     * Get submittedByUserId
     *
     * @return \Application\Entity\User
     */
    public function getSubmittedByUserId()
    {
        return $this->submitted_by_user_id;
    }

    /**
     * Set exportedByUserId
     *
     * @param \Application\Entity\User $exportedByUserId
     *
     * @return Election
     */
    public function setExportedByUserId(\Application\Entity\User $exportedByUserId)
    {
        $this->exported_by_user_id = $exportedByUserId;

        return $this;
    }

    /**
     * Get exportedByUserId
     *
     * @return \Application\Entity\User
     */
    public function getExportedByUserId()
    {
        return $this->exported_by_user_id;
    }
	
	
    /**
     * Set electionLocation
     *
     * @param string $electionLocation
     *
     * @return Election
     */
    public function setElectionLocation($electionLocation)
    {
        $this->election_location = $electionLocation;

        return $this;
    }

    /**
     * Get electionLocation
     *
     * @return string
     */
    public function getElectionLocation()
    {
        return $this->election_location;
    }
	

    /**
     * Set electionType
     *
     * @param string $electionType
     *
     * @return Election
     */
    public function setElectionType($electionType)
    {
        $this->election_type = $electionType;

        return $this;
    }

    /**
     * Get electionType
     *
     * @return string
     */
    public function getElectionType()
    {
        return $this->election_type;
    }

    /**
     * Set lajnaPayers
     *
     * @param integer $lajnaPayers
     *
     * @return Election
     */
    public function setLajnaPayers($lajnaPayers)
    {
        $this->lajna_payers = $lajnaPayers;

        return $this;
    }

    /**
     * Get lajnaPayers
     *
     * @return integer
     */
    public function getLajnaPayers()
    {
        return $this->lajna_payers;
    }

    /**
     * Set underEighteenWassayat
     *
     * @param integer $underEighteenWassayat
     *
     * @return Election
     */
    public function setUnderEighteenWassayat($under18Wassayat)
    {
        $this->underEighteenWassayat = $under18Wassayat;

        return $this;
    }

    /**
     * Get underEighteenWassayat
     *
     * @return integer
     */
    public function getUnderEighteenWassayat()
    {
        return $this->underEighteenWassayat;
    }

    /**
     * Set allowedDelegates
     *
     * @param integer $allowedDelegates
     *
     * @return Election
     */
    public function setAllowedDelegates($allowedDelegates)
    {
        $this->allowed_delegates = $allowedDelegates;

        return $this;
    }

    /**
     * Get allowedDelegates
     *
     * @return integer
     */
    public function getAllowedDelegates()
    {
        return $this->allowed_delegates;
    }

    /**
     * Set allowedElectors
     *
     * @param integer $allowedElectors
     *
     * @return Election
     */
    public function setAllowedElectors($allowedElectors)
    {
        $this->allowed_electors = $allowedElectors;

        return $this;
    }

    /**
     * Get allowedElectors
     *
     * @return integer
     */
    public function getAllowedElectors()
    {
        return $this->allowed_electors;
    }

    /**
     * Set over60Payers
     *
     * @param integer $over60Payers
     *
     * @return Election
     */
    public function setOver60Payers($over60Payers)
    {
        $this->over_60_payers = $over60Payers;

        return $this;
    }

    /**
     * Get over60Payers
     *
     * @return integer
     */
    public function getOver60Payers()
    {
        return $this->over_60_payers;
    }
}
