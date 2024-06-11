<?php
//login.php

include('database_connection.php');

if(isset($_SESSION['type']))
{
	header("location:index.php");
}

$message = '';

if(isset($_POST["login"]))
{
	$user_email=$_POST['user_email'];
	$query="SELECT * from user_details where user_email='$user_email'";
    $statement=mysqli_query($connect,$query);
    $count=mysqli_num_rows($statement);

	if($count > 0)
	{
		$result = mysqli_fetch_assoc($statement);
			if($result['user_status'] == 'Active')
			{
				if(password_verify($_POST["user_password"], $result["user_password"]))
				{
					session_start();
					$_SESSION['type'] = $result['user_type'];
					$_SESSION['user_id'] = $result['user_id'];
					$_SESSION['user_name'] = $result['user_name'];
					$_SESSION['user_password'] = $result['user_password'];
					header("location:index.php");
				}
				else
				{
					$message = "<label>Wrong Password</label>";
				}
			}
			else
			{
				$message = "<label>Your account is disabled, Contact Master</label>";
			}
	}
	else
	{
		$message = "<label>Wrong Email Address</labe>";
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Inventory Management System using PHP with Ajax Jquery</title>		
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h2 align="center">Inventory Management System using PHP with Ajax Jquery</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form method="post">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Email</label>
							<input type="text" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="user_password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>