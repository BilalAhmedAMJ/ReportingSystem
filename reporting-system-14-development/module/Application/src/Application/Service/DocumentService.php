<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder as QueryBuilder;

use ZfcDatagrid\DataSource\Doctrine2 as GridDataSource;

use Application\Entity\Document;

class DocumentService implements FactoryInterface{
        
    private $serviceLocator;
    /**
     * @var  Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    private $entityFactory;
    
    private $entityService;

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
        $this->entityService = $this->serviceLocator->get('EntityService');
        
        return $this;
    }
            


    public function saveFiles($data, $files_info,$user,$attributes=array('user_type')){
        
        $documents = array();
        
        foreach ($files_info as $file) {
                
            if($file['status'] != 'success'){
                $documents[]=array(
                                'error' => $file['error'],
                                'status' => 'error',
                                'original' =>$file 
                            );
                continue;
            }
            $meta_info=array('name'=>$file['name'],'size'=>$file['size'],'type'=>$file['type']);
            $title = $file['name'];
            $title = str_replace('.'.$file['ext'], '', $title);
            $title = ucwords($title);
            $access_rules = array('access_rule'=>$data['access_rule']);
            foreach ($attributes as $attrib) {
                if(key_exists($attrib, $data) ){
                    $access_rules[$attrib] = $data[$attrib];
                }
            }
            /**
             * @var Application\Entity\Document
             */
            $doc = $this->entityFactory->getDocument();
            
            $doc->setAccessRules(json_encode($access_rules));
            $doc->setDescription(json_encode($meta_info));
            $doc->setCreatedBy($user);
            $doc->setDateModified(new \DateTime());
            $doc->setDocumentDate($doc->getDateModified());
            $doc->setDateCreated($doc->getDateModified());
            $doc->setDocumentExt($file['ext']);
            $doc->setDocumentType($data['document_type']);
            $doc->setExpiryDate(new \DateTime($data['expiry_date']));
            $doc->setFileName($file['saved_as']);
            $doc->setLastModifiedBy($user);
            $doc->setTitle($title);

            if(key_exists('jamaat_halqa', $data)){
                
                $branch = $this->entityService->getObject('Branch',$data['jamaat_halqa']);
                $doc->setBranch($branch);
		//send email notification 
		#$this->uploadNotification($user,$branch);
            }
            
            $documents[]=$doc;
        }
        
        //persist to DB
        $this->entityManager->transactional(function($em) use(&$documents){
           foreach ($documents as $doc) {
               $em->persist($doc);   
           }
           $em->flush();
        });      
     
        return $documents;   
    }
    public function documentsDataSource($user_id,$params=null){
       
        $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
       
        $branches_with_offices = $office_srv->getBranchesWithOffices($user_id);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('document,branch')        
           ->from('\Application\Entity\Document','document')
           ->leftJoin('document.branch','branch')
           ->leftJoin('document.created_by','created_by')
           ->orderBy('document.document_type, document.title')
           ;

       // we don't want ORs as first where clause expr
       // otherwise add on expr might cause issues TODO confirm if we really need this
       $qb->where(' 1= 1 '); 
       
       $types = null;
       if($params==null || !key_exists('types', $params) ){
           $types = array('election_lists','tajnid_lists','mal_report','system_data');
           $qb->where("document.document_type not in (:types) ");
       }else{
           $types = $params['types'];
           $qb->where("document.document_type in (:types) ");
           $branches = split(',',trim($params['branch_id'],','));
           $branches_orX=$qb->expr()->orX();           
           foreach ($branches as $branch) {
              $branches_orX->add($qb->expr()->orX("document.access_rules like '%allowed_jamaat_halqa%branch_id=".$branch.",%'"));
           }
           $qb->andWhere($branches_orX);
       }
       $qb->setParameter('types', $types);

       if( !key_exists('branch_id',$params) ) {
           $qb->andWhere("document.branch is null");
       }
       
       $qb->andWhere(" document.expiry_date > :expiry ");
       $qb->setParameter('expiry', date('Y-m-d G:i:s') );
       
       //Ymd
       //print_r([$qb->getQuery()->getSql(),$types,date("Ymd")]);exit;
        
       $data_source = new GridDataSource($qb);
        
       return $data_source;
   }    

    function setDocumentToExpire($doc_id){
        $document = $this->getDocument($doc_id);
        
        $document->setExpiryDate(new \DateTime());
 
       $this->entityManager->transactional(function($em) use(&$document){
           $em->persist($document);   
           $em->flush();
        });      
                 
    }
    
    function getDocument($id){
            
        $repo = $this->entityManager->getRepository('Application\Entity\Document');
        
        if(! is_int($id)){                       
            $expr = \Doctrine\Common\Collections\Criteria::expr();
            $criteria = \Doctrine\Common\Collections\Criteria::create();
            $criteria->where($expr->eq('id', $id));
            $result = $repo->matching($criteria);
           
           return $result[0];
        }else{
            return $repo->find($id);
        }
    }
	
	function uploadNotification($current_user, $branch,$document){
		$office_srv = $this->serviceLocator->get('OfficeAssignmentService');
		$config_srv = $this->serviceLocator->get('ConfigService');
		
		$national_brnach=$config_srv->getConfig('national_brnach');
		//gs_department, national_brnach
		
		$reciepient = array();
		
		
		if($current_user->hasRole('sys-admin')||$current_user->hasRole('sys-admin')||$current_user->hasRole('national-general-secretary')){
			$reciepient [] = $this->office_srv->getBranchDepartmentOffice($national_brnach,$config_srv->getConfig('gs_department_name'));	
		}else{
			$reciepient [] = $this->office_srv->getBranchDepartmentOffice($branch,$config_srv->getConfig('gs_department_name'));
			$reciepient [] = $this->office_srv->getBranchDepartmentOffice($branch,$config_srv->getConfig('president_department_name'));
		}
		
		if(!empty($reciepient)){
           $email_srv = $this->getServiceLocator()->get('EmailService');
           $subject='AMJ Reports - Document Uploaded';
		   $vars=array('doc'=>$document,'branch'=>$branch);
		   foreach ($reciepient as $office) {
			   $vars['office']=$office;   
			   $email=$office->getUser()->getEmail();
	           $email_srv->sendTemplatedEmail($subject,$email,'document_uploaded',$vars);
		   }
			
		}
		
	}    
}
