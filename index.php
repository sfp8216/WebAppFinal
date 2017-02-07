<!DOCTYPE html>

<html>
<head>
  <title>Web app final (name pending)!</title>
</head>

<body>
  <?php
    require "dbconn.php";
  //PDO

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
