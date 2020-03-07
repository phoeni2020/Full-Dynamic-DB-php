<?php
/**
 *author : Devkhaled
 * Date   : 2/2/2020
 * Project name : DB class
 * Description : This class is contain a function's
 * that mange the CRUD operations on database
 * and it's full dynamic you can use it at any part into your project
 * just make sure that you follow the documentation
 * any edit is welcomed
 * ***PLEASE MAKE SURE THAT IF YOU FACED ANY BUG CAUSED BY THIS CLASS REPORT IT TO  MY E-MAIL
 *  IMMEDIATE OR YOU CAN REPORT IT ON GITHUB***
 * E-MAIL : php.programmr.94@gmail.com
 *======================================
 *(HOW TO USE AND INSTRUCTIONS)
 * now this function has a four parm
 * FIRST PPRAM :  $array as is it clear you must pass an array holding fields and values
 *
 * for example : table users has three fields username , password , email and i wanna add a new user
 * so i must array like this
 *
 * $data = array('username'=>$username,'password'=>$password,'email'=>$email)
 *
 * $user = new DB();
 * $user->query($data,'users','add');
 *
 * it will return ,massage string (done)
 * if there is any error make the process stop like no values your not passing array
 * the function will return  massage  string (try again)
 * ***************************************************************************************************
 * in a specific cases you gonna need to pass multi dimension array
 *
 * like retrieving one record or more from data base
 *
 * for example : user tries to log in and he will need all the data in DB to use it in web site like img ,email ....
 *
 * you must also enter the operator you gonna use AND , OR , NOT
 *
 * $data = array('*',array('username'=>'khaled','password'=>'1234'));
 *
 * $user = new DB();
 *
 * $res = $user->query($data,'users','getbycond','AND');
 *
 * $_SESSION['username']=$res[0]['username'];
 *
 * as you see to store username in a session i accsess first array offset[0]thenindex['username']
 *
 * it's mean the result will be an multi dimension array
 *
 * So the query  gonna be like this
 *(SELECT * FROM `users` WHERE `username` = 'khaled' AND `password` = '1234';)
 *
 * if no match happen it will return (no row found)
 * *****************************************************************
 * in update case it's a little bit different
 *
 * You MUST send and multi dimension array
 *
 * the first dimension MUST BE THE VALUES
 *
 * the second dimension MUST BE THE CONDITION'S
 *
 * (example)
 *
 *$data =array('username'=>'khaled','password'=>'1234','conditions'=>array('id'=>'2'));
 *
 * $userinfo = new DB();
 *
 * $res = $userinfo->operation($data,'users','update')
 *
 * it should return string (done) for this case
 *
 * in otherwise $res will return string like (try again ,the conditions has no value,You must send conditions)
 * OR it just gonna return bool=> false
 * it depends on the case
 *
 * 1- (try again)
 * it means that you did every thing right but no effected rows in db
 *
 * 2- (the conditions has no value)
 * it means you sent an array with conditions key and this key not an array or array
 * but contain no value
 *
 * 3- (You must send conditions)
 * means you didn't send into the array conditions key
 *
 * 4-bool => false
 * means that you send an one dimension
 *
 *
 *================================================================================
 *
 * SECOND PRAM : it simply the table name used into your db
 *
 * ===============================================================================
 *
 * THIRD PRAM : it's the operation you wanna do
 *
 * if you want to use insert statment that pram MUST be
 * ( add )=> never mind you can write it at any case you want uppercase or camel case
 *
 * but it must be the(add)word at any case
 * *****************************************************************
 * if you want to use update statment that pram MUST be
 *
 * (update)=> like (add) it should be the (update) word at any case you comfort with
 *
 * ******************************************************************
 */
