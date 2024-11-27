<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineEncrypt\Configuration\Encrypted;


/**
 * ElectionProposal
 *
 * @ORM\Table(name="election_proposals")
 * @ORM\Entity
 */
class ElectionProposal
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="proposal_number", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $proposal_number;

    /**
     * @var string
     *
     * @ORM\Column(name="member_code", type="string", precision=0, scale=0, nullable=true, unique=false)
     * @Encrypted
     */
    private $member_code;

    /**
     * @var string
     *
     * @ORM\Column(name="proposed_name", type="string", precision=0, scale=0, nullable=true, unique=false)
     * @Encrypted
     */
    private $proposed_name;

    /**
     * @var string
     *
     * @ORM\Column(name="regular_in_prayers", type="string", nullable=true)
     * 
     */
    private $regular_in_prayers;

    /**
     * @var string
     *
     * @ORM\Column(name="basharah_chanda", type="string", nullable=true)
     */
    private $basharah_chanda;

    /**
     * @var string
     *
     * @ORM\Column(name="have_beard", type="string", nullable=true)
     */
    private $have_beard;

    /**
     * @var string
     *
     * @ORM\Column(name="proposed_by", type="string", nullable=true)
     * @Encrypted
     */
    private $proposed_by;

    /**
     * @var string
     *
     * @ORM\Column(name="seconded_by", type="string", nullable=true)
     * @Encrypted
     */
    private $seconded_by;

    /**
     * @var string
     *
     * @ORM\Column(name="branch_name", type="string", nullable=true)
     * @Encrypted
     */
    private $branch_name;


    /**
     * @var integer
     *
     * @ORM\Column(name="votes", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $votes;

    /**
     * @var string
     *
     * @ORM\Column(name="introduction", type="string", nullable=true)
     * @Encrypted
	 * 
     */
    private $introduction;
	

    /**
     * @var \Application\Entity\ElectionReport
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\ElectionReport", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="election_report_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $election_report;



    /**
     * Validates contents of all proposals in current 
     *
    **/    
    public function validate($config){
    
        $result=array();
        
        foreach($config['proposal_validations'] as $validation){
            $var = $validation['name'];
            if(empty($this->$var)){
            	$title=empty($this->member_code)?'':$this->member_code.',';
				$title=empty($this->proposed_name)?'':$this->proposed_name;
				if(empty($title)){
					$title='Proposal Row '.$this->getProposalNumber();
				}
                $result[]=array('type'=>$validation['type'],'message'=>htmlspecialchars($title).': '.$validation['message']);
            }
        }
        return $result;    
    }

	public function isEmpty(){
		return 
			   empty($this->basharah_chanda) 
			&& empty($this->have_beard) 
			&& empty($this->member_code) 
			&& empty($this->proposed_by) 
			&& empty($this->proposed_name) 
			&& empty($this->regular_in_prayers) 
			&& empty($this->seconded_by) 
			&& (empty($this->votes) || $this->votes<1)					
		;
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
     * Set proposalNumber
     *
     * @param integer $proposalNumber
     *
     * @return ElectionProposal
     */
    public function setProposalNumber($proposalNumber)
    {
        $this->proposal_number = $proposalNumber;

        return $this;
    }

    /**
     * Get proposalNumber
     *
     * @return integer
     */
    public function getProposalNumber()
    {
        return $this->proposal_number;
    }

    /**
     * Set memberCode
     *
     * @param string $memberCode
     *
     * @return ElectionProposal
     */
    public function setMemberCode($memberCode)
    {
        $this->member_code = $memberCode;

        return $this;
    }

    /**
     * Get memberCode
     *
     * @return string
     */
    public function getMemberCode()
    {
        return $this->member_code;
    }

    /**
     * Set proposedName
     *
     * @param string $proposedName
     *
     * @return ElectionProposal
     */
    public function setProposedName($proposedName)
    {
        $this->proposed_name = $proposedName;

        return $this;
    }

    /**
     * Get proposedName
     *
     * @return string
     */
    public function getProposedName()
    {
        return $this->proposed_name;
    }

    /**
     * Set regularInPrayers
     *
     * @param string $regularInPrayers
     *
     * @return ElectionProposal
     */
    public function setRegularInPrayers($regularInPrayers)
    {
        $this->regular_in_prayers = $regularInPrayers;

        return $this;
    }

    /**
     * Get regularInPrayers
     *
     * @return string
     */
    public function getRegularInPrayers()
    {
        return $this->regular_in_prayers;
    }

    /**
     * Set basharahChanda
     *
     * @param string $basharahChanda
     *
     * @return ElectionProposal
     */
    public function setBasharahChanda($basharahChanda)
    {
        $this->basharah_chanda = $basharahChanda;

        return $this;
    }

    /**
     * Get basharahChanda
     *
     * @return string
     */
    public function getBasharahChanda()
    {
        return $this->basharah_chanda;
    }

    /**
     * Set haveBeard
     *
     * @param string $haveBeard
     *
     * @return ElectionProposal
     */
    public function setHaveBeard($haveBeard)
    {
        $this->have_beard = $haveBeard;

        return $this;
    }

    /**
     * Get haveBeard
     *
     * @return string
     */
    public function getHaveBeard()
    {
        return $this->have_beard;
    }

    /**
     * Set proposedBy
     *
     * @param string $proposedBy
     *
     * @return ElectionProposal
     */
    public function setProposedBy($proposedBy)
    {
        $this->proposed_by = $proposedBy;

        return $this;
    }

    /**
     * Get proposedBy
     *
     * @return string
     */
    public function getProposedBy()
    {
        return $this->proposed_by;
    }

    /**
     * Set secondedBy
     *
     * @param string $secondedBy
     *
     * @return ElectionProposal
     */
    public function setSecondedBy($secondedBy)
    {
        $this->seconded_by = $secondedBy;

        return $this;
    }

    /**
     * Get secondedBy
     *
     * @return string
     */
    public function getSecondedBy()
    {
        return $this->seconded_by;
    }


    /**
     * Set branchName
     *
     * @param string $branchName
     *
     * @return ElectionProposal
     */
    public function setBranchName($branchName)
    {
        $this->branch_name = $branchName;

        return $this;
    }

    /**
     * Get branchName
     *
     * @return string
     */
    public function getBranchName()
    {
        return $this->branch_name;
    }
    




    /**
     * Set votes
     *
     * @param integer $votes
     *
     * @return ElectionProposal
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer
     */
    public function getVotes()
    {
        return $this->votes;
    }


    /**
     * Set introduction
     *
     * @param string $introduction
     *
     * @return ElectionProposal
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;

        return $this;
    }

    /**
     * Get introduction
     *
     * @return string
     */
    public function getTntroduction()
    {
        return $this->introduction;
    }


    /**
     * Set electionReport
     *
     * @param \Application\Entity\ElectionReport $electionReport
     *
     * @return ElectionProposal
     */
    public function setElectionReport(\Application\Entity\ElectionReport $electionReport)
    {
        $this->election_report = $electionReport;

        return $this;
    }

    /**
     * Get electionReport
     *
     * @return \Application\Entity\ElectionReport
     */
    public function getElectionReport()
    {
        return $this->election_report;
    }

    /**
     * Get introduction
     *
     * @return string
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }
}
