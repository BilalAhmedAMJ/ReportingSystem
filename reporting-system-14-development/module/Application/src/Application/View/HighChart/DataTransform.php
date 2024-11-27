<?php

namespace Application\View\HighChart;
 
 
/**
 *  Transforms given array of data to corespondign HighChart data format
 * so we can use it as json/javascript object in charts   
 * 
 */ 
class DataTransform {
    
    const Categories_Key='categories';
    const Series_Key='series';
    const Series_Name='name';
    const Series_Data='data';
    const Series_Color='color';
    const Series_Index='index';
    
    
    public static function convertToColumn($data,$categories_key,$series_key,$data_key=null,$series_filter=array()){
        //make sure data is an array
        if ($data instanceof Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }
        
        //check if we need to summarize data
        //if($data_key instanceof function)
        
       $chart_data=array(static::Categories_Key=>array(),static::Series_Key=>array());
        
       $categories = array();
       $series  = array();
       $transformed_data=array();
       foreach ($data as $datum) {
          if(in_array($datum[$series_key],$series_filter)){
              continue;
          }
          if(!in_array($datum[$series_key], $series)){
              $series[]=$datum[$series_key];
          }    
          if(!in_array($datum[$categories_key], $categories)){
              $categories[]=$datum[$categories_key];
          }
          if(! key_exists($datum[$series_key], $transformed_data)){
              $transformed_data[$datum[$series_key]]=array();
          }
          //var_dump(key_exists('Jan-2014',$transformed_data[$datum[$series_key]]) );
          //var_dump($transformed_data[$datum[$series_key]]);
          if(! key_exists( $datum[$categories_key], $transformed_data[$datum[$series_key]])){
              $transformed_data[$datum[$series_key]][$datum[$categories_key]]='';
          }
          $value = $transformed_data[$datum[$series_key]][$datum[$categories_key]];
          if($data_key ==null){
              $value++ ;
          }else{
              $value = $datum[$data_key];
          }
          $transformed_data[$datum[$series_key]][$datum[$categories_key]] = $value;
       } 

      //$transformed_data_complete=array();
      $series_data=array();
      //setup tranformed array 
      foreach ($series as $curr_series) {
          //$transformed_data_complete[$curr_series]=array();
          $curr_series_data=array();
          $curr_series_data[static::Series_Name]= ucfirst($curr_series);
          if(DataTransform::getColor($curr_series)){
              $curr_series_data[static::Series_Color]=DataTransform::getColor($curr_series);
          }
          if(DataTransform::getIndex($curr_series)){
              $curr_series_data[static::Series_Index]=DataTransform::getIndex($curr_series);
          }
          $curr_series_data[static::Series_Data]=array();
          foreach ($categories as $category) {
            $value=0;
            if(key_exists($category, $transformed_data[$curr_series])){
                //$transformed_data_complete[$curr_series][$category]=$transformed_data[$curr_series][$category];
                $value +=$transformed_data[$curr_series][$category];
            }else{
             //   $transformed_data_complete[$curr_series][$category]=0;
            }
            $curr_series_data[static::Series_Data][]=$value;
          }
          $series_data[]=$curr_series_data;
      }
      
      

          // $curr_series_data=array();
          // $curr_series_data[static::Series_Name]=$curr_series;
          // $curr_series_data[static::Series_Data]=array();
          
          
      //transsform data now      
      foreach ($data as $datum) {
          
      }
       return array(static::Categories_Key=>$categories,static::Series_Key=>$series_data);
    }
 
 
    public static function summarizeReportsByBranchPeriod($data,$branches,$periods){
        $result=array();
        $now = new \DateTime();
        //setup summary table
        foreach ($branches as $branch) {
            $result[$branch->getBranchName()]=array();
            foreach ($periods as $period) {
                if($now<$period->getPeriodEnd()){
                    //
                    $result[$branch->getBranchName()][$period->getPeriodCode()]=null;
                }else{
                    $result[$branch->getBranchName()][$period->getPeriodCode()]=array('report_status'=>array(),'department'=>array());
                }
            }            
        }
        //fill in values for departments/reports
        foreach ($data as $row) {
            $result_row=&$result[$row['branch_name']][$row['period_code']];
            if(is_array($result_row)){
                //print_r("Adding $row[branch_name]: $row[period_code] : $row[department_name]  $row[report_id] \n");
                $result_row['department'][$row['department_name']]=$row['report_status'];
                $result_row['report_status'][$row['report_status']]=$row['date_completed'];
            }else{
                //print_r("Not Row!! $row[branch_name]: $row[period_code] : $row[department_name]  $row[report_id] \n");
            }
        }
        
        return $result;
    }


    public static function getColor($status){
        $colors=array(
            'draft'=>'#333333',
            'completed'=>'#E8B110',
            'verified'=>'#3A87AD',
            'received'=>'#82AF6F',
            'outstanding'=>'transparent',
            //'outstanding'=>'#CC3300',
        );
        return $colors[$status];
    }


    public static function getIndex($status){
        $colors=array(
            'draft'=>'4',
            'completed'=>'1',
            'verified'=>'2',
            'received'=>'3',
            'outstanding'=>'-1',
            //'outstanding'=>'#CC3300',
        );
        return $colors[$status];
    }


        
}//DataTransform
    
    
/*
foreach ($data as $datum) {           
           $index=-1;
           if(!key_exists($datum[$categories_key], $indices)){
               $index=$current_index++;
               $indices[$datum[$categories_key]]=$index;
               $chart_data[static::Categories_Key][$index]=$datum[$categories_key];
               $chart_data[static::Series_Key][$index]=array(static::Series_Data=>array(),static::Series_Name=>$datum[$series_key]);
           }else{
               $index=$indices[$datum[$categories_key]];//get index of current item in chart data 
           }
           $chart_data[static::Series_Key][$index][static::Series_Data][$index]=$datum[$data_key];
       } */    