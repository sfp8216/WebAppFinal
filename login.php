<!DOCTYPE html>

<html>
<head>
  <script src="http://code.jquery.com/jquery-latest.js">
</script><!--BEGIN THE BOOTSTRAPPENING-->
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"><!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous"><!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
</script>
<!-- Site Imports -->
  <link rel="stylesheet" href="css/styles.css">

  <script type="text/javascript" src="js/SvgElement.js"></script>
  <script type="text/javascript" src="js/Whiteboard.js"></script>
  <script type="text/javascript" src="js/scripts.js"></script>
  <title>TeachBoard - Login!</title>
</head>
<body class="container">
  <div class="text-center">
    <h1>TeachBoard - The Syncable Whiteboard App!</h1><br>
    <br>

    <h3>Please Login or Create an Account to Continue!</h3>
  </div><br>
  <br>

  <div class="row">
    <div class="col-md-2 col-md-offset-4">
      <button type="button" class="btn btn-primary">Login</button>
    </div>

    <div class="col-md-2">
      <button type="button" class="btn btn-primary">Create Account</button>
    </div>
  </div><br>
  <br>

  <div class="row">
    <div id="createForm" class="col-md-6 col-md-offset-4">
      Login!

      <form method="post" id="login" name="login" class="form-inline">
        <span>Username:</span><input type="text" name="username" id="username" class="form-control"><br>
        <span>Password:</span><input type="password" name="password" id="password" class="form-control"><br>
        <button type="button" class="btn btn-primary" name="submitBtn" id="submitBtn">Login</button><button type="reset" class="btn btn-warning">Reset</button>
      </form>
    </div>
  </div><br>
  <br>
  <br>

  <div class="row">
    <div id="createForm" class="col-md-6 col-md-offset-4">
      Create An Account!

      <form method="post" id="createLogin" name="createLogin" class="form-inline">
        <span>Username:</span><input type="text" name="newUser" id="newUser" class="form-control"><br>
        <span>Password:</span><input type="password" name="newPass" id="newPass" class="form-control"><br>
        <button type="button" name="submitBtn" id="newUserBtn" class="btn btn-success">Create</button><button type="reset" class="btn btn-warning">Reset</button>
      </form>
    </div>
  </div>
</body>
</html>