require 'config.php';
class DB
{
    private $con;
    public function __construct()
    {
        $this->con = new mysqli(DB_HOST, DB_USER,DB_PASS,DB_NAME);

        if (!empty($this->con->connect_error))
        {
            exit('conection err'.$this->con->connect_error.'error number'.$this->con->connect_errno);
        }
        return $this->con;
    }
    private function query($array ,$table ,$ope,$extracond='')
    {
        $count = count($array);
        $op = strtolower($ope);
        if($count <= 1 && $op =='update')
        {
            return $errormsg = false;
        }
        if ($op =='add')
        {
            $queryk = "INSERT INTO `$table` (";
            $queryv = ") Values(";
            foreach ($array as $key=>$value)
            {
                if($count >1)
                {
                    $queryk .= "`$key`,";
                    $queryv .="'$value',";
                }
                else
                {
                    $queryk.="`$key`";
                    $queryv.="'$value'";
                }
                $count--;
            }
            $querye =");";
            $query = $queryk . $queryv .$querye;
            $this->con->query($query);
            if ($this->con->affected_rows > 0)
            {
                return'done';
            }
            return 'try again';
        }
        elseif ($op == 'update')
        {
            if(array_key_exists('conditions',$array))
            {
                if (is_array($array['conditions']) && count($array['conditions']) >= 1) {
                    $queryf = "UPDATE `$table` SET ";
                    $querym = '';
                    $cond = ' WHERE ';
                    foreach ($array as $key => $value) {
                        if (!is_array($value)) {
                            if ($count - 1 > 1) {
                                $querym .= "`$key` = '$value',";
                            } else {
                                $querym .= "`$key` = '$value'";
                            }
                        } else {
                            $countcons = $count2 = count($value);
                            foreach ($value as $feild => $fvalue) {
                                if ($count2 > 1) {
                                    $cond .= "`$feild`='$fvalue' AND ";
                                    $count2--;
                                } else {
                                    $cond .= "`$feild` = '$fvalue'";
                                }
                            }
                        }
                        $count--;
                    }
                    $query = $queryf . $querym . $cond;
                    $this->con->query($query);
                    if ($this->con->affected_rows > 0) {
                        return 'done';
                    }
                    return 'try again';
                }
                else
                {
                    return'the conditions has no value';
                }
            }
            else
            {
                return'You must send conditions';
            }
        }
        elseif ($op == 'delete')
        {
            $queryf = "DELETE FROM $table WHERE";
            $cond='';
            if ($count > 1)
            {
                foreach ($array as $key => $value)
                {
                    if ($count ==1)
                    {
                        $cond .="`$key` = '$value';";
                    }
                    else
                    {
                        $cond .="`$key` = '$value' AND";
                    }
                    $count--;
                }
            }
            else
            {
                $ar='';
                $ar = array_keys($array);
                $value =$array[$ar[0]];
                $cond="`$ar[0]`= '$value'";
            }
            $query = $queryf.$cond;
            $this->con->query($query);
            if ($this->con->affected_rows > 0)
            {
                return'done';
            }
            return 'try again';
        }
        elseif($op== 'getbycond')
        {
            $value = '';
            $queryf ="SELECT * FROM `$table`";
            $cond = '';
            if ($count != 0)
            {
                if($array[0] == '*')
                {
                    $condoption =" $extracond ";
                    $condtions = $array[1];
                    if (is_array($condtions))
                    {
                        foreach ($condtions as $key => $value)
                        {
                            if ($count - 1 >= 1)
                            {
                                $cond .= "`$key` = '$value' $condoption ";
                            }
                            else
                            {
                                $cond .= "`$key` = '$value'";
                            }
                            $count--;
                        }
                        $query =$queryf.' WHERE '.$cond;
                        $res  =  $this->con->query($query);

                        if($res->num_rows > 0)
                        {
                            $records = array();
                            while ($record = $res->fetch_assoc())
                            {
                                $records[]=$record;
                            }
                            return $records;
                        }
                        return 'no row found';
                    }
                }
                else
                {
                    $valuestoget = $array[0];
                    $condtions = $array[1];
                    $values = '';
                    if(is_array($valuestoget)&&is_array($condtions))
                    {
                        $count1 = count($valuestoget);
                        $count2 = count($condtions);
                        foreach ($condtions as $key => $value)
                        {
                            if ($count2 - 1 >= 1)
                            {
                                $cond .= "`$key` = ' $value ' $extracond";
                            }
                            else
                            {
                                $cond .= "`$key` = '$value'";
                            }
                            $count2--;
                        }
                        foreach ($valuestoget as $value)
                        {
                            if ($count1 - 1 >= 1)
                            {
                                $values .= " `$value` , ";
                            }
                            else
                            {
                                $values .= " `$value` ";
                            }
                            $count1--;
                        }
                        $queryf ="SELECT $values FROM `$table`";
                        $query = $queryf .' WHERE '.$cond;
                        $res = $this->con->query($query);
                        if($res->num_rows > 0)
                        {
                            $records = array();
                            while ($record = $res->fetch_assoc())
                            {
                                $records[]=$record;
                            }

                            return $records;
                        }
                    }
                    else
                    {
                        return'sorry You need to pass all info you want';
                    }
                }
            }
            elseif($op = 'getall')
            {

                $res = $this->con->query($queryf);
                if($res->num_rows > 0)
                {
                    $records = array();
                    while ($record = $res->fetch_assoc())
                    {
                        $records[]=$record;
                    }
                    return $records;
                }
            }
        }
    }
    public function operation($array ,$table ,$op,$extracond='')
    {
        $res = $this->query($array , $table ,$op ,$extracond);

        return $res;
    }
}
