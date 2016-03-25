<?php
session_start();
include_once ("/var/www/html/Bridges/php/class/dbConnect.php");
include_once ("/var/www/html/Bridges/php/class/Employee.php");

$mysqli = MysqliConfiguration::getMysqli();

if(isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $empId = $_POST['empId'];

    $newEmployee = new Employee(null, $empId, $firstName, $lastName);
    try {
        $newEmployee->insert($mysqli);
        $_SESSION['output'] = $firstName . " " . $lastName . " has been inserted";
    }catch(Exception $e){
        $_SESSION['output'] = $firstName . " " . $lastName . " was not inserted: " . $e->getMessage();
    }

    header("Location: addEmployee.php");
}
else if(isset($_POST['submit1'])){
    $employeeId = $_POST['employeeId'];
    $firstName = $_POST['firstName1'];
    $lastName = $_POST['lastName1'];
    $empId = $_POST['empId1'];

    $newEmployee = Employee::getEmployeeByEmployeeId($mysqli,$employeeId);
    $newEmployee->setEmpId($empId);
    $newEmployee->setFirstName($firstName);
    $newEmployee->setLastName($lastName);

    try {
        $newEmployee->update($mysqli);
        $_SESSION['output'] = $firstName . " " . $lastName . " has been updated";
    }catch(Exception $e){
        $_SESSION['output'] = $firstName . " " . $lastName . " was not updated: " . $e->getMessage();
    }

    header("Location: addEmployee.php");

}else if(isset($_POST['submit2'])){
    $employeeId = intval($_POST['employeeIdA']);

    $employee = Employee::getEmployeeByEmployeeId($mysqli,$employeeId);

    try {
        $employee->delete($mysqli);
        $_SESSION['output'] = ucwords($employee->getFirstName()). " " . ucwords($employee->getLastName()) ." has been deleted";
    }catch(Exception $e){
        $_SESSION['output'] = ucwords($employee->getFirstName()). " " . ucwords($employee->getLastName()) ." has been deleted" . $e->getMessage();
    }

    header("Location: addEmployee.php");
}