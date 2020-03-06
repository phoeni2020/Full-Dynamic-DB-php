<?php
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
    public function query($array ,$table ,$op,$extracond='')
    {
        $count = count($array);
        if($count <= 1 && $op =='getone' && $op =='update')
        {
            echo $count;
            return $errormsg = false;
        }
        if ($op =='add')
        {
            echo 'hi';
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
            $queryf ="UPDATE `$table` SET ";
            $querym = '';
            $cond =' WHERE ';
            foreach ($array as $key=>$value)
            {
                if (!is_array($value))
                {
                    if ($count-1 > 1)
                    {
                        $querym .= "`$key` = '$value',";
                    }
                    else
                    {
                        $querym .= "`$key` = '$value'";
                    }

                }
                else
                {
                    $countcons = $count2 = count($value);

                    foreach ($value as $feild =>$fvalue)
                    {
                        if ($count2 > 1)
                        {
                            $cond.="`$feild`='$fvalue' AND ";
                            $count2 --;
                        }
                        else
                        {
                            $cond .= "`$feild` = '$fvalue'";
                        }
                    }


                }
                $count--;
            }
            $query =$queryf.$querym.$cond;
            $this->con->query($query);
            if ($this->con->affected_rows > 0)
            {
                return'done';
            }
            return 'try again';
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
        elseif($op== 'getall')
        {
            $queryf ="SELECT * FROM `$table`";
            $cond =' WHERE ';
            if ($count != 0)
            {
                foreach ($array as $key => $value) {
                    if (is_array($value))
                    {
                        if ($count - 1 > 1) {
                            $cond .= "`$key` = '$value',";
                        } else {
                            $cond .= "`$key` = '$value'";
                        }

                    }
                    else
                     {
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
            }
            else
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
            $query =$queryf.$querym.$cond;
            $this->con->query($query);
        }
        else
        {

        }
    }
}
$db = new DB();
$data = ['username'=>'khaled'];
$res = $db->query($data,'users','delete');

