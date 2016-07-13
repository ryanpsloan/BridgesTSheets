<?php
include_once("/var/www/html/Bridges/php/class/Job.php");
include_once("/var/www/html/Bridges/php/class/dbConnect.php");
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Employees</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <script>
        $(document).ready(function() {
            var select = $("select[id='jobList']");
            var selectB = $("select[id='jobListB']");
            var jobId = $("input[id='jobId1']");
            var jobCode = $("input[id='jobCode1']");
            var jobDesc = $("input[id='jobDesc1']");


            select.change(function(){
                var data = $(this).find(':selected').data('job');
                var desc = $(this).find(':selected').data('desc');
                console.log(data);
                $("input[id='jobId']").val(data);
                var value = select.val();
                var text = $("#jobList option:selected").text();
                jobId.val(value);
                jobCode.val(text);
                jobDesc.val(desc);



            });

            selectB.change(function(){
                var value = $(this).find(':selected').data('job');
                //console.log(value);
                $("input[id='jobIdA']").val(value);

            });





        });
    </script>
</head>
<body>
<header>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Home</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="#"></a></li>
                    <li><a href="#"></a></li>

                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"></a></li>

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>
<main>
    <div><?php if(isset($_SESSION['output'])){ echo $_SESSION['output']; $_SESSION['output'] = null; echo "<hr/>";} ?></div>
    <ul class="nav nav-tabs center" id="myTabs">
        <li class="active"><a data-toggle="tab" href="#home">Add Job</a></li>
        <li><a data-toggle="tab" href="#menu1">Update Job</a></li>
        <li><a data-toggle="tab" href="#menu2">Delete Job</a></li>
        <li><a data-toggle="tab" href="#menu3">View Job</a></li>
    </ul>
    <script>
        $(document).ready(function(){
            var tabList = $("ul.nav.nav-tabs.center > li > a");

            tabList.click(function(){

                var id = $(this).attr("href");
                localStorage.setItem('hash', id);
                //console.log(localStorage);


            });

            var hash = localStorage.getItem('hash');
            //console.log(has);
            $('#myTabs a[href="' + hash + '"]').tab('show');



        });

    </script>

    <div class="tab-content">
        <div id="home" class="tab-pane fade in active center">
            <h3>Add Job</h3>
            <p>All fields are required.</p>
            <p>Example: Job Code - APS ASB / Job Desciption - Albuquerque Public Schools ASB</p>
            <form action="processJob.php" method="POST">
                <table>
                    <tr>
                        <td><label for="jobCode">Job Code</label><input type="text" id="jobCode" name="jobCode" required></td>
                        <td><label for="jobDesc">Job Description</label><input type="text" id="jobDesc" name="jobDesc" required></td>

                    </tr>
                    <tr><td></td><td><input type="submit" value="Add Job" id="submit" name="submit"></td><td></td></tr>
                </table>
            </form>
        </div>
        <div id="menu1" class="tab-pane fade center">
            <h3>Update Job</h3>
            <form action="processJob.php" method="POST">
                <select id="jobList" name="jobList">
                    <option value="None">None Selected</option>
                    <?php
                    try {
                        $mysqli = MysqliConfiguration::getMysqli();
                        //var_dump($mysqli);
                        $jobs = Job::getAllJobs($mysqli);
                        //var_dump($jobs);
                        if ($jobs !== null) {
                            foreach ($jobs as $object) {
                                $jobId = $object->getJobId();
                                $jobCode = $object->getJobCode();
                                $jobDesc = $object->getJobDescription();

                                echo <<<HTML

                        <option data-job="$jobId" data-desc="$jobDesc" value="$jobId">$jobCode</option>

HTML;


                            }
                        }
                    }catch(Exception $e){
                        echo $e->getMessage();
                    }
                    ?></select>
                <input type="hidden" id="jobId" name="jobId" value="">

                <table>
                    <tr>
                        <td><label for="jobId1">Job Id</label><input type="text" id="jobId1" name="jobId1" disabled></td>
                        <td><label for="jobCode1">Job Code</label><input type="text" id="jobCode1" name="jobCode1" required></td>
                        <td><label for="jobDesc1">Last Name</label><input type="text" id="jobDesc1" name="jobDesc1" required></td>
                    </tr>
                    <tr><td></td><td><input type="submit" value="Update Employee" id="submit1" name="submit1"></td><td></td></tr>
                </table>
            </form>
        </div>
        <div id="menu2" class="tab-pane fade center">
            <h3>Delete Employee</h3>
            <form action="processJob.php" method="POST">
                <table><tr><td>Select Job to Delete:</td><td>
                            <select id=jobListB" name="jobListB" required>
                                <option value="None">None Selected</option>
                                <?php
                                $mysqli = MysqliConfiguration::getMysqli();
                                $jobs = Job::getAllJobs($mysqli);

                                if($jobs !== null) {
                                    foreach ($jobs as $object) {

                                        $jobId = $object->getJobId();
                                        $jobCode = $object->getJobCode();
                                        $jobDesc = $object->getJobDescription();
                                        echo <<<HTML

                        <option data-employee="$jobId" value="jobId">$jobCode</option>
HTML;


                                    }
                                }

                                ?></select></td>


                        <td><input type="submit" value="Delete Employee" id="submit2" name="submit2"></td>
                    </tr>

                </table>
                <input type="hidden" id="jobIdA" name="jobIdA" value="">
            </form>
        </div>
        <div id="menu3" class="tab-pane fade in center">
            <h3>View Employees</h3>
            <table id="viewEmployeesTable">

                <?php
                $mysqli = MysqliConfiguration::getMysqli();
                $employees = Employee::getAllEmployees($mysqli,1);
                if($employees !== null) {
                    echo "<tr><td>Database Id</td><td>Evolution Id</td><td>Last Name</td><td>First Name</td></tr>";
                    foreach ($employees as $object) {
                        $employeeId = $object->getEmployeeId();
                        $empId = $object->getEmpId();
                        $firstName = ucwords($object->getFirstName());
                        $lastName = ucwords($object->getLastName());
                        echo <<<HTML

                    <tr><td>$employeeId</td><td>$empId</td><td>$lastName</td><td>$firstName</td></tr>
HTML;

                    }
                }else{
                    echo "<tr><td>No employees to list</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

</main>
</body>
</html>