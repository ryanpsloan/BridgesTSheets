<?php


class Rate{

    private $rateId;
    private $rate;
    private $ed;
    private $code;

    public function __construct($newRateId, $newRate, $newED, $newCode){
        try {
            $this->setRateId($newRateId);
            $this->setRate($newRate);
            $this->setED($newED);
            $this->setCode($newCode);
        }catch(UnexpectedValueException $unexpected){
            throw(new UnexpectedValueException("Unable to construct Rate Object, check input format",0,$unexpected));
        }catch(RangeException $range){
            throw(new RangeException("Unable to construct Rate Object, check input format",0,$range));
        }
    }

    public function getRateId(){
        return $this->rateId;
    }

    public function getRate(){
        return $this->rate;
    }

    public function getED(){
        return $this->ed;
    }

    public function getCode(){
        return $this->code;
    }

    public function setRateId($newRateId){
        if($newRateId === null){
            $this->rateId = null;
        }

        if(filter_var($newRateId, FILTER_VALIDATE_INT) === false){
            throw(new UnexpectedValueException("rateId $newRateId is not an integer"));
        }

        $newRateId = intval($newRateId);

        if($newRateId <= 0){
            throw(new RangeException("rateId $newRateId is not positive"));
        }

        $this->rateId = $newRateId;
    }

    public function setRate($newRate){
        $newRate = trim($newRate);
        $newRate = filter_var($newRate, FILTER_SANITIZE_STRING);
        $this->rate = $newRate;
    }

    public function setED($newED){
        $newED = trim($newED);
        $newED = filter_var($newED, FILTER_SANITIZE_STRING);
        $this->ed = $newED;
    }

    public function setCode($newCode){
        $newCode = trim($newCode);
        $newCode = filter_var($newCode, FILTER_SANITIZE_STRING);
        $this->code = $newCode;
    }

    public static function getRateByRate(&$mysqli, $rate){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }

        $rate = trim($rate);
        $rate = filter_var($rate,FILTER_SANITIZE_STRING);

        $query = "SELECT rateId, rate, ed, code FROM rate WHERE rate = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("s", $rate);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }

        $result = $statement->get_result();

        if($result === false){
            throw(new mysqli_sql_exception("Unable to get result set"));
        }

        $row = $result->fetch_assoc();

        if($row !== null){
            try{
                $rate = new Rate($row['rateId'], $row['rate'], $row['ed'], $row['code']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Rate Object", 0, $exception));
            }
            return $rate;
        }else{
            return null;
        }
    }


    public static function getAllRates(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }

        $query = "SELECT rateId, rate, ed, code FROM rate ORDER BY rate ASC";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        /*$wasClean = $statement->bind_param();
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }*/

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }

        $result = $statement->get_result();

        if($result === false){
            throw(new mysqli_sql_exception("Unable to get result set"));
        }

        $rateArray = array();
        while(($row = $result->fetch_assoc()) !== null){

            try{
                $rateArray[] = new Rate($row['rateId'], $row['rate'], $row['ed'], $row['code']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Rate Object", 0, $exception));
            }


        }
        if($result->num_rows === 0){
            return null;
        }else{
            return $rateArray;
        }
    }


}