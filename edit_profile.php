<?php

//edit_profile.php

include('database_connection.php');

if(isset($_POST['user_name']))
{
	$query = '
    SELECT user_password FROM user_details
    WHERE user_id = "'.$_SESSION["user_id"].'"';

	$statement = $connect->prepare($query);
	$statement->execute();
	$statement->store_result();
	$statement->bind_result($user_password);
	$statement->fetch();

	$flag=1;
	if($_POST["user_new_password"] != '')
	{
		
		if(password_verify($_POST["user_old_password"],$user_password)){
			$query = "
			UPDATE user_details SET 
				user_name = '".$_POST["user_name"]."', 
				user_email = '".$_POST["user_email"]."', 
				user_password = '".password_hash($_POST["user_new_password"], PASSWORD_DEFAULT)."' 
				WHERE user_id = '".$_SESSION["user_id"]."'
			";
		}
		else{
			$flag=0;
		}
	}
	else
	{
		$query = "
		UPDATE user_details SET 
			user_name = '".$_POST["user_name"]."', 
			user_email = '".$_POST["user_email"]."'
			WHERE user_id = '".$_SESSION["user_id"]."'
		";
	}
	
	// $result = $statement->fetch();
	if($flag==1)
	{
		$statement = $connect->prepare($query);
		$statement->execute();
		echo '<div class="alert alert-success">Profile Edited</div>';
	}
	elseif($flag==0){
		echo '<div class="alert alert-warning">Old Password Wrong</div>';
	}
}

?>