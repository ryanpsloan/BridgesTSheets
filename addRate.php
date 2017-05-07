<?php
include_once("/var/www/html/Bridges/php/class/Rate.php");
include_once("/var/www/html/Bridges/php/class/dbConnect.php");
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Rates</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <script>
        $(document).ready(function() {
            var select = $("select[id='rateList']");
            var selectB = $("select[id='rateListB']");

            var rateId = $("input[id='rateId1']");
            var rate = $("input[id='rate1']");
            var ed = $("input[id='ed1']");
            var code = $("input[id='code1']");


            select.on('change',function(){
                var data = $(this).find(':selected').data('rate');
                var embeddedED = $(this).find(':selected').data('ed');
                var embeddedCode = $(this).find(':selected').data('code');
                console.log(data);
                console.log(embeddedED);
                console.log(embeddedCode);
                $("input[id='rateId']").val(data);
                var value = select.val();
                var text = $("#rateList option:selected").text();
                rateId.val(value);
                rate.val(text);
                ed.val(embeddedED);
                code.val(embeddedCode);



            });

            selectB.on('change', function(){
                var value = $(this).find(':selected').val();
                console.log("B",value);
                $("input[id='rateIdB']").val(value);

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
        <li class="active"><a data-toggle="tab" href="#home">Add Rate</a></li>
        <li><a data-toggle="tab" href="#menu1">Update Rate</a></li>
        <li><a data-toggle="tab" href="#menu2">Delete Rate</a></li>
        <li><a data-toggle="tab" href="#menu3">View Rate</a></li>
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
            <h3>Add Rate</h3>
            <p>All fields are required.</p>
            <p>Example { Rate: 'PTO  - Rate 1' , ED: 'E' , Code: '03' } </p>

            <form action="processRate.php" method="POST">
                <table>
                    <tr>
                        <td><label for="rate">Rate</label><input type="text" id="rate" name="rate" required></td>
                        <td><label for="ed">ED</label><input type="text" id="ed" name="ed" required></td>
                        <td><label for="Code">Code</label><input type="text" id="code" name="code" required></td>

                    </tr>
                    <tr><td></td><td><input type="submit" value="Add Rate" id="submit" name="submit"></td><td></td></tr>
                </table>
            </form>
        </div>
        <div id="menu1" class="tab-pane fade center">
            <h3>Update Rate</h3>
            <form action="processRate.php" method="POST">
                <select id="rateList" name="rateList">
                    <option value="None">None Selected</option>
                    <?php
                    try {
                        $mysqli = MysqliConfiguration::getMysqli();
                        //var_dump($mysqli);
                        $rates = Rate::getAllRates($mysqli);
                        //var_dump($rates);
                        if ($rates !== null) {
                            foreach ($rates as $object) {
                                $rateId = $object->getRateId();
                                $rate = $object->getRate();
                                $ed = $object->getED();
                                $code = $object->getCode();
                                echo <<<HTML

                        <option data-rate="$rateId" data-ed="$ed" data-code="$code" value="$rateId">$rate</option>

HTML;


                            }
                        }
                    }catch(Exception $e){
                        echo $e->getMessage();
                    }
                    ?></select>
                <input type="hidden" id="rateId" name="rateId" value="">

                <table>
                    <tr>
                        <td><label for="rateId1">Rate Id</label><input type="text" id="rateId1" name="rateId1" disabled></td>
                        <td><label for="rate1">Rate</label><input type="text" id="rate1" name="rate1" required></td>
                        <td><label for="ed1">ED</label><input type="text" id="ed1" name="ed1" required></td>
                        <td><label for="code1">Code</label><input type="text" id="code1" name="code1" required></td>
                    </tr>
                    <tr><td></td><td><input type="submit" value="Update Rate" id="submit1" name="submit1"></td><td></td></tr>
                </table>
            </form>
        </div>
        <div id="menu2" class="tab-pane fade center">
            <h3>Delete Rate</h3>
            <form action="processRate.php" method="POST">
                <table><tr><td>Select Rate to Delete:</td><td>
                            <select id="rateListB" name="rateListB" required>
                                <option value="None">None Selected</option>
                                <?php
                                $mysqli = MysqliConfiguration::getMysqli();
                                $rates = Rate::getAllRates($mysqli);

                                if($rates !== null) {
                                    foreach ($rates as $object) {
                                        $rateId = $object->getRateId();
                                        $rate = $object->getRate();
                                        $ed = $object->getED();
                                        $code = $object->getCode();
                                        echo <<<HTML

                        <option value="$rateId">$rate</option>
HTML;


                                    }
                                }

                                ?></select></td>


                        <td><input type="submit" value="Delete Rate" id="submit2" name="submit2"></td>
                    </tr>

                </table>
                <input type="hidden" id="rateIdB" name="rateIdB" value="">
            </form>
        </div>
        <div id="menu3" class="tab-pane fade in center">
            <h3>View Rates</h3>
            <table id="viewRatesTable">

                <?php
                $mysqli = MysqliConfiguration::getMysqli();
                $rates = Rate::getAllRates($mysqli);
                if($rates !== null) {
                    echo "<tr><td>Database Id</td><td>Rate</td><td>ED</td><td>Code</td></tr>";
                    foreach ($rates as $object) {
                        $rateId = $object->getRateId();
                        $rate = $object->getRate();
                        $ed = $object->getED();
                        $code = $object->getCode();
                        echo <<<HTML

                    <tr><td>$rateId</td><td>$rate</td><td>$ed</td><td>$code</td></tr>
HTML;

                    }
                }else{
                    echo "<tr><td>No rates to list</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

</main>
</body>
</html>