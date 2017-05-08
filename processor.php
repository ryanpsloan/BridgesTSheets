<?php
/**********************************************************************************************************************
  Author: Ryan Sloan
  This process will read a F657 GL .txt file from LCDN and sort the data and analyze the credits and debits and whether or not
  they balance and output the total and whether or not the balance to a web page
  ryan@paydayinc.com
 *********************************************************************************************************************/

session_start();
include_once("/var/www/html/Bridges/php/class/Employee.php");
include_once("/var/www/html/Bridges/php/class/dbConnect.php");
include_once("/var/www/html/Bridges/php/class/Rate.php");
include_once("/var/www/html/Bridges/php/class/Job.php");
if(isset($_FILES)) { //Check to see if a file is uploaded
    try {
        if (($log = fopen("log.txt", "w")) === false) { //open a log file
            //if unable to open throw exception
            throw new RuntimeException("Log File Did Not Open.");
        }

        $today = new DateTime('now'); //create a date for now
        fwrite($log, $today->format("Y-m-d H:i:s") . PHP_EOL); //post the date to the log
        fwrite($log, "--------------------------------------------------------------------------------" . PHP_EOL); //post to log

        $name = $_FILES['file']['name']; //get file name
        fwrite($log, "FileName: $name" . PHP_EOL); //write to log
        $type = $_FILES["file"]["type"];//get file type
        fwrite($log, "FileType: $type" . PHP_EOL); //write to log
        $tmp_name = $_FILES['file']['tmp_name']; //get file temp name
        fwrite($log, "File TempName: $tmp_name" . PHP_EOL); //write to log
        $tempArr = explode(".", $_FILES['file']['name']); //set file name into an array
        $extension = end($tempArr); //get file extension
        fwrite($log, "Extension: $extension" . PHP_EOL); //write to log

        //If any errors throw an exception
        if (!isset($_FILES['file']['error']) || is_array($_FILES['file']['error'])) {
            fwrite($log, "Invalid Parameters - No File Uploaded." . PHP_EOL);
            throw new RuntimeException("Invalid Parameters - No File Uploaded.");
        }

        //switch statement to determine action in relationship to reported error
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                fwrite($log, "No File Sent." . PHP_EOL);
                throw new RuntimeException("No File Sent.");
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
                throw new RuntimeException("Exceeded Filesize Limit.");
            default:
                fwrite($log, "Unknown Errors." . PHP_EOL);
                throw new RuntimeException("Unknown Errors.");

        }

        //check file size
        if ($_FILES['file']['size'] > 2000000) {
            fwrite($log, "Exceeded Filesize Limit." . PHP_EOL);
            throw new RuntimeException('Exceeded Filesize Limit.');
        }

        //define accepted extensions and types
        $goodExts = array("txt", "csv");
        $goodTypes = array("text/plain", "text/csv", "application/vnd.ms-excel");

        //test to ensure that uploaded file extension and type are acceptable - if not throw exception
        if (in_array($extension, $goodExts) === false || in_array($type, $goodTypes) === false) {
            fwrite($log, "This page only accepts .txt and .csv files, please upload the correct format." . PHP_EOL);
            throw new Exception("This page only accepts .txt and .csv files, please upload the correct format.");
        }

        //move the file from temp location to the server - if fail throw exception
        $directory = "/var/www/html/Bridges/BridgesFiles";
        if (move_uploaded_file($tmp_name, "$directory/$name")) {
            fwrite($log, "File Successfully Uploaded." . PHP_EOL);
            //echo "<p>File Successfully Uploaded.</p>";
        } else {
            fwrite($log, "Unable to Move File to /BridgesFiles." . PHP_EOL);
            throw new RuntimeException("Unable to Move File to /BridgesFiles.");
        }

        //rename the file using todays date and time
        $month = $today->format("m");
        $day = $today->format('d');
        $year = $today->format('y');
        $time = $today->format('H-i-s');

        $newName = "$directory/BridgesData-$month-$day-$year-$time.$extension";
        if ((rename("$directory/$name", $newName))) {
            fwrite($log, "File Renamed to: $newName" . PHP_EOL);
            //echo "<p>File Renamed to: $newName </p>";
        } else {
            fwrite($log, "Unable to Rename File: $name" . PHP_EOL);
            throw new RuntimeException("Unable to Rename File: $name");
        }

        //open the stream for file reading
        $handle = fopen($newName, "r");
        if ($handle === false) {
            fwrite($log, "Unable to Open Stream." . PHP_EOL);
            throw new RuntimeException("Unable to Open Stream.");
        } else {
            fwrite($log, "Stream Opened Successfully." . PHP_EOL);
        }

        $fileData = array();
        //read first line headers
        $headers = fgets($handle);

        //read the data in line by line
        while (!feof($handle)) {
            $line_of_data = fgets($handle); //gets data from file one line at a time
            $line_of_data = trim($line_of_data); //trims the data
            $fileData[] = explode(",", $line_of_data); //breaks the line up into pieces that the array can store
        }

        //close file reading stream

        fclose($handle);

        if(count(end($fileData)) < 10) {
            array_pop($fileData);
        }

        //sorts the data by name column index 1 and date column index 3

        $nameArr = $jobArr = array();

        foreach($fileData as $key => $value ){

            $nameArr[] = $value[1];
            $jobArr[] = $value[6];
        }
        array_multisort($nameArr, SORT_ASC, $jobArr, SORT_ASC, $fileData);

        //var_dump($fileData);

        $data = array();
        foreach($fileData as $line){
            $lastName = preg_replace("/\"/", "", trim($line[1]));
            if(strpos($line[2], "-") !== false) {
                $temp = explode("-", preg_replace("/\"/", "", trim($line[2])));
                $firstName = trim($temp[0]);
                $temp = trim($temp[1]);
                $jobType = " - " . $temp;
            }else{
                $firstName = preg_replace("/\"/", "", trim($line[2]));
                $jobType = '';
            }

            $rate = trim($line[4]);
            $job = trim($line[6]);

            $data[($firstName . " " . $lastName . $jobType)][$job][$rate][] = array($line[0], $lastName, $firstName, $line[3], $rate, $line[5], $line[6], $line[7]);         }

        //var_dump("DATA", $data);
        $sum = array();
        foreach($data as $name => $ee) {
            foreach ($ee as $job => $arr) {
                foreach($arr as $rate => $a) {
                    foreach($a as $row){
                        $sum[$name][$job][$rate][] = $row[5];
                    }
                }
            }
        }

        //var_dump("SUM", $sum);

        $summed = array();
        foreach($sum as $name => $ee){

            foreach($ee as $job => $arr){
                foreach($arr as $rate => $line){
                    $summed[$name][$job][$rate] = array_sum($line);
                }

            }
        }

        //var_dump("SUMMED", $summed);
        $mysqli = MysqliConfiguration::getMysqli();
        $output = array();
        foreach($data as $name => $ee){
            foreach($ee as $job => $arr){
                foreach($arr as $rate => $line){

                    $queryRate = Rate::getRateByRate($mysqli, $rate);
                    $nameArr = explode(' ', $name);
                    $firstName = $nameArr[0];
                    $lastName = $nameArr[1];
                    $jobCode = $line[0][6];
                    $eeId = $line[0][0];

                    if($eeId === ''){
                        $employee = Employee::getEmployeeByName($mysqli, $firstName, $lastName);

                        if($employee ===  null){
                            throw(new RuntimeException("The employee Id field is blank for $firstName $lastName and cannot be added via the Database, please add employee to the Database or update the file with the Employee Id"));
                        }
                        $eeId = $employee->getEmpId();
                    }

                    //          0                           1              2      3       4         5               6                    7        8                       9  10             15             20          25
                    $output[] = array($eeId, "$firstName $lastName", "","$jobCode", "", $queryRate->getED(), $queryRate->getCode(), "", $summed[$name][$job][$rate], "","","","","","","","","","","","","","","","",$rate);

                }
            }
        }
        /*$mysqli = MysqliConfiguration::getMysqli();
        $output = array();
        foreach($data as $name => $ee) {
            $nameArr = explode(' ', $name);

            if(isset($nameArr[3])){
                $firstName = $nameArr[0];
                $lastName = $nameArr[1];
                $jobType = $nameArr[3];
            }else{
                $firstName = $nameArr[0];
                $lastName = $nameArr[1];
                $jobType = '';
            }

            $employees = Employee::getEmployeeByName($mysqli, $firstName, $lastName);
            var_dump($employees);
            if(count($employees) > 1){
                foreach ($employees as $emp){

                    if($jobType === "Contractor" && strpos($emp->getEmpId(), "C") !== false){
                        $employee = $emp;
                    }else if($jobType === "Employee" && strpos($emp->getEmpId(), "C") === false){
                        $employee = $emp;
                    }else{
                        $employee = $emp;
                    }

                }
            }else{
                $employee = $employees[0];
            }
            var_dump($name,$employee);
            if($employee === null){
                throw(new Exception($firstName ." ". $lastName . " is not in the employee database please add them.<br><a href='addEmployee.php'>Add ". $nameArr[0]."</a>"));
            }
            foreach ($ee as $job => $arr) {

                    foreach($arr as $rate => $line){
                        $date = $line[0][3];
                        $jobDescription = $line[0][7];
                        $jobObj = Job::getJobByJobDescription($mysqli, $jobDescription);
                        if($jobObj === null){
                            throw(new Exception("The job $jobDescription is not in the database, please notify ryan to add it."));
                        }
                        //var_dump($nameArr, $jobDescription, $jobObj);
                        $objCode = $jobObj->getJobCode();
                        //var_dump($nameArr, $jobDescription, $jobObj, $objCode);
                        $queryRate = Rate::getRateByRate($mysqli, $line[0][10]);
                        if($queryRate === null){
                            throw(new Exception("The rate ".$line[0][10]." is not in the database, please notify ryan to add it."));
                        }
                        $objRate = $line[0][10];
                        if(substr($objRate,0,2) === "PT"){
                            $objRate = substr($objRate,11,1);
                        }else if(substr($objRate,0,2) === "OT"){
                            $objRate = substr($objRate,10,1);
                        }else if(substr($objRate,0,2) === "Ra"){
                            $objRate = substr($objRate,5,1);
                        }else if(substr($objRate,0,2) === "SA" || substr($objRate,0,2) === "Sa"){
                            $objRate = "";
                        }
                        //var_dump($objRate);
                        //                        0                           1              2      3       4         5               6                    7        8                       9  10             15             20          25
                        $output[] = array($employee->getEmpId(), "$nameArr[0] $nameArr[1]", "","$objCode", "", $queryRate->getED(), $queryRate->getCode(), "", $summed[$name][$job][$rate], "","","","","","","","","","","","","","","","",$objRate);

                    }
            }
        }*/

        //var_dump($output);


        $today = new DateTime('now');
        $month = $today->format("m");
        $day = $today->format('d');
        $year = $today->format('y');
        $time = $today->format('H-i-s');


        $fileName = "BridgesFiles/Bridges_TSheets_Processed_File_" .$month . "-" . $day . "-" . $year . "-" . $time . ".csv";
        $handle = fopen($fileName, 'wb');


        foreach($output as $arr){
            $count = 0;
            foreach($arr as $line) {
                fwrite($handle, "$line");

                if($count < 25){
                    fwrite($handle, ",");
                }
                $count++;

            }
            fwrite($handle, "\n");
        }
        fclose($handle);

        $_SESSION['output'] = "Successfully created File.";
        $_SESSION['fileName'] = $fileName;
        header("Location: format.php");

    } catch (Exception $e) {
        $_SESSION['output'] = $e->getMessage();
        header('Location: format.php?');
    }
}else{
    $_SESSION['output'] = "<p>No File Was Selected</p>";
    header('Location: format.php');
}
?>