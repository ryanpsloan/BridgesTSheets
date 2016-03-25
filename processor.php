<?php
/**********************************************************************************************************************
  Author: Ryan Sloan
  This process will read a F657 GL .txt file from LCDN and sort the data and analyze the credits and debits and whether or not
  they balance and output the total and whether or not the balance to a web page
  ryan@paydayinc.com
 *********************************************************************************************************************/
/**
Data Example:

Index: 0            1        2          3        4   5-num   6   7    8-DB   9-CR      10-name
PR061015 WK# 24, 99982473, ER WCA,   6/11/2015, 6508, 200,  20,  60,  2.3,       0, AGNES L OLSEN
PR061015 WK# 24, 99982473, NM-SUI,   6/11/2015, 6510, 200,  20,  60, 5.47,       0, AGNES L OLSEN
PR061015 WK# 24, 99982499, NETPAY,   6/11/2015, 1030, 100,   0,   0,    0,  483.07, AMANDA  JARAMILLO
PR061015 WK# 24, 99982499, OASDI,    6/11/2015, 2210, 100,   0,   0,    0,   44.27, AMANDA  JARAMILLO
PR061015 WK# 24, 99982499, ER OASDI, 6/11/2015, 2210, 100,   0,   0,    0,   44.27, AMANDA  JARAMILLO
PR061015 WK# 24, 99982499, MEDICARE, 6/11/2015, 2220, 100,   0,   0,    0,   10.35, AMANDA  JARAMILLO

 *
 **/

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
            //echo "<p>Stream Opened Successfully.</p>";
        }

        //echo "<hr>";

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
            $jobArr[] = $value[5];
        }
        array_multisort($nameArr, SORT_ASC, $jobArr, SORT_ASC, $fileData);


        $_SESSION['fileData'] = $fileData;

        //var_dump($fileData);

        $data = array();
        foreach($fileData as $line){
            //$newDate = new DateTime($line[3]);
            $data[$line[0]." ".$line[1]][$line[5] . " " . $line[6]][] = $line; //[$newDate->format("m-d-Y")][] = $line;
        }

        //var_dump($data);
        $sum = array();
        foreach($data as $name => $ee) {
            foreach ($ee as $job => $arr) {
                foreach($arr as $row) {
                   //$newDate = new DateTime($row[3]);
                   $sum[$name][$job][] = $row[4];//[$newDate->format("m-d-Y")][] = $row[4];

                }
            }
        }

        //var_dump($sum);

        $summed = array();
        foreach($sum as $name => $ee){

            foreach($ee as $job => $arr){
                $summed[$name][$job] = array_sum($arr);
            }
        }
        //var_dump($summed);

        $mysqli = MysqliConfiguration::getMysqli();
        $output = array();
        foreach($data as $name => $ee) {
            $nameArr = explode(' ', $name);
            if($nameArr[0] === ""){
                continue;
            }
            $employee = Employee::getEmployeeByName($mysqli, $nameArr[0], $nameArr[1]);
            //var_dump($employee);
            if($employee === null){
                throw(new Exception($nameArr[0] ." ". $nameArr[1]. " is not in the employee database please add them.<br><a href='addEmployee.php'>Add ". $nameArr[0]."</a>"));
            }
            foreach ($ee as $job => $line) {
                    //var_dump($line[0][8]);
                    $date = $line[0][3];
                    $jobDescription = $line[0][5] . " " . $line[0][6];
                    $jobObj = Job::getJobByJobDescription($mysqli, $jobDescription);
                    //var_dump($nameArr, $jobDescription, $jobObj);
                    $objCode = $jobObj->getJobCode();
                    //var_dump($nameArr, $jobDescription, $jobObj, $objCode);
                    $rate = Rate::getRateByRate($mysqli, $line[0][8]);
                    $objRate = $line[0][8];
                    if(substr($objRate,0,2) === "PT"){
                        $objRate = substr($objRate,10,1);
                    }else if(substr($objRate,0,2) === "OT"){
                        $objRate = substr($objRate,9,1);
                    }else if(substr($objRate,0,2) === "Ra"){
                        $objRate = substr($objRate,5,1);
                    }else if(substr($objRate,0,2) === "SA"){
                        $objRate = "";
                    }
                    //var_dump($rate);
                    //                        0                           1              2      3       4         5               6           7        8                9  10             15             20          25
                    $output[] = array($employee->getEmpId(), "$nameArr[0], $nameArr[1]", "","$objCode", "", $rate->getED(), $rate->getCode(), "", $summed[$name][$job], "","","","","","","","","","","","","","","","",$objRate);

            }
        }

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