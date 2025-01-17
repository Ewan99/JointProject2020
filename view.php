<?php
session_start();

$host = 'localhost';
$username = 'admin';
$password = '!Wa25Zg3H7Tjsg';
$conn = new mysqli($host, $username, $password);

$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';

$ppsn = $_SESSION['ppsnum'];
$clientno = $_SESSION['clientnum'];

if ($conn->connect_error)
{
  die('Connection failed: ' . $conn->connect_error);
}

$sql = 'CREATE DATABASE IF NOT EXISTS clientsdb;';
if (!$conn->query($sql) === TRUE)
{
  die('Error creating database: ' . $conn->error);
}

$sql = 'USE clientsdb;';
if (!$conn->query($sql) === TRUE)
{
  die('Error using database: ' . $conn->error);
}

$sql = ' CREATE TABLE IF NOT EXISTS `clients`
  (
  `Client No.` int(8) NOT NULL auto_increment,
  `First Name` varchar(250)  NOT NULL,
  `Last Name` varchar(250)  NOT NULL,
  `Address` varchar(250)  NOT NULL,
  `PPSN` varchar(250)  NOT NULL,
  `Phone`  varchar(250)  NOT NULL,
  `Email` varchar(250)  NOT NULL,
  `Password` varchar(250)  NOT NULL,
  `DOB` varchar(250)  NOT NULL,
  `iv` varchar(250)  NOT NULL,
   PRIMARY KEY  (`Client No.`)
  ); ';

if (!$conn->query($sql) === TRUE)
{
  die('Error creating clients database: ' . $conn->error);
}

$sql = ' CREATE TABLE IF NOT EXISTS `quizresults`
  (
  `Entry` int(8) NOT NULL auto_increment,
  `Client No.` int(8) NOT NULL,
  `fever` varchar(250),
  `aches/pains` varchar(250),
  `cough` varchar(250),
  `breathing` varchar(250),
  `smell/taste` varchar(250),
  `fatigue` varchar(250),
  `locations` varchar(250),
  `doctor` varchar(250),
  `iv` varchar(250),
   PRIMARY KEY  (`Entry`)
  ); ';

if (!$conn->query($sql) === TRUE)
{
  die('Error creating quizresults database: ' . $conn->error);
}

if (isset($_POST['changepass']))
{
  $sql = "SELECT `iv` FROM clients WHERE `Client No.` = $clientno";
  $result = $conn->query($sql);
  if (!$conn->query($sql) === TRUE)
  {
    echo('Error updating database: ' . $conn->error);
  }
  while($row = $result->fetch_assoc())
  {
    $iv = hex2bin($row['iv']);
  }
  $newpass = $conn -> real_escape_string($_POST['newpass']);
  $newpass = openssl_encrypt($newpass, $cipher, $key, OPENSSL_RAW_DATA, $iv);
  $newpass_hex = bin2hex($newpass);
  $sql = "UPDATE clients SET `Password` = '$newpass_hex'  where `Client No.` = $clientno;";
  if (!$conn->query($sql) === TRUE)
  {
    echo('Error updating database: ' . $conn->error);
  }
  else {
    echo('Success! Password has been updated');
  }
}

if (isset($_POST['deluser']))
{
  $sql = "DELETE FROM `quizresults` WHERE `Client No.` = $clientno";
  if (!$conn->query($sql) === TRUE)
  {
    echo('Error deleting from quizresults database: ' . $conn->error);
  }
  $sql = "DELETE FROM `clients` WHERE `Client No.` = $clientno";
  if (!$conn->query($sql) === TRUE)
  {
    echo('Error deleting from clients database: ' . $conn->error);
  }
  else
  {
    echo ('<script>alert("Profile & Records deleted successfully! You will now return to the main page");document.location="index.php"</script>');
  }
}

