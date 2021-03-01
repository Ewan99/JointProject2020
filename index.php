<html>
<head>
<title>Medical Check-in - Home</title>
<link rel="stylesheet" type="text/css" href="index.css">
</style>
</head>

<body>
<?php

$host = 'localhost';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password);

$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';

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
  `First Name` varchar(250)  NOT NULL default "",
  `Last Name` varchar(250)  NOT NULL default "",
  `Address` varchar(250)  NOT NULL default "",
  `PPSN` varchar(250),
  `Phone`  varchar(250),
  `Email` varchar(250)  NOT NULL default "",
  `DOB`varchar(250),
  `iv` varchar(250),
   PRIMARY KEY  (`Client No.`)
  ); ';
if (!$conn->query($sql) === TRUE)
{
  die('Error creating database: ' . $conn->error);
}


if (isset($_POST['validate']))
{
	$sql = "SELECT `Client No.`, `PPSN`, `iv` FROM clients";
	$result = $conn->query($sql);
	$found = "false";

	if ($result->num_rows > 0)
	{
	  while($row = $result->fetch_assoc())
		{
				$clientno = $row['Client No.'];
		    $ppsn_hex = hex2bin($row['PPSN']);
		    $iv = hex2bin($row['iv']);
		    $ppsn = openssl_decrypt($ppsn_hex, $cipher, $key, OPENSSL_RAW_DATA, $iv);
				if($ppsn == $_POST['medNum'])
				{
					$found = "true";
				}
		}
	}

  if($found == "false")
  {
     echo '<p style="font-size:25px;color:red;padding-left:450px;">Error! Profile not found...  Have you registered Before? If so, please check your PPS Number again.</p>';
  }
  else
  {
      header("Location: /jp/checkin.php");
  }
}
?>

<div class = "container">
	<form>
		<h2>Medical Check-in</h2>
		<h4>Welcome! Please check-in with a questionnare before your appointment</h4><br>

	 	<div class="inputbox"><label for="MedNum">Medical Card Number:</label>
			 <input type="text" title="Must contain 7 digits followed by one or two characters" name="medNum" required id="MedNum" pattern="(\d{7})([A-Z]{1,2})"/>
	 	</div>

		<input type="submit" formmethod="post" value="Log-in" name="validate" />

		<br><br>
		<h3 style="padding-left:32%;">New user? Register below...</h3>
		<input type="button" value="Register Profile" onclick="location.href='form.php';" />
	</form>
</div>

</body>
</html>
