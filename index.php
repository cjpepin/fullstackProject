<!DOCTYPE HTML>
<?php session_start() ?>
<html>
<head>
    <meta charset="utf-8" />
    <title>Page Title</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" 
            integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" 
            crossorigin="anonymous"></script> -->
    <script src="js/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.3.2/dist/chart.min.js"></script>
</head>
<body>
    <?php 
    require "database.php";
    $user = $_SESSION['user'];
    $error = "";
    //Test input for harmful content
    function test_in($input){
        $input = trim($input);
        $input = stripslashes($input);
        $input = htmlspecialchars($input);
        return $input;
    }
    #Clean inputs
    $date = test_in($_POST["date"]);
    $lift = test_in($_POST["lift"]);
    $lbsorkg = test_in($_POST["lbsOrKg"]);
    $weight = test_in($_POST["weight"]);
    $reps = test_in($_POST["reps"]);
    $rpe = test_in($_POST["rpe"]);
   // Begin chart
    $squatDates = "";
    $benchDates = "";
    $deadliftDates = "";
    $squatData = "";
    $benchData = "";
    $deadliftData = "";
    $query = "Select  lift, date, weight from user_data order by date asc";
    $result = $mysqli->query($query);
    function rpeMap($rpeNum){
        $rpeMap = [
            "10"=> 1,
            "9.5"=> 0.978,
            "9"=> 0.955,
            "8.5"=> 0.939,
            "8"=> 0.922,
            "7.5"=> 0.907,
            "7"=> 0.892,
            "6.5"=> 0.878,
            "6"=> 0.863,
            "5.5"=> 0.848,
            "5"=> 0.834
        ];
        return $rpeMap[$rpeNum];
    }
    echo rpeMap("6");
    // function e1rm_Calc($weight,$reps,$rpe){
        
    // }
    while($row = mysqli_fetch_array($result)){
        if ($row['lift'] == 'squat'){
            $squatDates = $squatDates . '"' . $row['date'] . '",';
            $squatData = $squatData . '"' . $row['weight'] . '",';
        } elseif($row['lift'] == 'bench') {
            $benchDates = $benchDates . '"' . $row['date'] . '",';
            $benchData = $benchData . '"' . $row['weight'] . '",';
        } elseif($row['lift'] == 'deadlift') {
            $deadliftDates = $deadliftDates . '"' . $row['date'] . '",';
            $deadliftData = $deadliftData . '"' . $row['weight'] . '",';
        }
    }
    $squatDates = trim($squatDates, ",");
    $squatData = trim($squatData, ",");
    $benchDates = trim($benchDates, ",");
    $benchData = trim($benchData, ",");
    $deadliftDates = trim($deadliftDates, ",");
    $deadliftData = trim($deadliftData, ",");

    // echo $squatDates;
    // echo $benchDates;
    // echo $deadliftDates;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        echo "hello $user";

        #check all filled out
        if(empty($date)){
            $error = "Make sure to fill out all sections.";
        } elseif(empty($lift)){
            $error = "Make sure to fill out all sections.";
        } elseif (empty( $lbsorkg)){
            $error = "Make sure to fill out all sections.";
        } elseif (empty($weight)){
            $error = "Make sure to fill out all sections.";
        } elseif (empty( $reps)){
            $error = "Make sure to fill out all sections.";
        } elseif (empty( $rpe)){
            $error = "Make sure to fill out all sections.";
        } else {

        
        // Check if lift has already been done on a certain day
        $stmt1 = $mysqli -> prepare("Select date, lift from user_data having date=? and lift=?");
        $stmt1->bind_param('ss', $date, $lift);
        $stmt1->execute();
        $val1 = $stmt1->fetch();
            if(!$val1){
              
                $stmt = $mysqli -> prepare("insert into user_data (user, date, lift, weight, lborkg, reps, rpe) values (?, ?, ?, ?, ?, ?, ?)");
                
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }

                $stmt->bind_param('sssdsii', $user, $date, $lift, $weight, $lbsorkg, $reps, $rpe);

                $stmt->execute();
                $stmt->close();
                // $alter = "Alter table user_data order by weight asc";
                // $done = $mysqli->query($query);
            } else {
                $error = "You have already done this lift on this day";
            }
     
        }
    }
    
    ?>

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <div class="date-selector">
            <input type="date" name="date">
        </div>
        <div class="dropdowns-wrapper">
            <!-- End calendar, begin lift dropdown -->
            <div class="dropdown-set-one">
                
                    <div class="lift-select">
                        <div class="lift-selected">
                            Lift
                        </div>
                        <select name="lift">
                            <option value=""></option>
                            <option value="squat">Squat</option>
                            <option value="bench">Bench</option>
                            <option value="deadlift">Deadlift</option>
                        </select>
                    </div>
                    <!-- End Lift begin weight-->
                    <div class="weight-select">
                        <div class="lift-selected">
                            Weight
                        </div>
                        <div class="input">
                            <input type="number" name="weight" id="weight" step="any">
                        </div>
                        <div class="lbkg-select">
                            <div class="lift-selected">
                                lbs or kg?
                            </div>
                            <select name="lbsOrKg">
                                <option value=""></option>
                                <option value="lbs">lbs</option>
                                <option value="kg">kg</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- End weight begin reps -->
                    <div class="reps-select">
                        <div class="lift-selected">
                            Reps
                        </div>
                        <div class="input">
                            <input type="number" name="reps" id="reps">
                        </div>
                    </div>
                    <!-- End reps start RPE -->
                    <div class="rpe-select">
                        <div class="lift-selected">
                            RPE
                        </div>
                        <div class="input">
                            <input type="number" name="rpe" id="rpe">
                        </div>
                    </div>
                    <!-- End RPE start lbsorkg -->
                    
                    <div class="button1">
                        <button id="mybtn">Submit!</button>
                    </div>
                    <div class="error">
                        <?php echo htmlentities($error)?> </span>
                    </div>
                </div>
            </form>
        </div>
        <div class="charts">
            <div class="chart">
                <canvas id="chart1"></canvas>
            </div>
            <div class="chart">
                <canvas id="chart2"></canvas>
            </div>
            <div class="chart">
                <canvas id="chart3"></canvas>
            </div>
        </div>
        <script>
            var sch = document.getElementById("chart1").getContext('2d');
            var squatChart = new Chart(sch, {
                type: 'line',
                data: {
                    labels: [<?php echo $squatDates ?>],
                    datasets: [{
                        label: 'Squat',
                        data:  [<?php echo $squatData ?>],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        color: 'rgba(239, 0, 0, 1)',
                        fill: false,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                      
                        y: {
                            beginAtZero: true,
                        }
                    }
                    
                },
                backgroundColor: ['rgb(255,0,0, 1)']
            });
            var bch = document.getElementById("chart2").getContext('2d');
            var benchChart = new Chart(bch, {
                type: 'line',
                data: {
                    labels: [<?php echo $benchDates ?>],
                    datasets: [{
                        label: 'Squat',
                        data:  [<?php echo $benchData ?>],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(81, 163, 0, 1)',
                        color: 'rgba(239, 0, 0, 1)',
                        fill: false,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        // y: {
                        //     beginAtZero: true,
                        // }
                    }
                    
                },
                backgroundColor: ['rgb(255,0,0, 1)']
            });
            var dch = document.getElementById("chart3").getContext('2d');
            var deadliftChart = new Chart(dch, {
                type: 'line',
                data: {
                    labels: [<?php echo $deadliftDates ?>],
                    datasets: [{
                        label: 'Deadlift',
                        data:  [<?php echo $deadliftData ?>],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(13, 13, 251, 1)',
                        color: 'rgba(255, 99, 132, 1)',
                        fill: false,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                        }
                    }
                    
                },
                backgroundColor: ['rgb(255,0,0, 1)']
            });
        </script> 
        <!-- <div class="updating-table">
            <table class="updating-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Lift</th>
                        <th>Weight</th>
                        <th>Reps</th>
                        <th>RPE</th>
                        <th>Estimated 1rm</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            <button id="btnExportToCsv" type ="button" class="button">ProcessData</button> -->
            <!-- <script>
                const dataTable = document.querySelector('.container .updating-table table');
                const exportbutton = document.getElementById('btnExportToCsv');

                exportbutton.addEventListener("click", () => {
                    const exporter = new TableToCSV(dataTable);
                    const csvOutput = exporter.convertToCSV();
                    const csvBlob = new Blob([csvOutput], { type: "text/csv" });
                    const blobUrl = URL.createObjectURL(csvBlob);

                    const anchorEl = document.createElement("a");

                    anchorEl.href = blobUrl;
                    anchorEl.download = "file.csv";
                    anchorEl.click();


                    setTimeout(() => {
                        URL.revokeObjectURL(blobUrl);
                    }, 500);
                });
            </script> -->
        <!-- </div>
        <div class="charts">
            <div class="chart1">
                <canvas id="chart1"></canvas> 
            </div>
            <div class="chart2">
                <canvas id="chart2"></canvas> 
            </div> 
            <div class="chart3">
                <canvas id="chart3"></canvas> 
            </div>
        </div>
        </div>
       -->
    </div>
    
    
</body>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      
    </script>
<script src="main.js">
    
</script>
</html>