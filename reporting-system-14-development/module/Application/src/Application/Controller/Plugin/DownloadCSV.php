<?php

namespace Application\Controller\Plugin;
 
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ReflectionObject;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;

 
/**
 * 
 */ 
class DownloadCSVPlugin extends AbstractPlugin{
    
	
    /**
	 * Expects $data to be an array of hashes
	 * and uses keys for first element as col names   
	 * 
	 */
    public function send($data,$name=null){

		if(!is_array($data) || count($data)<1){
			return null;
		}
		if($name==null){
			$name='amjr_export_'.date('Y_M_d_H_i_s').'.csv';
		}
		
		$fiveMBs=5 * 1024 * 1024;
		$file_h = fopen("php://temp/maxmemory:$fiveMBs", 'w');
		
		//
		$first=true;
		foreach ($data as $row) {
			if($first){
				fputcsv($file_h,array_keys($row), ',','"');
				$first=false;
			}
			fputcsv($file_h,array_values($row), ',','"');
		}
				
		
		$size=ftell($file_h)+1;
		
		rewind($file_h);
			
        $response = new Stream();
        $response->setStream($file_h);
        $response->setStatusCode(200);
        $response->setStreamName($name);
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . ($name) .'"',
            'Content-Type' => 'text/csv',
            'Content-Length' => $size
        ));
        $response->setHeaders($headers);
	
		return $response;	        
    }    
    
}
