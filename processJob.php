<?php
session_start();
include_once ("/var/www/html/Bridges/php/class/dbConnect.php");
include_once ("/var/www/html/Bridges/php/class/Job.php");

$mysqli = MysqliConfiguration::getMysqli();
var_dump($POST_['submit1']);
if(isset($_POST['submit'])) {
    $jobCode = $_POST['jobCode'];
    $jobDesc = $_POST['jobDesc'];

    $newJob = new Job(null, $jobCode, $jobDesc);
    try {
        $newJob->insert($mysqli);
        $_SESSION['output'] = $jobCode . " has been inserted";
    }catch(Exception $e){
        $_SESSION['output'] = $jobCode . " was not inserted: " . $e->getMessage();
    }

    //header("Location: addJob.php");
}
else if(isset($_POST['submit1'])){
    $jobCode = $_POST['jobCode1'];
    $jobDesc = $_POST['jobDesc1'];


    $newJob = Job::getJobByJobDescription($mysqli,$jobDesc);
    $newJob->setJobCode($jobCode);
    $newJob->setJobDescription($jobDesc);

    try {
        $newJob->update($mysqli);
        $_SESSION['output'] = $jobCode . " has been updated";
    }catch(Exception $e){
        $_SESSION['output'] = $jobCode . " was not updated: " . $e->getMessage();
    }

    header("Location: addJob.php");

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