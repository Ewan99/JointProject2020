<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
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

$sql = ' CREATE TABLE IF NOT EXISTS `quizresults`
  (
  `PPSN` varchar(8),
  `fever` varchar(250),
  `aches/pains` varchar(250),
  `cough` varchar(250),
  `breathing` varchar(250),
  `smell/taste` varchar(250),
  `fatique` varchar(250),
  `locations` varchar(250),
  `iv` varchar(250),
   PRIMARY KEY  (`PPSN`)
  ); ';

if (!$conn->query($sql) === TRUE)
{
  die('Error creating database: ' . $conn->error);
}

$sql = "SELECT `Client No.`,`First Name`, `PPSN`, `iv` FROM clients WHERE `Client No.` = $clientno";
$result = $conn->query($sql);
if ($result->num_rows > 0)
{
  while($row = $result->fetch_assoc()) {
    $id = $row['Client No.'];
    $iv = hex2bin($row['iv']);
    $fname = hex2bin($row['First Name']);
    $fname = openssl_decrypt($fname, $cipher, $key, OPENSSL_RAW_DATA, $iv);
  }
}

if (isset($_POST['validate']))
{
  $ans1 = $_POST['q1'];
  $ans2 = $_POST['q2'];
  $ans3 = $_POST['q3'];
  $ans4 = $_POST['q4'];
  $ans5 = $_POST['q5'];
  $ans6 = $_POST['q6'];
  $ans7 = $_POST['q7'];

  $totalCorrect = 0;

  if ($ans1 == "Yes")
  {
    $totalCorrect++;
  }

  if ($ans2 == "Yes")
  {
    $totalCorrect++;
  }

  if ($ans3 == "Yes")
  {
    $totalCorrect++;
  }

  if ($ans4 == "Yes")
  {
    $totalCorrect++;
  }

  if ($ans5 == "Yes")
  {
    $totalCorrect++;
  }

  if ($ans6 == "Yes")
  {
    $totalCorrect++;
  }

  if($totalCorrect > 3)
  {
    $iv = random_bytes(16);

    $enc_ppsn = openssl_encrypt($ppsn, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $ppsn_hex = bin2hex($enc_ppsn);

    $q1 = $conn -> real_escape_string($_POST['q1']);

    $q2 = $conn -> real_escape_string($_POST['q2']);

    $q3 = $conn -> real_escape_string($_POST['q3']);

    $q4 = $conn -> real_escape_string($_POST['q4']);

    $q5 = $conn -> real_escape_string($_POST['q5']);

    $q6 = $conn -> real_escape_string($_POST['q6']);

    $enc_q7 = openssl_encrypt($q7, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    $q7_hex = bin2hex($enc_q7);

    $iv_hex = bin2hex($iv);
    $sql = "INSERT INTO quizresults (`PPSN`, `fever`, `aches/pains`, `cough`, `breathing`, `smell/taste`, `fatigue`, `locations` `iv`) VALUES ('$ppsn_hex', '$q1', '$q2', '$q3', '$q4', '$q5', '$q6', '$iv_hex')";
    if ($conn->query($sql) === TRUE)
    {
      echo('<p style="font-size:25px;color:red;padding-left:370px;">You have declared numerous symptoms involving COVID-19.  Please return home IMMEDIATELY.  Your results have been uploaded for contact tracing purposes...</p>');
      echo('<br><br><br><br><br><br>');
    }
    else
    {
      echo('<p style="font-size:25px;color:red;padding-left:400px;">You have declared numerous symptoms involving COVID-19.  Please return home IMMEDIATELY.  However, an error prevented the upload of your results...   Error: </p>' . $conn->error);
    }
  }
  else
  {
    echo ('<script>alert("You are not showing significant signs of COVID-19.  Your appointment has been confirmed");document.location="index.php"</script>');
  }
}
?>

<html>
<head>
<title>Medical Check-in - Home</title>
<link rel="stylesheet" type="text/css" href="checkin.css">
</style>
</head>

<body>

  <div class = "container">
  	<form method="POST" action="" id="quiz" required>
  		<h2>COVID Symptoms Check List</h2>
  		<h4 class="header">Welcome <?php echo($fname);?>!
        <br><br> Please complete the following COVID Check-list before your appointment
        <br><br>Please note:  These results and your personal details WILL be kept on record for contact tracing purposes if deemed necessary</h4>
        <br>
      <ol>
        <h3> Within the last 14 days... </h3><br><br>
          <li>
              <h3>Have you experienced a <span style="color:red">fever</span>? (temperature higher than 38 Celsius)?</h3>
              <h4> - Symptoms can include flushed cheeks, feeling fatigued,being warm/hot to touch</h4>
              <div>
                  <label for="q1-Yes"> Yes </label>
                  <input type="radio" name="q1" id="q1-Yes" value="Yes" />
              </div>

              <div>
                  <label for="q1-No"> No </label>
                  <input type="radio" name="q1" id="q1-No" value="No" />
              </div>
          </li>

          <li>
              <h3>Have you experienced any <span style="color:red">aches/pains</span>? and/or <span style="color:red">headaches</span>?</h3>
              <h4> - This can include notable muscular pain, difficulty when moving any parts of your body</h4>
              <div>
                  <label for="q2-Yes"> Yes </label>
                  <input type="radio" name="q2" id="q2-Yes" value="Yes" />
              </div>

              <div>
                  <label for="q2-No"> No </label>
                  <input type="radio" name="q2" id="q2-No" value="No" />
              </div>
          </li>

          <li>
              <h3>Have you experienced a <span style="color:red">cough</span> of any kind?</h3>
              <h4> - This can include any repetitive chesty coughs</h4>
              <div>
                  <label for="q3-Yes"> Yes </label>
                  <input type="radio" name="q3" id="q3-Yes" value="Yes" />
              </div>

              <div>
                  <label for="q3-No"> No </label>
                  <input type="radio" name="q3" id="q3-No" value="No" />
              </div>
          </li>

          <li>
              <h3>Have you experienced any difficulty <span style="color:red">breathing</span>?</h3>
              <h4> - This can include weazing, feeling like your panting, and/or you can't fill your lungs</h4>
              <div>
                  <label for="q4-Yes"> Yes </label>
                  <input type="radio" name="q4" id="q4-Yes" value="Yes" />
              </div>

              <div>
                  <label for="q4-No"> No </label>
                  <input type="radio" name="q4" id="q4-No" value="No" />
              </div>
          </li>

          <li>
              <h3>Have you experienced any loss/change to your sense of <span style="color:red">smell</span> and/or <span style="color:red">taste</span>?</h3>
              <h4> - This can mean foods do not have any taste/smell, or	they taste/smell completely different to normal</h4>
              <div>
                  <label for="q5-Yes"> Yes </label>
                  <input type="radio" name="q5" id="q5-Yes" value="Yes" />
              </div>

              <div>
                  <label for="q5-No"> No </label>
                  <input type="radio" name="q5" id="q5-No" value="No" />
              </div>
          </li>

          <li>
              <h3>Have you experienced any unusual levels of <span style="color:red">fatigue</span>?</h3>
              <h4> - This could be fatigue that prevents you from doing typical non-strenuous tasks</h4>
              <div>
                  <label for="q6-Yes"> Yes </label>
                  <input type="radio" name="q6" id="q6-Yes" value="Yes" />
              </div>

              <div>
                  <label for="q6-No"> No </label>
                  <input type="radio" name="q6" id="q6-No" value="No" />
              </div>
          </li>

          <li>
              <h3>Have you <span style="color:red">travelled</span> anywhere?</h3>
              <h4> - If so, please give location details</h4>
              <div>
                  <label for="q7-Yes"> Yes, </label>
                  <input type="text" name="q7" id="q7-Yes"/>
              </div>

              <div>
                  <label for="q7-No"> No </label>
                  <input type="radio" name="q7" id="q7-No" value="No" />
              </div>
          </li>
      </ol>
  		<input class = "submit" type="submit" formmethod="post" value="Submit" name="validate" />
    </form>

  </div>

</body>
</html>
