<?php

$host="localhost";
$user="root";
$pass="";

$con=mysqli_connect($host,$user,$pass,'cur')or die(mysqli_error('connection failed'));

if($con){
   // echo"connection success";
}
else
{
    //echo"Connect failed to connect";
}
?>