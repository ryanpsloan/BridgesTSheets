<?php
include_once("/var/www/html/Bridges/php/class/Employee.php");
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
            var select = $("select[id='employeeList']");
            var selectB = $("select[id='employeeListB']");
            var empId = $("input[id='empId1']");
            var firstName = $("input[id='firstName1']");
            var lastName = $("input[id='lastName1']");
            select.change(function(){
                var data = $(this).find(':selected').data('employee');
                console.log(data);
                $("input[id='employeeId']").val(data);
                var value = select.val();
                var text = $("#employeeList option:selected").text();
                var arr = text.split(" ");
                //console.log(arr);
                empId.val(value);
                firstName.val(arr[0]);
                lastName.val(arr[1]);

            });

            selectB.change(function(){
                var value = $(this).find(':selected').data('employee');
                //console.log(value);
                $("input[id='employeeIdA']").val(value);

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
    <div><?php if(isset($_SESSION['output'])){ echo $_SESSION['output']; $_SESSION['output'] = "";} ?></div>
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#home">Add Employee</a></li>
        <li><a data-toggle="tab" href="#menu1">Update Employee</a></li>
        <li><a data-toggle="tab" href="#menu2">Delete Employee</a></li>
        <li><a data-toggle="tab" href="#menu3">View Employees</a></li>
    </ul>

    <div class="tab-content">
        <div id="home" class="tab-pane fade in active center">
            <h3>Add Employee</h3>
            <p>All fields are required. Employee Id must be unique with no spaces or special characters. First or Last name can contain spaces, periods, commas, dashes and &.</p>
            <form action="processEmployee.php" method="POST">
                <table>
                    <tr>
                        <td><label for="empId">Employee Id</label><input type="text" id="empId" name="empId" required></td>
                        <td><label for="firstName">First Name</label><input type="text" id="firstName" name="firstName" required></td>
                        <td><label for="lastName">Last Name</label><input type="text" id="lastName" name="lastName" required></td>
                    </tr>
                    <tr><td></td><td><input type="submit" value="Add Employee" id="submit" name="submit"></td><td></td></tr>
                </table>
            </form>
        </div>
        <div id="menu1" class="tab-pane fade center">
            <h3>Update Employee</h3>
            <form action="processEmployee.php" method="POST">
            <select id="employeeList" name="employeeList">
                <option value="None">None Selected</option>
            <?php
                $mysqli = MysqliConfiguration::getMysqli();
                $employees = Employee::getAllEmployees($mysqli);
                if($employees !== null) {
                    foreach ($employees as $object) {
                        $employeeId = $object->getEmployeeId();
                        $empId = $object->getEmpId();
                        $name = ucwords($object->getFirstName() . " " . $object->getLastName());
                        echo <<<HTML

                        <option data-employee="$employeeId" value="$empId">$name</option>
HTML;


                    }
                }

            ?></select>
                <input type="hidden" id="employeeId" name="employeeId" value="">
                <table>
                    <tr>
                        <td><label for="empId1">Evolution Id</label><input type="text" id="empId1" name="empId1"></td>
                        <td><label for="firstName1">First Name</label><input type="text" id="firstName1" name="firstName1"></td>
                        <td><label for="lastName1">Last Name</label><input type="text" id="lastName1" name="lastName1"></td>
                    </tr>
                    <tr><td></td><td><input type="submit" value="Update Employee" id="submit1" name="submit1"></td><td></td></tr>
                </table>
            </form>
        </div>
        <div id="menu2" class="tab-pane fade center">
            <h3>Delete Employee</h3>
            <form action="processEmployee.php" method="POST">
                <table><tr><td>Select Employee to Delete:</td><td>
                <select id="employeeListB" name="employeeListB" required>
                    <option value="None">None Selected</option>
                    <?php
                    $mysqli = MysqliConfiguration::getMysqli();
                    $employees = Employee::getAllEmployees($mysqli);

                    if($employees !== null) {
                        foreach ($employees as $object) {

                            $employeeId = $object->getEmployeeId();
                            $empId = $object->getEmpId();
                            $name = ucwords($object->getFirstName() . " " . $object->getLastName());
                            echo <<<HTML

                        <option data-employee="$employeeId" value="$empId">$name</option>
HTML;


                        }
                    }

                    ?></select></td>


                    <td><input type="submit" value="Delete Employee" id="submit2" name="submit2"></td>
                    </tr>

                </table>
                <input type="hidden" id="employeeIdA" name="employeeIdA" value="">
            </form>
        </div>
        <div id="menu3" class="tab-pane fade in center">
            <h3>View Employees</h3>
            <table id="viewEmployeesTable">

            <?php
               $mysqli = MysqliConfiguration::getMysqli();
               $employees = Employee::getAllEmployees($mysqli);
               if($employees !== null) {
                   echo "<tr><td>Database Id</td><td>Evolution Id</td><td>First Name</td><td>Last Name</td></tr>";
                   foreach ($employees as $object) {
                       $employeeId = $object->getEmployeeId();
                       $empId = $object->getEmpId();
                       $firstName = ucwords($object->getFirstName());
                       $lastName = ucwords($object->getLastName());
                       echo <<<HTML

                    <tr><td>$employeeId</td><td>$empId</td><td>$firstName</td><td>$lastName</td></tr>
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
