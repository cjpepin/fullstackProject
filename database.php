<?php 

$mysqli = new mysqli('localhost', 'cjpepin', 'Sp!k300123', 'powerliftingProject');

        if($mysqli->connect_errno) {
            printf("Connection Failed: %s\n", $mysqli->connect_error);
            exit;
        }

?>