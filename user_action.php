<?php

//user_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add') {
		if (filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
			$query = "
			INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status) 
			VALUES (?, ?, ?, ?, ?)
			";
		
			$statement = $connect->prepare($query);

			if ($statement) {
				$user_email = $_POST["user_email"];
				$user_password = password_hash($_POST["user_password"], PASSWORD_DEFAULT);
				$user_name = $_POST["user_name"];
				$user_type = 'user';
				$user_status = 'active';
		
				$statement->bind_param("sssss", $user_email, $user_password, $user_name, $user_type, $user_status);
				$statement->execute();
		
				if($statement->affected_rows > 0) {
					echo 'New User Added';
				} else {
					echo 'Failed to add new user';
				}
			} else {
				echo "Statement preparation failed: " . $connect->error;
			}
		} else {
			echo "Invalid email format";
		}

	}
	
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM user_details WHERE user_id = ?";
		
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["user_id"]);
		$statement->execute();
		$result = $statement->get_result();
		$output = array();
		if ($row = $result->fetch_assoc()) {
			$output['user_email'] = $row['user_email'];
			$output['user_name'] = $row['user_name'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['user_password'] != '')
		{
			$query = "
			UPDATE user_details SET 
				user_name = '".$_POST["user_name"]."', 
				user_email = '".$_POST["user_email"]."',
				user_password = '".password_hash($_POST["user_password"], PASSWORD_DEFAULT)."' 
				WHERE user_id = '".$_POST["user_id"]."'
			";
		}
		else
		{
			$query = "
			UPDATE user_details SET 
				user_name = '".$_POST["user_name"]."', 
				user_email = '".$_POST["user_email"]."'
				WHERE user_id = '".$_POST["user_id"]."'
			";
		}
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->get_result();
		if(isset($result))
		{
			echo 'User Details Edited';
		}
	}
	if($_POST['btn_action'] == 'delete') {
		$status = 'Active';
		if($_POST['status'] == 'Active') {
			$status = 'Inactive';
		}
		$query = "
			UPDATE user_details 
			SET user_status = ? 
			WHERE user_id = ?
		";
		$statement = $connect->prepare($query);
		if ($statement) {
			// Bind parameters and execute
			$statement->bind_param("si", $status, $_POST["user_id"]);
			$statement->execute();
	
			// Check if the statement executed successfully
			if($statement->affected_rows > 0) {
				echo 'User Status changed to ' . $status;
			} else {
				echo 'Failed to change user status';
			}
		} else {
			echo "Statement preparation failed: " . $connect->error;
		}
	}
	
}

?>