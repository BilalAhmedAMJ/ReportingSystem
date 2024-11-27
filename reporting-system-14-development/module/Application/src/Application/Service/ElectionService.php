<?php


namespace Application\Service;

use Application\Entity\Branch;
use Application\Entity\Department;
use Application\Entity\Period;
use Application\Entity\User;


use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

use ZfcDatagrid\DataSource\Doctrine2 as GridDataSource;


class ElectionService implements FactoryInterface{
    
    private $LEVELS=array('national','imarat','jamaat','halqa'); 
    
    private $EMPTY_ARRAY=array('');
	
	private $PROPOSAL_FACTOR=1.5;
    
    private $serviceLocator;
    private $entityManager;
    private $entityFactory;
    private $entityService;
	 private $config;
    private $hydrator;	 
	 
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $this->entityFactory = $this->serviceLocator->get('entityFactory');
        $this->entityService = $this->serviceLocator->get('entityService');
        $this->config = $serviceLocator->get('ConfigService')->getConfig('election_config');
		  
        $this->hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true); 	 	        
		
		
        return $this;
    }


	public function getElection($id,$as_array=false){
		
		$election = $this->entityService->getObject('Election',$id);
        $office_srv = $this->serviceLocator->get('OfficeAssignmentService');		
        
        //FIXME
        //check that user have access to this election
        $can_access = $office_srv->canAccess($election->getBranch(),array(5,26));
        if(!$can_access){
            return null;
        }
		if($as_array){
			$data = $this->hydrator->extract($election);
			$data['election_date']=$data['election_date']->format('d-M-Y');
			$data['branch_id']=$data['branch']->getBranchName();
			return $data;	
		}else{
			return $election;	
		}
								
	} 


     public function getUnsubmittedElection($branch,$term,$type){
            //that's not submitted yet
            return $this->entityService->findOneBy('Election',array('branch'=>$branch,
                                                                        'election_status'=>'draft',
                                                                        'election_type' => $type,
                                                                        'election_term'=>$term));
                                                                            
     } 	 
 	 public function saveElection($election_result) {   

        //getcurrent user
        $user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
		
		if(!key_exists('branch_id', $election_result)){ 
			$election_result['branch_id']=$election_result['election']['branch_id']; 
		}
		if(!key_exists('election_term', $election_result) && key_exists('election', $election_result) 
		   && key_exists('election_term', $election_result['election']) ){
			$election_result['election_term']=$election_result['election']['election_term'];
		}
		
		if(!key_exists('election_date', $election_result) && key_exists('election', $election_result) 
           && key_exists('election_date', $election_result['election']) ){
	        $election_result['election']['election_date'] = new \DateTime($election_result['election']['election_date']);
		}

        //populate object from data values    
        $election_result['branch']= $this->entityService->getObject('Branch',$election_result['branch_id']);
		
		if(key_exists('election_date',$election_result)){
	        $election_result['election_date'] = new \DateTime($election_result['election_date']);
		}
		
        $election_result['modified_by']=$user;

        $election=null;
        if(empty($election_result['election_id'])){
        	
        	if($election_result['user_action']=='create_election'){
        		//try to find election based on branch and term
        		//that's not submitted yet
        		$election = $this->getUnsubmittedElection($election_result['branch'],
        		                  $election_result['election_term'],
        		                  $election_result['election']['election_type']);
        		//if can't find create one
        		if($election==null){	
				    //in case not found create one
	                $election = $this->createElection($election_result);
        		}
        	} 
        }else{
            $election = $this->entityService->getObject('Election',$election_result['election_id']);
            $this->serviceLocator->get('Logger')->err('Got Election:'.$election->getUnderEighteenWassayat().','.$election->getLajnaPayers());
        }
        if($election==null){
            throw new \Exception(sprintf('Invalid election_id [%s] for acton [%s]',$election_result['election_id'],$election_result['user_action']));
        }

		if(!key_exists('election', $election_result)){
			$election_result['election']=$this->hydrator->extract($election);
		}
		if(key_exists('election_date',$election_result['election'])&&is_string($election_result['election']['election_date'])){
	        $election_result['election']['election_date'] = new \DateTime($election_result['election']['election_date']);
		}
        $election_result['election']['modified_by']=$user;

         
		$election_report=null;
		if(key_exists('user_action',$election_result) && in_array($election_result['user_action'], array('save','complete')) ){
			
			$election_report = $this->entityService->getObject('ElectionReport',$election_result['election_report_id']); 
				
			$this->hydrator->hydrate($election_result['election_report'],$election_report);
			
			if(key_exists('election_proposal', $election_result)){
				$election_proposals = $election_report->getElectionProposalsByNum();
				//print_r('<pre>');
				//print_r(array_keys($election_proposals));
				//print_r($election_result['election_proposal']);
				
				foreach ($election_result['election_proposal'] as $ep_data) {
					$proposal=null;
					if(key_exists($ep_data['proposal_number'], $election_proposals)){
						$proposal=$election_proposals[$ep_data['proposal_number']];
						//print_r('Found proposal = > '.$proposal->getId());
					}else{
						$proposal = $this->entityFactory->getElectionProposal();
		                $this->hydrator->hydrate(array('election_report'=>$election_report,'proposal_number'=>$ep_data['proposal_number']),$proposal);
						$election_report->addElectionProposal($proposal);
						$election_proposals[$ep_data['proposal_number']]=$proposal;
					}
					$this->hydrator->hydrate($ep_data,$proposal);
				//	print_r([$proposal->getId(),$proposal->getProposalNumber(),$proposal->getProposedBy(),$proposal->getBranchName()]);
				}
				//print_r('</pre>');
				//exit;

			}
		}
		if(key_exists('user_action',$election_result) && in_array($election_result['user_action'], array('submit')) ){
			$election_result['election']['date_submitted']=new \DateTime();
			$election_result['election']['submitted_by_user_id']=$user;
			
			$this->hydrator->hydrate($election_result['election'],$election);

            $this->serviceLocator->get('Logger')->err('Hydrated Election:'.$election->getUnderEighteenWassayat().','.$election->getLajnaPayers());

		}
		
 		//now set modify tracing
 		$election->setDateModified(new \DateTime());
 		$election->setModifiedBy($user);
 		         
        $this->entityManager->transactional(function($em) use(&$election,&$election_report){
        	$em->persist($election);
			if($election_report){
	        	$em->persist($election_report);
			}
        });
 

        // print_r(array_keys($election_result));
		// print_r(($election_result['election_report']));
		// 		
		// if(key_exists('election_proposal', $election_result))
		// print_r(($election_result['election_proposal']));
		//		
        //exit;
        
        return $election;
 	 }

     public function deleteElection($election_id,$current_user){
         
        $election = $this->getElection($election_id);
        $this->entityManager->transactional(function($em) use(&$election,&$current_user){
                    
                $election->setElectionStatus('deleted');
                $election->setDateModified(new \DateTime());
                $election->setModifiedBy($current_user);
                
                $em->persist($election);   
        });
         
     }
 	 
     public function markExported($election,$status,$current_user){
         
        $elections=$election;
        if(!is_array($election)){
            $elections=array($election);
        }
        
        $this->entityManager->transactional(function($em) use(&$elections,&$status,&$current_user){
            foreach ($elections as $elect) {
                    
                $elect->setElectionStatus($status);
                $elect->setDateModified(new \DateTime());
                $elect->setModifiedBy($current_user);
                $elect->setExportedByUserId($current_user);
                $elect->setDateExported(new \DateTime());
                
                $em->persist($elect);   
            }
        });
         
     }
 	 private function createElection($election_result){

		$election_data=$election_result['election'];
		
		$election_data['branch']=$election_result['branch'];
				
        $election = $this->entityFactory->getElection(); 

		$this->hydrator->hydrate($election_data,$election);
		        
        $election->setDateCreated(new \DateTime());
		
        $election->setCreatedBy($election_result['modified_by']);
		$election->setElectionStatus('draft');
        $branch=$election_result['branch'];

        $dept_ids=$this->config['electable_dept'][$election_data['election_type']][$branch->getBranchLevel()]; 
                
        $depts = $this->entityService->findBy('Department',array('id'=>$dept_ids),array('sort_order'=>'ASC'));
		
		$preset_num_proposals = $this->config['default_proposals'];
		
		if($election_data['election_type'] == 'shura' && key_exists('allowed_delegates', $election_data) && $election_data['allowed_delegates'] > $preset_num_proposals/$this->PROPOSAL_FACTOR ){
			$preset_num_proposals = ceil( $election_data['allowed_delegates'] * $this->PROPOSAL_FACTOR );
		}
		
        if($election_data['election_type'] == 'majlis_intikhab' && key_exists('allowed_electors', $election_data) && $election_data['allowed_electors'] > $preset_num_proposals/$this->PROPOSAL_FACTOR ){
            $preset_num_proposals = ceil( $election_data['allowed_electors'] * $this->PROPOSAL_FACTOR );
        }

        foreach($depts as $dept){
        	
            $election_report = $this->entityFactory->getElectionReport();
            $this->hydrator->hydrate(array('election'=>$election,'department'=>$dept,'report_status'=>'draft',
            							   'eligible_voters_present'=>$election_data['eligible_voters_present'],
            							   'need_introduction'=>( in_array($dept->getId(),$this->config['need_introduction']) )
										  )
									,$election_report);           
            for($i=1;$i<=$preset_num_proposals;$i++){
            	
                $proposal = $this->entityFactory->getElectionProposal();
                $this->hydrator->hydrate(array('election_report'=>$election_report,'proposal_number'=>$i),$proposal);
                $election_report->addElectionProposal($proposal);
                
             }
             $election->addElectionReport($election_report);
                  
        }
        return $election;	 	 
 	 }


    public function electionsDataSource($user,$params=null){
       
        $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
        $branch_srv = $this->serviceLocator->get('BranchManagementService');
       
	   	
        $branches_with_offices = $office_srv->getBranchesWithOffices($user,true);
		$branches=array_keys($branches_with_offices);	
				
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('election,branch,modified_by')        
           ->from('\Application\Entity\Election','election')
           ->innerJoin('election.branch','branch')
           ->leftJoin('election.modified_by','modified_by')
	   ->leftJoin('election.election_reports','election_reports','WITH',"election_reports.report_status = 'completed' ")
	   ->addSelect('sum(ifnull(election_reports.id,0,1)) as election_completed_count')
           ->orderBy('election.date_created')
	   ->groupBy('election')
	   ;

	
       // we don't want ORs as first where clause expr
       // otherwise add on expr might cause issues TODO confirm if we really need this
       $qb->where(" election.election_status != 'deleted' ");
       $qb->andWhere(' election.branch in (:branches)');       
       $qb->setParameter('branches', $branches);
        
       $data_source = new GridDataSource($qb);
        
       return $data_source;
   }

	public function getElections($filters){
	    	    
		$elections = $this->entityService->findBy('Election',$filters);
        return $elections;
	}    

	/**/
    public function validate($election_item){
            return $election_item->validate($this->config['validations']);
    }

	public function getElectionReportsForElection($election_id,$report_status='completed'){
		return $this->entityService->findBy('ElectionReport',array('election'=>$election_id,'report_status'=>$report_status));
	}

	public function getElectionReport($election_report_id){
		return $this->entityService->findOneBy('ElectionReport',array('id'=>$election_report_id));		
	}
	
	public function getElectionConfig(){
		return $this->config;
	}

	public function exportElection($election){
		
		if(is_numeric($election)){
			$election = $this->getElection($election);
		}elseif(!is_object($election)){
			throw new \InvalidArgumentException('Argument must a an election or election id');
		}
		
		//$election_result =  $this->extractElection($election);
		$exported_results=array();
		$electionType = $election->getElectionType();
		foreach ($election->getElectionReports() as $electReport) {
			if( $electReport->getNotApplicable()){ continue; }
			
			foreach ($electReport->getElectionProposals() as $proposal) {
				if( (! $proposal->isEmpty()) ){
                    if($electionType == 'office_bearer'){
                        $exported_results[]=$this->exportProposalOfficeBearer($proposal,$electReport,$election);
                    }elseif($electionType == 'shura'){
                        $exported_results[]=$this->exportProposalShura($proposal,$electReport,$election);
                    }else{
                        $exported_results[]=array('Export for this election type is not implemented '+$electionType);
                    }				    
				}

			}
		}
		
		return $exported_results;
	}
	
    function exportProposalShura($proposal,$report,$election){
        
        $conf = $this->config['export'];
        
        $result = array();
        
        $result['fldShuraApprovalsID']='';
        $result['Shura Yr']=$election->getElectionTerm();
        $result['Election Date']=$election->getElectionDate()->format('d-M-Y');
        $result['Chanda Payer Present']=$report->getEligibleVotersPresent();

        $result['Title']='D';

        $result['M.Code']=$proposal->getMemberCode();
        /*Branch*/        
        $branch_level=$election->getBranch()->getBranchType();
        
        $jamaat='NA';
        if(key_exists($election->getBranch()->getBranchCode(), $conf['branch'])){
            $jamaat=$conf['branch'][$election->getBranch()->getBranchCode()];
        }else{
            $jamaat=$election->getBranch()->getBranchCode();
        }
        
        $result['Jama`at']=$jamaat;        

        $result['Proposed By']=$proposal->getProposedBy();
        $result['Seconded By']=$proposal->getSecondedBy();
        $result['Votes']=$proposal->getVotes();
        $result['Approval Status']='PEN';
        $result['Beard?']=( strtolower($proposal->getHaveBeard()) == 'yes' ? 'TRUE' : 'FALSE' );
        
        $result['Age']='';
        $result['fldAam']='';
        $result['fldJalsaSalana']='';
        $result['fldAux']='';
        $result['fldIjtima']='';        
        $result['Recommendation']=$proposal->getIntroduction();
        $result['fldRecommendedBy']='';

        $result['Comments']='';
        $result['Created By']='';
        $result['Date Created']='';
        $result['Updated By']='';
        $result['Date Updated']='';
        $result['Election Type']='TB';
        
        return $result;
    }
    
	function exportProposalOfficeBearer($proposal,$report,$election){
		
		$conf = $this->config['export'];
		
		$result = array();
		
		$result['fldApprovalsID']='';
		$result['Duration']='';
		
        $branch_level=$election->getBranch()->getBranchType();
		$result['Dept. Lvl.']=$conf['branch_level'][$branch_level];//Branch Level
		
		$result['Dept. Lvl.']=preg_replace('/\`/','',$result['Dept. Lvl.']);
		
		$dept_code=$report->getDepartment()->getDepartmentCode();
		
		$result['fldOFCDE=Department']=$conf['department'][$dept_code][$branch_level];

		$result['M.Code']=$proposal->getMemberCode();
		$result['Beard?']=$proposal->getHaveBeard();


		if(key_exists($election->getBranch()->getBranchCode(), $conf['branch'])){
			$jamaat=$conf['branch'][$election->getBranch()->getBranchCode()];
		}else{
			$jamaat=$election->getBranch()->getBranchCode();
		}
		
		$halqa='NA';
		
		if($result['Dept. Lvl.']=='Halqa'){
			$halqa=$jamaat;
			$parent_branch = $election->getBranch()->getParent();
			$jamaat=$parent_branch?$conf['branch'][$parent_branch->getBranchCode()]:'N/A';
		}else{
		    //remove ` from Jama`at level
		    $result['Dept. Lvl.'] = preg_replace('/`/', '', $result['Dept. Lvl.']); 
		}
        
		$result['Jama`at']=$jamaat;
		$result['Halqa']=$halqa;

		$result['Proposed By']=$proposal->getProposedBy();
		$result['Seconded By']=$proposal->getSecondedBy();
		$result['Votes']=$proposal->getVotes();
		$result['Recommendation']=$proposal->getIntroduction();
		$result['fldRecommendedBy']='';
		$result['Comments']=$report->getComments();

		$result['Approval Status']='';
		$result['Approval Date']='';
		$result['Year']=$election->getElectionTerm();
		$result['Election Date']=$election->getElectionDate()->format('d-M-Y');
		$result['Chanda Payer Present']=$report->getEligibleVotersPresent();

		$result['National Secretary Comments']='';
		$result['Finance Comments']='';
		$result['`Umur `Amma Comments']='';
		$result['Local President Comments']='';	
		$result['fldStatus']='';
		$result['fldApprovalLetterID']='';
		$result['Created By']='';
		$result['Date Created']='';
		$result['Updated By']='';
		$result['Date Updated']='';

		$result['fldEnteredVia']='';
		$result['Approval Category ID']='';


		
		return $result;
	}
	
	function extractElection($election){
		
		$arr = $this->hydrator->extract($election);
		$arr['branch']=$this->hydrator->extract($arr['branch']);
		$election_reports=array();
		foreach ($election->getElectionReports() as $report) {
			$er=$this->hydrator->extract($report);
			$ep=array();
			foreach($report->getElectionProposals() as $proposal){
				$ep[]=$this->hydrator->extract($proposal);				
			}
			$er['election_proposals']=$ep;
			$election_reports[]=$er;
		}
		$arr['election_reports']=$election_reports;
		return $arr;
	}
	
}
