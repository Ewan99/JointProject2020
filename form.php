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


?>
<html>
  <head>
    <title>Medical Check-in - Registration</title>
    <link rel="stylesheet" type="text/css" href="form.css">
    </style>

    <script>
    function checkEmail(input)
    {
    	if(input.value != document.getElementById('Email').value)
    		{
    			input.setCustomValidity('The two email addresses must match!');
    		}
    	else
    		{
    			input.setCustomValidity('');
    		}
    }
    </script>


  </head>

  <body>

    <?php
    if (isset($_POST['new-record'])) {
      $iv = random_bytes(16);

      $enc_fname = $conn -> real_escape_string($_POST['firstname']);
      $enc_fname = openssl_encrypt($enc_fname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $fname_hex = bin2hex($enc_fname);

      $enc_lname = $conn -> real_escape_string($_POST['lastname']);
      $enc_lname = openssl_encrypt($enc_lname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $lname_hex = bin2hex($enc_lname);

      $enc_address = $conn -> real_escape_string($_POST['address']);
      $enc_address = openssl_encrypt($enc_address, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $address_hex = bin2hex($enc_address);

      $enc_ppsn = $conn -> real_escape_string($_POST['medNum']);
      $enc_ppsn = openssl_encrypt($enc_ppsn, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $ppsn_hex = bin2hex($enc_ppsn);

      $enc_phone = $conn -> real_escape_string($_POST['phoneNum']);
      $enc_phone = openssl_encrypt($enc_phone, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $phone_hex = bin2hex($enc_phone);

      $enc_email = $conn -> real_escape_string($_POST['email']);
      $enc_email = openssl_encrypt($enc_email, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $email_hex = bin2hex($enc_email);

      $enc_passwd = $conn -> real_escape_string($_POST['passwd']);
      $enc_passwd = openssl_encrypt($enc_passwd, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $passwd_hex = bin2hex($enc_passwd);

      $enc_dob = $conn -> real_escape_string($_POST['dateofBirth']);
      $enc_dob = openssl_encrypt($enc_dob, $cipher, $key, OPENSSL_RAW_DATA, $iv);
      $dob_hex = bin2hex($enc_dob);

      $iv_hex = bin2hex($iv);
      $sql = "INSERT INTO clients (`Client No.`, `First Name`, `Last Name`, `Address`, `PPSN`, `Phone`, `Email`, `Password`, `DOB`, `iv`) VALUES (NULL, '$fname_hex', '$lname_hex', '$address_hex', '$ppsn_hex', '$phone_hex', '$email_hex', '$passwd_hex', '$dob_hex', '$iv_hex')";
      if ($conn->query($sql) === TRUE)
      {
        echo ('<script>alert("Profile created successfully! You will now return to the main page");document.location="index.php"</script>');
      }
      else
      {
        die('There was an error creating your record.  Please try again!   Error: ' . $conn->error);
      }

    }
    ?>

    <div class = "container">

      <form>
        <h2>Profile Registration Form</h2>
        <h4>Welcome! Please create your medical profile before your appointment</h4><br>
  	     <div class="inputbox"><label for="FirstName">First Name:</label>
  	        <input title="Name must only contain alphabetic characters" type="text" name="firstname" required id="FirstName" pattern="[a-zA-Z|\s|.]+"/>
        </div>

  	     <div class="inputbox"><label for="LastName">Last Name:</label>
  	        <input title="Name must only contain alphabetic characters" type="text" name="lastname" required id="LastName" pattern="[a-zA-Z|\s|.]+"/>
        </div>

  	     <div class="inputbox"><label for="Address">Address:</label>
  	        <input title="Address must only contain the following: a-Z , - , 0-9 , . , , )" type="text" name="address" required id="Address" pattern="[a-zA-Z|,|\s|.|0-9]+"/>
        </div>

  	     <div class="inputbox"><label for="MedNum">Medical Card Number:</label>
  	        <input type="text" title="Must contain 7 digits followed by one or two characters" name="medNum" required id="MedNum" pattern="(\d{7})([A-Z]{1,2})"/>
        </div>

  	    <div class="inputbox"><label for="PhoneNum">Phone Number:</label>
    	     <input title="Valid Format : 0871234567" type="tel" name="phoneNum" required id="PhoneNum" pattern="[0-9]{10}"/>
        </div>

  	    <div class="inputbox"><label for="Email">Email Address:</label>
  	       <input type="email" name="email" required id="Email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" />
        </div>

        <div class="inputbox"><label for="Password">Password:</label>
           <input type="text" name="passwd" required id="passwd" />
       </div>

  	    <div class="inputbox"><label for="VerifyEmail">Confirm Email:</label>
  	       <input type="email" name="verifyEmail" required id="VerifyEmail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" oninput=checkEmail(VerifyEmail) />
        </div>

  	    <div class="inputbox"><label for="DateOfBirth">Date of Birth:</label>
  	       <input type="date" name="dateofBirth" required id="DateOfBirth" oninput="checkAge(DateOfBirth)" />
        </div>
  	    <br>
  	    <div class="myButton">
  	       <input type="submit" formmethod="post" value="Submit Form" name="new-record" />
  	    </div>
      </form>
    </div>

  </body>
</html>
