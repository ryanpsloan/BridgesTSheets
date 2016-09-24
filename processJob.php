<?php
session_start();
include_once ("/var/www/html/Bridges/php/class/dbConnect.php");
include_once ("/var/www/html/Bridges/php/class/Job.php");

$mysqli = MysqliConfiguration::getMysqli();

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

    header("Location: addJob.php");
}
else if(isset($_POST['submit1'])){
    $jobId = $_POST['jobId'];
    $jobCode = $_POST['jobCode1'];
    $jobDesc = $_POST['jobDesc1'];

    $newJob = Job::getJobByJobId($mysqli,$jobId);
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
    var_dump($_POST['jobIdB']);
    $jobId = intval($_POST['jobIdB']);

    $job = Job::getJobByJobId($mysqli,$jobId);

    try {
        $job->delete($mysqli);
        $_SESSION['output'] = $job->getJobCode() ." has been deleted";
    }catch(Exception $e){
        $_SESSION['output'] = $job->getJobCode() ." has not been deleted: " . $e->getMessage();
    }

    header("Location: addJob.php");
}