<?php
//phpinfo();
session_start();

?>
<!DOCTYPE>
<html>
<head>
    <title>Bridges TSheets Formatter</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/css.css">


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
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>


                </ul>

                <ul class="nav navbar-nav navbar-right">

                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>
<main>
<div class="container-fluid">
    <div class="row center border">
        <h3>Bridges TSheets Formatter</h3>
        <div id="notice">
            <p>The Formatter has been reconfigured to take the new file format from Bridges </p>
            <p>Files before 7/10/16 will no longer work</p>
            <p>Headers should be: </p>
            <p>fname,lname,<strong>Full Name</strong>,group,local_date,hours,jobcode_1,jobcode_2,service item,Rate</p>
        </div>
        <a href="format.php">Format File</a>
        <ul id="indexUL">
            <li>
                <a href="addEmployee.php">Add/Edit/Update Employee Information</a>
            </li>
            <li>
                <a href="viewRates.php">View Rate Information</a>
            </li>
            <li>
                <a href="viewJobs.php">View Job Information</a>
            </li>
            <li>

            </li>

        </ul>


    </div>



</div>
</main>
</body>
</html>