if (isset($_POST['delrecord']))
{
  echo ('<script>alert("Deleting Records...")</script>');
  $sql = "DELETE FROM `quizresults` WHERE `Client No.` = $clientno";
  if (!$conn->query($sql) === TRUE)
  {
    echo('Error creating quizresults database: ' . $conn->error);
  }
  else
  {
    echo ('<script>alert("Records deleted successfully! You will now return to the main page");document.location="index.php"</script>');
  }

}

?>
<html>
<head>
<title>Medical Check-in - View Profile</title>
<link rel="stylesheet" type="text/css" href="view.css">
</style>
</head>
  <body>
    <input style="margin-left:46%" type="button" value="Home" onclick="location.href='index.php';" />

    <div class = "container">
      <h2>User Profile</h2>
      <table><tr id = "header"><th>First Name</th><th>Last Name</th> <th>Home Address</th><th>PPS Number</th> <th>Phone Number</th><th>Email Address</th> <th>Date of Birth</th></tr>

      <?php
      $sql = "SELECT `First Name`, `Last Name`, `Address`, `PPSN`, `Phone`, `Email`, `DOB`, `iv` FROM clients WHERE `Client No.` = $clientno";
      $result = $conn->query($sql);

      while($row = $result->fetch_assoc())
      {
        $iv = hex2bin($row['iv']);

        $fname = hex2bin($row['First Name']);
        $fname = openssl_decrypt($fname, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        $lname = hex2bin($row['Last Name']);
        $lname = openssl_decrypt($lname, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        $address = hex2bin($row['Address']);
        $address = openssl_decrypt($address, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        $phone = hex2bin($row['Phone']);
        $phone = openssl_decrypt($phone, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        $email = hex2bin($row['Email']);
        $email = openssl_decrypt($email, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        $dob = hex2bin($row['DOB']);
        $dob = openssl_decrypt($dob, $cipher, $key, OPENSSL_RAW_DATA, $iv);


        echo "<tr><th>$fname</th><th>$lname</th> <th>$address</th><th>$ppsn</th> <th>$phone</th><th>$email</th> <th>$dob</th></tr>";
      }
    ?>
    </table>
    <br><br>
    <h2>Records</h2>
    <table><tr id = "header"><th>Fever</th><th>Aches/Pains</th> <th>Cough</th><th>Breahting Problems</th> <th>Smell/Taste Problems</th><th>Fatigue</th> <th>Prior Locations</th><th>Doctor</th></tr>

    <?php
    $sql = "SELECT `fever`, `aches/pains`, `cough`, `breathing`, `smell/taste`, `fatigue`, `locations`, `doctor`, `iv` FROM quizresults WHERE `Client No.` = $clientno";

    $result = $conn->query($sql);

    while($row = $result->fetch_assoc())
    {
      $iv = hex2bin($row['iv']);

      $fever = $row['fever'];

      $achespains = $row['aches/pains'];

      $cough = $row['cough'];

      $breathing = $row['breathing'];

      $smelltaste = $row['smell/taste'];

      $fatigue = $row['fatigue'];

      $locations = hex2bin($row['locations']);
      $locations = openssl_decrypt($locations, $cipher, $key, OPENSSL_RAW_DATA, $iv);

      $doctor = hex2bin($row['doctor']);
      $doctor = openssl_decrypt($doctor, $cipher, $key, OPENSSL_RAW_DATA, $iv);


      echo "<tr><th>$fever</th><th>$achespains</th> <th>$cough</th><th>$breathing</th> <th>$smelltaste</th><th>$fatigue</th> <th>$locations</th><th>$doctor</th></tr>";

    }

  ?>
  </table>
</div>
  <div class = "containerBottom">
  <form id="form" method="POST" action="" required>
    <label for="newpass">New Password:</label>
    <input type="text" name="newpass" id="newpass" />
    <input id = "bottomb" type="submit" formmethod="post" value="Change Password" name="changepass" />
    <input id = "bottomb" type="submit" value="Delete Profile" name="deluser" />
    <input id = "bottomb" type="submit" value="Delete Records" name="delrecord" />
  </form>
    </div>
  </body>
</html>
