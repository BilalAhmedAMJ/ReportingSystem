<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ReflectionObject;
use Zend\Form\Form;
use Zend\View\Model\ModelInterface;
use Zend\Form\Element;
use Zend\Form\MonthSelect;

 
/**
 * 
 */ 
class ListFilterFormPlugin extends AbstractPlugin{
    
    
    public function form(){

        $user = $this->getController()->zfcUserAuthentication()->getIdentity(); 
        
        $form = new Form();           

        $configSrv = $this->getServiceLocator()->get('ConfigService');
        
        $monthYear = new Element\MonthSelect('monthyear');
        $monthYear->setMinYear(2008);
        $monthYear->setShouldRenderDelimiters(false);
        $monthYear->getMonthElement()->setValueOptions($configSrv->getConfig('months'));
        $monthYear->getMonthElement()->setEmptyOption('All');
        $monthYear->getYearElement()->setEmptyOption('All');
        $form->add($monthYear);
        
        $officeSrv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $offices=$officeSrv->getBranchesWithOffices($user->getId());
                
        $branchSrv = $this->getServiceLocator()->get('BranchManagementService');
        $branch_ids = array_keys($offices);
        
        if($user->hasRole('sys-admin')||$user->hasRole('national-general-secretary')){
            $branch_ids = null;    
        }
        
        $branches = $branchSrv->listBranchNames($branch_ids);
        
        $branches = array_values($branches);
        $branches = array_combine($branches,$branches);
        
        
        $dept_ids=array();
        foreach($offices as $b=>$depts){
            $dept_ids=array_merge($dept_ids,$depts);
        }
        $dept_ids=(array_unique($dept_ids));

        if($user->hasRole('sys-admin')||$user->hasRole('national-general-secretary')){
            $dept_ids = null;    
        }
        
        $departments = $configSrv->listDepartmentNames($dept_ids);
        //$departments = array_values($configSrv->listDepartmentNames(array_values($offices)));
        $departments = array_combine($departments,$departments);

        $form->setOption('branches',$branches);
                
        $form->setOption('departments',$departments);
                
        $post = $this->getController()->params()->fromPost();
        
        if($this->getController()->getRequest()->isPost() ){
            $form->setData($post);
        }
        
        $form->prepare();

        return $form;
    }    
    
    private function getServiceLocator(){
        return $this->getController()->getServiceLocator();
    }
    public function __invoke(){
        
        return $this->form();
    }
}
