<?php

class Job{

    private $jobId;
    private $jobCode;
    private $jobDescription;

    public function __construct($newJobId, $newJobCode, $newJobDescription){
            try{
                $this->setJobId($newJobId);
                $this->setJobCode($newJobCode);
                $this->setJobDescription($newJobDescription);
            }catch(UnexpectedValueException $unexpected){
                throw(new UnexpectedValueException("Unable to construct Job Object. Check input formats.",0,$unexpected));
            }catch(RangeException $range){
                throw(new RangeException("Unable to construct Job Object. Check input formats.",0,$range));
            }
    }

    public function getJobId(){
        return $this->jobId;
    }

    public function getJobCode(){
        return $this->jobCode;
    }

    public function getJobDescription(){
        return $this->jobDescription;
    }

    public function setJobId($newJobId){
        if($newJobId === null){
            $this->jobId = null;
            return;
        }

        if(filter_var($newJobId, FILTER_VALIDATE_INT)  === false){
            throw(new UnexpectedValueException("jobId $newJobId is not an integer"));
        }

        $newJobId = intval($newJobId);

        if($newJobId <= 0){
            throw(new RangeException("jobId $newJobId is not positive"));
        }

        $this->jobId = $newJobId;
    }

    public function setJobCode($newJobCode){
        $newJobCode = trim($newJobCode);
        $newJobCode = filter_var($newJobCode, FILTER_SANITIZE_STRING);
        $this->jobCode = $newJobCode;
    }

    public function setJobDescription($newJobDescription){
        $newJobDescription = trim($newJobDescription);
        $newJobDescription = filter_var($newJobDescription, FILTER_SANITIZE_STRING);
        $this->jobDescription = $newJobDescription;

    }

    public function insert(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("input is not a mysqli object"));
        }

        if($this->jobId !== null){
            throw(new mysqli_sql_exception("not a new job"));
        }

        $query = "INSERT INTO job (jobId, jobCode, jobDescription) VALUES (?,?,?)";
        $statement = $mysqli->prepare($query);

        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("iss", $this->jobId, $this->jobCode, $this->jobDescription);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }

        $this->jobId = $mysqli->insert_id;
    }

    public function delete(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("input is not a mysqli object"));
        }

        if($this->jobId === null){
            throw(new mysqli_sql_exception("Unable to delete a job that does not exist"));
        }

        $query = "DELETE FROM job WHERE jobId = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("i",$this->jobId);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }
    }

    public function update(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli") {
            throw(new mysqli_sql_exception("input is not a mysqli object"));
        }

        if($this->jobId === null){
            throw(new mysqli_sql_exception("Unable to update a job that does not exist"));
        }

        $query = "UPDATE job SET jobCode = ?, jobDescription = ? WHERE jobId = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("ssi", $this->jobCode, $this->jobDescription, $this->jobId);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }
    }

    public static function getJobByJobId(&$mysqli,$jobId){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }

        $jobId = intval($jobId);
        if(filter_var($jobId, FILTER_VALIDATE_INT) === false){
            throw(new UnexpectedValueException("jobId $jobId is not an integer"));
        }

        if($jobId <= 0){
            throw(new RangeException("jobId $jobId is not positive"));
        }

        $query = "SELECT jobId, jobCode, jobDescription FROM job WHERE jobId = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("i", $jobId);
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
                $job = new Employee($row['jobId'], $row['jobCode'], $row['jobDescription']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Job Object", 0, $exception));
            }
            return $job;
        }else{
            return null;
        }
    }

    public static function getJobByJobDescription(&$mysqli, $jobDescription){
        $jobDescription = trim($jobDescription);
        $jobDescription = filter_var($jobDescription, FILTER_SANITIZE_STRING);
        $query = "SELECT jobId, jobCode, jobDescription From job WHERE jobDescription = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("s", $jobDescription);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }

        $result = $statement->get_result();
        if($result === false) {
            throw(new mysqli_sql_exception("Unable to get result set"));
        }

        $row = $result->fetch_assoc();
        if($row !== null) {
            try {
                $job = new Job ($row['jobId'], $row['jobCode'], $row['jobDescription']);
                return $job;
            } catch(Exception $exception) {
                throw(new mysqli_sql_exception("Unable to convert row to Job Object", 0, $exception));
            }
        }else {
            return (null);
        }
    }

    public static function getAllJobs(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }

        $query = "SELECT jobId, jobCode, jobDescription FROM job ORDER BY jobDescription ASC";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        /*$wasClean = $statement->bind_param("",);
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

        $jobArray = array();
        while(($row = $result->fetch_assoc()) !== null){

            try{
                $jobArray[] = new Job($row['jobId'], $row['jobCode'], $row['jobDescription']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Job Object", 0, $exception));
            }


        }
        if($result->num_rows === 0){
            return null;
        }else{
            return $jobArray;
        }
    }

}