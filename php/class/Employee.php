<?php

class Employee{
    private $employeeId;
    private $empId;
    private $firstName;
    private $lastName;

    public function __construct($newEmployeeId, $newEmpId, $newFirstName, $newLastName){
        try{
            $this->setEmployeeId($newEmployeeId);
            $this->setEmpId($newEmpId);
            $this->setFirstName($newFirstName);
            $this->setLastName($newLastName);

        }catch(UnexpectedValueException $unexpected){
            throw(new UnexpectedValueException("Unable to construct Employee Object. Check input formats.", 0, $unexpected));

        }catch(RangeException $range){
            throw(new RangeException("Unable to construct Employee Object. Check input formats.", 0, $range));

        }

    }

    public function getEmployeeId(){
        return $this->employeeId;

    }

    public function getEmpId(){
        return $this->empId;
    }

    public function getFirstName(){
        return $this->firstName;
    }

    public function getLastName(){
        return $this->lastName;
    }

    public function setEmployeeId($newEmployeeId){
        if($newEmployeeId === null){
            $this->employeeId = null;
            return;
        }

        if(filter_var($newEmployeeId, FILTER_VALIDATE_INT) === false){
            throw(new UnexpectedValueException("employeeId $newEmployeeId is not an integer"));
        }

        $newEmployeeId = intval($newEmployeeId);

        if($newEmployeeId <= 0){
            throw(new RangeException("employeeId $newEmployeeId is not positive"));
        }

        $this->employeeId = $newEmployeeId;

    }

    public function setEmpId($newEmpId){
        if(filter_var($newEmpId, FILTER_VALIDATE_INT) === false){
            throw(new UnexpectedValueException("EmpId $newEmpId is not an integer"));
        }

        $newEmpId = intval($newEmpId);
        if($newEmpId <= 0){
            throw(new RangeException("EmpId $newEmpId is not positive"));
        }

        $this->empId = $newEmpId;

    }

    public function setFirstName($newFirstName){
        $newFirstName = trim($newFirstName);
        $newFirstName = strtolower($newFirstName);
        $filterOptions = array("options" => array("regexp" => "/^[a-z]+$/"));
        if(filter_var($newFirstName, FILTER_VALIDATE_REGEXP, $filterOptions) === false){
            throw(new RangeException("First name cannot contain spaces, numbers, or special characters"));
        }
        $this->firstName = $newFirstName;
    }

    public function setLastName($newLastName){
        $newLastName = trim($newLastName);
        $newLastName = strtolower($newLastName);
        $filterOptions = array("options" => array("regexp" => "/^[a-z]+$/"));
        if(filter_var($newLastName, FILTER_VALIDATE_REGEXP, $filterOptions) === false){
            throw(new RangeException("Last name cannot contain spaces, numbers, or special characters"));
        }
        $this->lastName = $newLastName;


    }

    public function insert(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("input is not a mysqli object"));
        }

        if($this->employeeId !== null){
            throw(new mysqli_sql_exception("not a new employee"));
        }

        $query = "INSERT INTO employee (employeeId, empId, firstName, lastName) VALUES (?,?,?,?)";
        $statement = $mysqli->prepare($query);

        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("iiss", $this->employeeId, $this->empId, $this->firstName, $this->lastName);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }

        $this->employeeId = $mysqli->insert_id;
    }

    public function delete(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("input is not a mysqli object"));
        }

        if($this->employeeId === null){
            throw(new mysqli_sql_exception("Unable to delete an employee that does not exist"));
        }

        $query = "DELETE FROM employee WHERE employeeId = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("i",$this->employeeId);
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

        if($this->employeeId === null){
            throw(new mysqli_sql_exception("Unable to update an employee that does not exist"));
        }

        $query = "UPDATE employee SET empId = ?, firstName = ?, lastName = ? WHERE employeeId = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("issi", $this->empId, $this->firstName, $this->lastName, $this->employeeId);
        if($wasClean === false){
            throw(new mysqli_sql_exception("Unable to bind parameters"));
        }

        if($statement->execute() === false){
            throw(new mysqli_sql_exception("Unable to execute mysqli statement"));
        }
    }

    public static function getEmployeeByEmployeeId(&$mysqli,$employeeId){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }

        if(filter_var($employeeId, FILTER_VALIDATE_INT) === false){
            throw(new UnexpectedValueException("employeeId $employeeId is not an integer"));
        }

        $employeeId = intval($employeeId);
        if($employeeId <= 0){
            throw(new RangeException("employeeId $employeeId is not positive"));
        }

        $query = "SELECT employeeId, empId, firstName, lastName FROM employee WHERE employeeId = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("i", $employeeId);
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
                $employee = new Employee($row['employeeId'], $row['empId'], $row['firstName'], $row['lastName']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Employee Object", 0, $exception));
            }
            return $employee;
        }else{
            return null;
        }
    }

    public static function getEmployeeByName(&$mysqli,$firstName,$lastName){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }
        $firstName = trim($firstName);
        $lastName = trim($lastName);

        $firstName = strtolower($firstName);
        $lastName = strtolower($lastName);

        $firstName = filter_var($firstName, FILTER_SANITIZE_STRING);
        $lastName = filter_var($lastName, FILTER_SANITIZE_STRING);

        $query = "SELECT employeeId, empId, firstName, lastName FROM employee WHERE firstName = ? AND lastName = ?";
        $statement = $mysqli->prepare($query);
        if($statement === false){
            throw(new mysqli_sql_exception("Unable to prepare statement"));
        }

        $wasClean = $statement->bind_param("ss", $firstName, $lastName);
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
                $employee = new Employee($row['employeeId'], $row['empId'], $row['firstName'], $row['lastName']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Employee Object", 0, $exception));
            }
            return $employee;
        }else{
            return null;
        }
    }

    public static function getAllEmployees(&$mysqli){
        if(gettype($mysqli) !== "object" || get_class($mysqli) !== "mysqli"){
            throw(new mysqli_sql_exception("Input is not a valid mysqli object"));
        }

        $query = "SELECT employeeId, empId, firstName, lastName FROM employee";
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

        $employeeArray = array();
        while(($row = $result->fetch_assoc()) !== null){

            try{
                $employeeArray[] = new Employee($row['employeeId'], $row['empId'], $row['firstName'], $row['lastName']);
            }catch(Exception $exception){
                throw(new mysqli_sql_exception("Unable to convert row to Employee Object", 0, $exception));
            }


        }
        if($result->num_rows === 0){
            return null;
        }else{
            return $employeeArray;
        }
    }

}





?>