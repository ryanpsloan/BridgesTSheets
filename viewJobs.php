<?php
include_once ("/var/www/html/Bridges/php/class/dbConnect.php");
include_once ("/var/www/html/Bridges/php/class/Job.php");
session_start();



?>
<!DOCTYPE html>
<html>
<head>
    <title>View Rates</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/css.css">

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
    <div><?php if(isset($_SESSION['output'])){ echo $_SESSION['output']; $_SESSION['output'] = "";} ?></div>
    <div class="container-fluid">
        <div class="row center">
            <h3>View Jobs</h3>
            <table id="viewJobsTable">

                <?php
                $mysqli = MysqliConfiguration::getMysqli();
                $jobs = Job::getAllJobs($mysqli);
                if($jobs !== null) {
                    echo "<tr><td>Database Id</td><td>Job Code</td><td>Job Description</td></tr>";
                    foreach ($jobs as $object) {
                        $jobId = $object->getJobId();
                        $jobCode = $object->getJobCode();
                        $jobDesc = $object->getJobDescription();

                        echo <<<HTML

                <tr><td>$jobId</td><td>$jobCode</td><td>$jobDesc</td></tr>
HTML;

                    }
                }else{
                    echo "<tr><td>No jobs to list</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</main>
</body>
</html>
