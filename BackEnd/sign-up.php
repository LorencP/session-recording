<?php
include_once("/dataLayer.php");
include_once("/errorMessages.php");

if (isset($_POST["firstName"]))
{
$firstName = $_POST["firstName"];
}
if (isset($_POST["lastName"]))
{
$lastName = $_POST["lastName"];
}
if (isset($_POST["email"]))
{
$email = $_POST["email"];
}
if (isset($_POST["password"]))
{
$password = $_POST["password"];
}
if (isset($_POST["confirmPassword"]))
{
$confirmPassword = $_POST["confirmPassword"];
}


if($password != $confirmPassword){
    echo($signUpPasswordMismatch);
    return;
}


//get meetingID from eventID 
$dl = new dataLayer();
$newUser= $dl->signUp($firstName, $lastName, $email, $password);

if($newUser = ""){
    echo($userNotCreatedAtSignUp);
    return;
}else{
    echo(json_encode($brothers));
    return;
}

return;





?>