<?php

namespace Application\Util;

use Zend\Crypt\BlockCipher;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Zend\Http\Headers;
use Zend\Http\Response\Stream;


use Rhumsaa\Uuid\Uuid;


class DocumentUtil implements FactoryInterface{
    
    
    const UPLOAD_TYPE_DOCUMENT='document';
    const UPLOAD_TYPE_ATTACHMENT='attachment';
    
    private $serviceLocator;
    
    private $blockCipher;
    
    private $uploads_dir;
    
    private $encryption_adapter;
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
     
    private $_CHUNK_SIZE;
    private $_CHUNK_SIZE_DOUBLE;
          
    public function createService(ServiceLocatorInterface $serviceLocator){       

        $this->serviceLocator = $serviceLocator;  

        $this->blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $this->blockCipher->setKey($this->getKey());

        $config = $serviceLocator->get('config');
        $this->uploads_dir = $config['application']['uploads_dir'];
        
        //to simplify encryption config setup we will use doctrine-encrypt-module config
        //it causes an undue dependency on doctrine-encrypt-module, 
        //however, it might be better than repeating config        
        $this->encryption_adapter = $serviceLocator->get('DoctrineEncryptAdapter'); 
        //$config['doctrine']['encryption']['orm_default']['adapter']();
        
        $this->_CHUNK_SIZE = 1024*1024; // 1MB
        $this->_CHUNK_SIZE_DOUBLE = 2*1024*1024; // 2MB
        
        return $this;
    }    
    
    public function encrypt($data){
        
        $result = $this->blockCipher->encrypt($data);
        
        return $result;
    }


    public function decrypt($data){
        
        $result = $this->blockCipher->decrypt($data);
        
        return $result;
    }

    private function getKey(){
        if( getenv('APPLICATION_ENV') == 'production' ) {
            #Don't change prod value
            return 'new'.$_SERVER['SERVER_NAME'];
        }
        else {
            return base64_decode(trim(file_get_contents(getenv('bin_data'))));
        }
    }

    private function fileNameToUuid($name){
        
        $uuid_name = hash('crc32', $name);        
        $uuid_name .= Uuid::uuid4()->getHex();
        
        return $uuid_name;
    }
    
    public function saveUploadedFiles($files,$upload_type='document'){
        $saved=array();
        foreach ($files as $file) {
            if($file['error']!=0 || $file['tmp_name']==''){
                //file did not got submitted properly, 
                //therefore we will skip this file
                $saved[]=array(
                    'error' => $file['error'],
                    'status' => 'error',
                    'original' =>$file 
                );
                continue;
            }
            $dest_file_name=$this->fileNameToUuid($file['name']);
            $dest_path = $this->uploads_dir.DIRECTORY_SEPARATOR.$upload_type.DIRECTORY_SEPARATOR.$dest_file_name;
            $source_file = $file['tmp_name'];
            
            //Add save file encrypting it
            $this->cryptFileChunks($source_file, $dest_path,'encrypt');
            
            unlink($source_file);//remove tmp file after upload

            $name_parts = explode('.', $file['name']); 
            $saved[] = array(
                        'name'=>$file['name'],
                        'size'=>$file['size'],
                        'type'=>$file['type'],
                        'saved_as'=>$dest_file_name,
                        'ext'=>array_pop($name_parts),
                        'status' =>'success'
                        );
        }
        return $saved;
    }
    
    
    public function documentToResponse($doc,$upload_type='document'){
        
        $filePath=$this->uploads_dir.DIRECTORY_SEPARATOR.$upload_type.DIRECTORY_SEPARATOR.$doc->getFileName();
        
        
        if(!is_file($filePath)) {
            error_log("Unable to find file for download $filePath");
            //file does not exist
            return null;
        }
        

        //if meta data was stored with doc use that for file info
        $metaData =json_decode($doc->getDescription(),true);       
        
        if(! $metaData){
            // looks like we are unable to read meta data from file 
            // so guess/assume file info from document
            $name=$doc->getTitle();
            if($doc->getDocumentExt()){
                $name = $name .'.'.$doc->getDocumentExt();
            }             
            $metaData['type'] = 'application/octet-stream';   
            $metaData['name'] = $name;
            $metaData['size'] = filesize($filePath);             
        }

        //error_log("sending [$filePath] => [".$metaData['name']."]");
        
        return $this->fileToStream($metaData,$filePath);        

    }

    public function getFullPath($file_name,$type){
        return $this->uploads_dir.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$file_name;
    }
    public function attchmentToResponse($file_info){
        
        $filePath=$this->uploads_dir.DIRECTORY_SEPARATOR.$this::UPLOAD_TYPE_ATTACHMENT.DIRECTORY_SEPARATOR.$file_info['saved_as'];
        //TODO FIXME need to make sure file exists etc
        
        if(!is_file($filePath)) {
            //file does not exist
            error_log('File does not exist : '.$filePath);
            return null;
        }
                
        
        return $this->fileToStream($file_info, $filePath); 
    }
    
    /**
     * Encrypt or decrypt a file chunk by chunk
     * this will avoid imposing any type of restriction on file size
     * due to encryption process
     */
    public function cryptFileChunks($source, $destination, $op){

        if($op != "encrypt" and $op != "decrypt") return false;

        $buffer = '';
        $inHandle = fopen($source, 'rb');
        $outHandle = fopen($destination, 'wb+');

        if ($inHandle === false) return false;
        if ($outHandle === false) return false;

        while(!feof($inHandle)){
            if($op == "encrypt") {
                $buffer = fread($inHandle, $this->_CHUNK_SIZE);
                $buffer = $this->encryption_adapter->encrypt($buffer);
                $buffer .= PHP_EOL;
            }
            elseif($op == "decrypt") {
                $buffer = fgets($inHandle,$this->_CHUNK_SIZE_DOUBLE);
                $buffer = trim($buffer);
                if(!empty($buffer)) $buffer = $this->encryption_adapter->decrypt(trim($buffer));
            }
            if(!empty($buffer)) fwrite($outHandle, $buffer);
        }
        fclose($inHandle);
        fclose($outHandle);
        return true;
    }
    
    private function fileToStream($metaData,$filePath){
        //we have file and all the info so let's create a response stream and return it
        
        //decrypt source file and save to tmp file
        $temp_decrypted_file = tempnam(sys_get_temp_dir(),'enc');
        
        $success = $this->cryptFileChunks($filePath, $temp_decrypted_file,'decrypt');
        if(!$success){
	    error_log('Unable to DECRYPT!!!');
            throw new \Exception('Unabel to decrypt document');
        }
         
        //register on shutdown to delete decrypted file once download is done.
        register_shutdown_function('unlink',$temp_decrypted_file);
        
        $response = new Stream();
        $response->setStream(fopen($temp_decrypted_file, 'r'));
        $response->setStatusCode(200);
        $response->setStreamName(($metaData['name']));
        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . ($metaData['name']) .'"',
            'Content-Type' => $metaData['type'],
            'Content-Length' => $metaData['size']
        ));
        $response->setHeaders($headers);

        return $response;
    } 
}
