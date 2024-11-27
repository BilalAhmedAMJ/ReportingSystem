<?php

$path = __DIR__.'/../../../vendor/zendframework/zendframework/library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

$path = __DIR__.'/../../../../module/Application/src';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

use Zend\Crypt\Password\Bcrypt as Bcrypt;

use Application\Entity\Period;

//$brypt = new Zend::Crypt::Password::Bcrypt();

print_r($path);
//echo crypt('test123');


class testObj{
    public function callb($arg, $argtwo,$argArr){
        var_dump($arg);
        var_dump($argtwo);
        var_dump($argArr);
    }
}


$obj=new testObj();

function wrapper($callback){
    $callback();        
}


function wrapper2(/*$obj,$methodName,args=array()*/){
    $args=func_get_args();
    
    $obj=array_shift($args);
    $methodName=array_shift($args);
    if(is_object($obj)&&is_string($methodName)){
        $reflectObj=new ReflectionObject($obj);
        try{
            $method=$reflectObj->getMethod($methodName);
        }catch(Exception $e){
                    
        }
        if($method){
            $method->invokeArgs($obj,$args);  
            return true;      
        }
    }
    //method call was unsuccessful 
    return false;  
}

function misc(){
$arg1='arg1';
$arg2='true';
$arg3=array(1,'a',false,2,'b',true);

//wrapper(function()use($obj,$arg1,$arg2,$arg3){$obj->callb($arg1,$arg2,$arg3);});

echo "[".wrapper2($obj,'callb',$arg1,$arg2,$arg3)."]";
//wrapper(function()use($obj,$arg1,$arg2,$arg3){$obj->callb($arg1,$arg2,$arg3);});


$a=array();
$a['a']='A';
$a['b']=array('ab'=>'AB');

$b=$a;

$a['c']='After';

var_dump($b);


$dt = new DateTime('NOW');

$dt->setDate(null,null,01);
print_r($dt);

}


function testJsonDecode(){
    var_dump(json_decode('["Some String config"]'));
    var_dump(json_last_error());
}

function testArrayMerge(){
    $a1=array(array(1,2,3,5,6),array(6),array(7,8));
    $a2=array();
    print_r($a2);
}

//testArrayMerge();

$date = new DateTime('2000-07-01');
$now = new DateTime('2013-12-02');

print_r($date);
// 
// while($date<$now){
    // $code=($date->format('M-Y')."\n");
    // $month =  new DateInterval('P1M');
    // $date = date_add($date,$month);
    // $period = Period::create($code);
    // print_r($period->__toString()."\n");
// }
// //print_r($date);
// 
// $year=2015;

// $a=array(['a'=>1],['b'=>2]);
 $b=array(array('c'=>3));
 
print_r(array_merge($a,$b));

print_r(array_merge($b,$a));


print_r(new DateTime('15-May-2016'));

$d = new DateTime('15-May-2016');

print_r($d->format('Y-M-d'));

