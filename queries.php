<html>

<head>
    <title>Problem 2</title>
    <style>
        td {
            background-color: #FFCCCB;
        }
    </style>
</head>

<body>
    <?php
    $link = mysqli_connect("localhost", "root", "");
    if (!$link) {
        die('Connect Error (' . mysqli_connect_errno() . ')' . my_sqli_connect_error());
    } else {
         mysqli_query($link,"CREATE DATABASE Assignment");

        mysqli_select_db($link, "Assignment");
      //  mysqli_query($link, "DROP TABLE Arrivals");
        mysqli_query($link, "CREATE TABLE Arrivals(
                                    Airline varchar(40),
                                    Flight_no varchar(20) primary key,
                                    Flight_date varChar(20),
                                    Schedule_time time null,
                                    Estimate_time time null,
                                    Actual_time time null,
                                    Arriving_From varchar(20),
                                    Via varchar(20),
                                    Terminal int,
                                    Hall int,
                                    Arrival_Status varchar(20)
                                     )");


        require "Classes/PHPExcel.php";

        $path = "arrivals.xls";
        $reader = PHPExcel_IOFactory::createReaderForFile($path);
        $excel_Obj = $reader->load($path);

        $worksheet = $excel_Obj->getSheet('0');


        $lastRow = $worksheet->getHighestRow();
        $colomncount = $worksheet->getHighestDataColumn();
        $colomncount_number = PHPExcel_Cell::columnIndexFromString($colomncount);




        for ($row = 3; $row <= $lastRow; $row++) {
            for ($col = 0; $col < $colomncount_number; $col++) {
                $cell = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row);
                $arr[$col] = $cell->getValue() == "" ? "null" : "'{$cell->getFormattedValue()}'";

                //$arr[$col] = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col) . $row)->getFormattedValue();
            }

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $Terminal = (int) $arr[8];
            $Hall = (int) $arr[9];
            mysqli_query($link, "INSERT INTO Arrivals Values ($arr[0],$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$arr[7],$Terminal,$Hall,$arr[10])");
        }
        if (isset($_GET['all'])) {
            echo "<h2> Arrivals: </h2>";
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $info = mysqli_query($link, "SELECT * FROM Arrivals");
        } else if (isset($_GET['already_landed'])) {
            echo "<h2>Already landed flights: </h2>";
            $info = mysqli_query($link, "SELECT * FROM Arrivals WHERE Arrival_Status = 'landed'");
        } else if (isset($_GET['same_city'])) {
            
                echo "<h2>Flights that landed from : {$_GET['city']} </h2>";
                $info = mysqli_query($link, "SELECT * FROM Arrivals where Arriving_From = '{$_GET['city']}' ");
            
        } else if (isset($_GET['scheduled_after'])) {
            echo "<h2>Flights that are scheduled to arrive after :" . $_GET['time'] . "</h2>";
            $info = mysqli_query($link, "SELECT * FROM Arrivals where Schedule_time >= '$_GET[time]'");
        }

        echo "<table border=1>";
        echo "<tr><th>Airline</th> <th>Flight No.</th> <th>Flight Date</th> <th>Schedule Time</th>
            <th>Estimate Time</th> <th>Actual Time</th> <th>From</th> <th>Via</th>
            <th>Terminal</th> <th>Hall</th> <th>Status</th></tr> ";
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        while ($row = mysqli_fetch_array($info)) {
            echo "<tr>";
            for ($col = 0; $col < $colomncount_number; $col++) {
                echo "<td>" . $row[$col] . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    mysqli_close($link);
    ?>

</body>

</html>