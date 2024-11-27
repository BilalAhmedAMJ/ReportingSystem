<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;

use Application\Entity\Branch;

class BranchManagementService implements FactoryInterface{
    
    private $serviceLocator;
    private $entityManager;
    private $entityFactory;
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $this->entityFactory = $serviceLocator->get('entityFactory');
        
        return $this;
    }
    
    public function listBranchNames($branch_ids=null,$filters=array()){
        $branches=$this->listBranches($branch_ids,$filters);
        $values=array();
        foreach ($branches as $branch) {
            $values[' '. $branch->getId()]=$branch->getBranchName();
        }
        return $values;
    }
    
    public function getBranchIdsBySize($size){

        if( $size == 'all_imarat' ) {
            $branch_level = 'Imarat';
            $count = 24;
        }
        else if( $size == 'large' ) {
            $branch_level = 'Jama`at';
            $count = 24;
        }
        else if( $size == 'medium' ) {
            $branch_level = 'Jama`at';
            $count = 12;
        }
        else if( $size == 'small' ) {
            $branch_level = 'Jama`at';
            $count = 8;
        }

        $sql = "SELECT b.id, sum(required) as reports_required 
        FROM required_reports  q
        inner join branches b on q.branch_id = b.id and b.branch_level = '{$branch_level}'
        where b.status = 'active' 
        group by branch_id having sum(required) = {$count}
        ;";
        // echo '<pre>';  echo $sql; die;
        $query = $this->entityManager->createQuery($sql);
        
        $rsm = new ResultSetMapping();            
        $rsm->addScalarResult('id', 'id');

        $query = $this->entityManager->createNativeQuery($sql, $rsm);  
        $data = $query->getResult();
       
        $ids = [];
        foreach($data as $branch) {
            $ids[] = $branch['id'];
        }

        return $ids;
    }
    
    public function listBranches($branch_ids=null,$filters=array()){

        /**
         * @var \Doctrine\ORM\QueryBuilder
         **/        
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('branch')        
           ->from('\Application\Entity\Branch','branch')
           ->where(' branch.status = :bstatus ')
           ->orderBy('branch.branch_name','ASC')
           ;
           
        $qb->setParameter('bstatus', 'active');

           

        if(isset($branch_ids) && is_array($branch_ids)){
            $qb=$qb->andWhere(' branch.id in (:branches)');
            $qb->setParameter('branches', $branch_ids);

        }

        //exlude non property filters
        $branch_obj = $this->entityFactory->getBranch();
        
        //if not filtering by status remove disabled branches
        if(! array_key_exists('status', $filters)){
            $qb=$qb->andWhere(" branch.status = 'active' ");
        }

        foreach ($filters as $key => $value) {
            if(empty($value) || !property_exists($branch_obj,$key) ){
                continue;//exclude non property keys or mpty values
            }    
            $qb=$qb->andWhere(' branch.'.$key.' = :'.$key);
            $qb->setParameter($key, $value);
        }
        // $e = new \Exception();
        // $trace = $e->getTrace();
        // //position 0 would be the line that called this function so we ignore it
        // $last_call = print_r($trace[1],true);
//     
        // error_log('Called with branchId being '.print_r($branch_ids,true)."\n Called by ".$last_call);
        
        $list=$qb->getQuery()->getResult();
        
        return $list;        
    }
    
    public function getBranchByCode($branch_code){        
        $repo = $this->entityManager->getRepository('Application\Entity\Branch');
        return   $repo->findOneBy(array('branch_code'=>$branch_code));
    }
    
    public function getBranch($branch_key){
        
        $branch_field=(is_numeric($branch_key)?'id':'branch_code');
                
        $repo = $this->entityManager->getRepository('Application\Entity\Branch');
        
        return   $repo->findOneBy(array($branch_field=>$branch_key));
    }
    
    public function addOfficeAssignment($username,$branch_code,$department_code,$period_code){
        
        $user = $this->serviceLocator->get('UserProfileService')->getUserByUsername($username);
        $dept = $this->serviceLocator->get('ConfigService')->getDepartment($department_code);
        $branch = $this->getBranchByCode($branch_code);
        $period = $this->serviceLocator->get('ConfigService')->getPeriod($period_code);

        $officeAssignment=$this->entityFactory->getOfficeAssignment();         
        
        $officeAssignment->setBranch($branch);
        $officeAssignment->setPeriodFrom($period);
        $officeAssignment->setDepartment($dept);
                
        $officeAssignment->setUser($user);
        $officeAssignment->setBranch($branch);
        $officeAssignment->setStatus('approved');
        

        $this->entityManager->persist($officeAssignment);
        $user->addOfficeAssignment($officeAssignment);
        $this->entityManager->flush();
        

        return $officeAssignment;
    }
    
    
    public function getChildBranches( $branch, $grandChilden=false){

        $repo = $this->entityManager->getRepository('Application\Entity\Branch');
        
        $branches=   $repo->findBy(array('parent'=>$branch,'status'=>'active'));
        
        //$sql = $this->entityManager->getUnitOfWork()->getEntityPersister('Application\Entity\Branch')->getSelectSQL($criteria, null, 0, null, null,null);

        // var_dump($branch->getId());
        // var_dump(count($branches));

        if( $grandChilden ) {
            $branches = array_merge($branches, $this->getChildBranches($branches));
        }
                
        return $branches;
    }

    /**
     * List branches from IDs sorted appropriately, i.e. Halqa after respective Imarat
     */
    public function getBranchList($branches){
        
        $dql = "select branch 
                from \Application\Entity\Branch branch, 
                     \Application\Entity\Branch psort 
                where branch in (:branches) AND 
                (
                    (branch.branch_level='Halqa' and branch.parent=psort.id) 
                    OR 
                    (branch.branch_level!='Halqa' and branch.id=psort.id)
                )
                order by psort.branch_name, branch.branch_level, branch.branch_name";
        
        $query = $this->entityManager->createQuery($dql);
        $query->setParameter('branches',$branches);
        
        return $query->getResult();
        
    }

}
