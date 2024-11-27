<?php

namespace Application\Util;


use Zend\Crypt\Symmetric\SymmetricInterface;


class PlainTextSymmetricImpl implements SymmetricInterface{
    /**
     * @param string $data
     */
    public function encrypt($data){
        return $data;
    }

    /**
     * @param string $data
     */
    public function decrypt($data){
        return $data;
    }

    /**
     * @param string $key
     */
    public function setKey($key){
        return $key;
    }

    public function getKey(){
        return ;
    }

    public function getKeySize(){
        return ;
    }

    public function getAlgorithm(){
        return ;
    }

    /**
     * @param  string $algo
     */
    public function setAlgorithm($algo){
        return $algo;
    }

    public function getSupportedAlgorithms(){
        return ;
    }

    /**
     * @param string|false $salt
     */
    public function setSalt($salt){
        return $sale;
    }

    public function getSalt(){
        return ;
    }

    public function getSaltSize(){
        return ;
    }

    public function getBlockSize(){
        return ;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode){
        return $mode;
    }

    public function getMode(){
        return ;
    }

    public function getSupportedModes(){
        return ;
    }
}