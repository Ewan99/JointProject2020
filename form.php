<?php
$host = 'localhost';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password);

$cipher = 'AES-128-CBC';
$key = 'thebestsecretkey';

if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

if (isset($_POST['delete-everything'])) {
  $sql = 'DROP DATABASE securenotepad;';
  if (!$conn->query($sql) === TRUE) {
    die('Error dropping database: ' . $conn->error);
  }
}

$sql = 'CREATE DATABASE IF NOT EXISTS securenotepad;';
if (!$conn->query($sql) === TRUE) {
  die('Error creating database: ' . $conn->error);
}

$sql = 'USE securenotepad;';
if (!$conn->query($sql) === TRUE) {
  die('Error using database: ' . $conn->error);
}

$sql = 'CREATE TABLE IF NOT EXISTS notes (
id int NOT NULL AUTO_INCREMENT,
iv varchar(32) NOT NULL,
content varchar(256) NOT NULL,
PRIMARY KEY (id));';
if (!$conn->query($sql) === TRUE) {
  die('Error creating table: ' . $conn->error);
}
?>
<html>
<head>
<title>Secure Notepad</title> </head>
<body>
<h1>Secure Notepad</h1>
<?php
if (isset($_POST['new-note'])) {
  $iv = random_bytes(16);
  $escaped_content = $conn -> real_escape_string($_POST['content']);
  $encrypted_content = openssl_encrypt($escaped_content, $cipher, $key, OPENSSL_RAW_DATA, $iv);
  $iv_hex = bin2hex($iv);
  $content_hex = bin2hex($encrypted_content);
  $sql = "INSERT INTO notes (iv, content) VALUES ('$iv_hex', '$content_hex')";
  if ($conn->query($sql) === TRUE) {
    echo '<p><i>New note added!</i></p>';
  } else {
    die('Error creating note: ' . $conn->error);
  }
}
?>
<h2>Create a New Note</h2>

<form method="post">
  <input type="text" id="content" name="content" size="64"><br><br>
  <button type="submit" name="new-note">Create Note</button>
</form>

<h2>List Existing Notes</h2>

<?php
$sql = "SELECT id, iv, content FROM notes";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  echo '<table><tr><th>ID</th><th>Content</th></tr>';
  while($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $iv = hex2bin($row['iv']);
    $content = hex2bin($row['content']);
    $unencrypted_content = openssl_decrypt($content, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    echo "<tr><td>$id</td><td>$unencrypted_content</td></tr>";
  }
  echo '</table>';
} else {
  echo '<p>There are no notes!</p>';
}
?>

<h3>Delete Everything</h3>

<form method="post">
  <button type="submit" name="delete-everything">Delete Everything!</button>
</form>
</body>
</html>
