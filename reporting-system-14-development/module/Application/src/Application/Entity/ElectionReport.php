<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;

use DoctrineEncrypt\Configuration\Encrypted;

 
 /**
 *
 * An election for a single department, it is part of Election 
 * 
 * @ORM\Entity
 * @ORM\Table(name="election_reports")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class ElectionReport 
{
    
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var Election
     * @ORM\ManyToOne(targetEntity="Application\Entity\Election",fetch="EAGER")
     * @ORM\JoinColumn(name="election_id", referencedColumnName="id", nullable=false)
     */
    protected $election;     

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Application\Entity\Department",fetch="EAGER")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", nullable=false)
     */
    protected $department;     


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Application\Entity\ElectionProposal", 
     *                 mappedBy="election_report", cascade={"persist"}, orphanRemoval=true,fetch="EAGER")
     * @ORM\JoinTable(name="election_proposals",
     *                joinColumns={@ORM\JoinColumn(name="election_report_id", referencedColumnName="id")},
     *               )
     */
    protected $election_proposals;
    
    
    /**
     * Present voters
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $eligible_voters_present;     
    
    /**
     * Some branches may not have election for a department based on instructions from Markaz, in that case it needs to be not_applicable
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $not_applicable;     
    
    /**
     * Incase there were warnings user acknowledged those
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $warnings_acknowledged;     
    
    /**
     * copy of warnings that user acknowledged
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Encrypted
     */
    protected $warnings;     
	
    /**
     * Comments of presiding officer
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Encrypted
	 * 
	 */
    protected $comments;     
	
    /**
     * will be true if all proposed names on this election report require an introduction
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $need_introduction;     


    /**
     * Report is complete vs still in progress (need to add more proposals) 
     * @var string
     * @ORM\Column(type="string", nullable=false,options="default='draft'")
     */
    protected $report_status;     
	
	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->election_proposals = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    private $VALIDATE_VOTE_MESSAGE='Expected Votes don\'t match Reported Votes (%s vs %s)';
    private $NO_VOTE_MESSAGE='Eligible voters are %s but no votes are (%s) reported';
    /**
     * Validates contents of all proposals in current 
     *
    **/    
    public function validate($config){
    
        $result=array();
		$empty = $this->isEmpty(); 
        if($empty){
            $result[] = array('type'=>'error','message'=>'No results entered for '.$this->getOfficeTitle());
        }elseif($this->eligible_voters_present<1 && !($this->not_applicable)){
        	$result[] = array('type'=>'error','message'=>'Results are entered however voters count before election is not provided.');
        }

        $total_votes=0;
		$non_empty_proposals=0;
        		
		if(!$empty && !($this->getNotApplicable())){			
	        foreach($this->getElectionProposals() as $proposal){
				
	        	if($proposal->isEmpty()){
	        		continue;//no need to validate empty proposal
	        				 //there is a separate validation to make sure at least one propsoal is not empty
	        	}
				$non_empty_proposals++;
	            $total_votes += $proposal->getVotes();
	            $proposal_result=$proposal->validate($config);
	            //if result is empty no need to report it
	            if(!empty($proposal_result)){
	                $result=array_merge($result,$proposal_result);
	            }
	        }
		}

        /* Validate for minimum proposals*/
		$min_propsals_required=$config['min_proposals']['required']['default'];
		
		if($this->election->getElectionType() != 'shura' &&
		  key_exists($this->department->getId().'',$config['min_proposals']['required'] )){
			$min_propsals_required=$config['min_proposals']['required'][$this->department->getId().''];
		}

        if($this->election->getElectionType() == 'shura' ){
            $min_propsals_required = $this->election->getAllowedDelegates();
        }

        if($this->election->getElectionType() == 'majlis_intikhab' ){
            $min_propsals_required = $this->election->getAllowedElectors();
        }

		if(!$empty && !($this->getNotApplicable()) && $non_empty_proposals < $min_propsals_required){
			$result=array_merge(array( 
									array('type'=>$config['min_proposals']['type'],
            			  			'message'=>sprintf($config['min_proposals']['message'],$min_propsals_required )
									)
								),
						  		$result 
				 );				
		}
		
        /* All proposals should have votes */
		if(!$empty && !($this->getNotApplicable()) && $total_votes==0 && $this->getEligibleVotersPresent()>0){
				
			$result=array_merge(array(array('type'=>'error',
            			  			'message'=>sprintf($this->NO_VOTE_MESSAGE, $this->getEligibleVotersPresent(),$total_votes) 
						  		)),
						  		$result 
				 );				
		}
        
        /* Validate for vote count */
        $total_possible_votes=$this->getEligibleVotersPresent();
        if($this->election->getElectionType() == 'majlis_intikhab'){
            //for shura voters are allowed to vote for each delegate
            $total_possible_votes = $total_possible_votes * $this->election->getAllowedElectors();
        }
        
        if($this->election->getElectionType() == 'shura'){
            //for shura voters are allowed to vote for each delegate
            $total_possible_votes = $total_possible_votes * $this->election->getAllowedDelegates();
        }

        if(!$empty && !($this->getNotApplicable()) && $total_votes!= $total_possible_votes){
            $result=array_merge($result,
            					array(array('type'=>$config['vote_match']['type'],
		            			  'message'=>sprintf($this->VALIDATE_VOTE_MESSAGE, $total_possible_votes,$total_votes) 
								))								 
						 );
        }
		
        /* Summarize warnings and errors*/
		$summary=array('type'=>'summary','error'=>0,'warning'=>0);		
		foreach ($result as $validation) {
			if(is_array($validation)&&key_exists('type', $validation)){
				$summary[$validation['type']]=$summary[$validation['type']]+1;
			}
		}
		
		//if there are warnings and warnings are not acknowledged it's a validation error
		if($summary['warning']>0 && (! $this->warnings_acknowledged) ){
			$summary['error']=$summary['error']+1;
			$result=array_merge($result,
            					array(array('type'=>'error',
		            			  'message'=>'There are un-acknowledged warnings, please review and fix or acknowledge' 
								))								 
						 );

		}
		
		$summary['message']=sprintf('Validation results for %s: There are %s error(s) and %s warning(s)',$this->getOfficeTitle(),$summary['error'],$summary['warning']);
		
        $result = array_merge(array($summary),$result);
		
		return $result;    
    }
	
	public function isEmpty(){
		$empty = empty($this->not_applicable) || !($this->not_applicable=='1'||$this->not_applicable=='on'||$this->not_applicable=='yes');
		
		if($empty){
			foreach ($this->getElectionProposals() as $ep) {
				$empty = $empty && $ep->isEmpty(); 
			}
		}
		return $empty;	
	}
	
	public function getElectionProposalsByNum(){
		$election_proposals_by_num=array();
		
		foreach ($this->election_proposals as $ep) {
			$election_proposals_by_num[$ep->getProposalNumber()]=$ep;
		}
		
		return $election_proposals_by_num;
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
     * Set eligibleVotersPresent
     *
     * @param integer $eligibleVotersPresent
     *
     * @return ElectionReport
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
     * Set notApplicable
     *
     * @param boolean $notApplicable
     *
     * @return ElectionReport
     */
    public function setNotApplicable($notApplicable)
    {
        $this->not_applicable = $notApplicable;

        return $this;
    }

    /**
     * Get notApplicable
     *
     * @return boolean
     */
    public function getNotApplicable()
    {
        return $this->not_applicable;
    }

    /**
     * Set election
     *
     * @param \Application\Entity\Election $election
     *
     * @return ElectionReport
     */
    public function setElection(\Application\Entity\Election $election)
    {
        $this->election = $election;

        return $this;
    }

    /**
     * Get election
     *
     * @return \Application\Entity\Election
     */
    public function getElection()
    {
        return $this->election;
    }

    /**
     * Set department
     *
     * @param \Application\Entity\Department $department
     *
     * @return ElectionReport
     */
    public function setDepartment(\Application\Entity\Department $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Add electionProposal
     *
     * @param \Application\Entity\ElectionProposal $electionProposal
     *
     * @return ElectionReport
     */
    public function addElectionProposal(\Application\Entity\ElectionProposal $electionProposal)
    {
        $this->election_proposals[] = $electionProposal;

        return $this;
    }

    /**
     * Remove electionProposal
     *
     * @param \Application\Entity\ElectionProposal $electionProposal
     */
    public function removeElectionProposal(\Application\Entity\ElectionProposal $electionProposal)
    {
        $this->election_proposals->removeElement($electionProposal);
    }

    /**
     * Get electionProposals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getElectionProposals()
    {
        return $this->election_proposals;
    }
	
	
	public function getReportStatus(){
		return $this->report_status;
	}

	public function setReportStatus($rs){
		$this->report_status=$rs;
	}

	
	 
	public function getOfficeTitle(){
        $branch=$this->election->getBranch();
		$title='';
        if(in_array($this->department->getId(), array(26,27)) ){
        	//TODO 
        	//FIXME
        	//UGLY
        	//find a way to figure out if dept is needs per branch level title
        	$title=$branch->getBranchHeadTitle();
        	if($this->getDepartment()->getId()==27 && in_array($branch->getBranchLevel(),array('Imarat','Markaz')) ) {
        		$title='Na`ib '.$title;
        	}elseif($this->getDepartment()->getId()==27){
        		$title=$this->department->getDepartmentName();
        	}
        }else{
        	$title=$this->department->getDepartmentName();
        }
		
		return $title;
	}

    /**
     * Set warningsAcknowledged
     *
     * @param boolean $warningsAcknowledged
     *
     * @return ElectionReport
     */
    public function setWarningsAcknowledged($warningsAcknowledged)
    {
        $this->warnings_acknowledged = $warningsAcknowledged;

        return $this;
    }

    /**
     * Get warningsAcknowledged
     *
     * @return boolean
     */
    public function getWarningsAcknowledged()
    {
        return $this->warnings_acknowledged;
    }

    /**
     * Set warnings
     *
     * @param string $warnings
     *
     * @return ElectionReport
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * Get warnings
     *
     * @return string
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return ElectionReport
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set needIntroduction
     *
     * @param boolean $needIntroduction
     *
     * @return ElectionReport
     */
    public function setNeedIntroduction($needIntroduction)
    {
        $this->need_introduction = $needIntroduction;

        return $this;
    }

    /**
     * Get needIntroduction
     *
     * @return boolean
     */
    public function getNeedIntroduction()
    {
        return $this->need_introduction;
    }
}
