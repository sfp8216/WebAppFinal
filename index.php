<!DOCTYPE html>

<html>
<head>
  <title>Web app final (name pending)!</title>
</head>

<body>
  <?php

  //PDO
  $host = 'x';
  $db   = 'x';
  $user = 'x';
  $pass = 'x';
  $charset = 'x';

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  //$opt = [
  //    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  //    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  //    PDO::ATTR_EMULATE_PREPARES   => false,
  //];
  $pdo = new PDO($dsn, $user, $pass);
  $stmt = $pdo->query('SELECT username, password FROM users');
  while ($row = $stmt->fetch()){
          echo $row['username']. " | ". $row['password']."<br/>";
  }


  //Checks
           $username = '';
  $password = '';
  if(!empty($_POST['username']) && !empty($_POST['password'])){
      $username =$_POST['username'];
  $password = $_POST['password'];
  }
  if($username == "admin" && $password=="admin"){
  echo "LOGGED IN";
  }else{
      echo "Invalid username or password";
  }

  //TODO
  // Set up database for username [X] table is users,
  // Columns: uid, username, password
  // Setup token system
  // Styles lol
  //

  ?>

  <form action="index.php" method="post">
    <span>Username:</span><input type="text" name="username"><br>
    <span>Password:</span><input type="password" name="password">
    <hr>
    <button type="submit" name="submitBtn">Login</button><button type="reset">Reset</button>
  </form>
</body>
</html>
