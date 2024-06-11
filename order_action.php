<?php

//order_action.php

include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add') {
		$query = "
			INSERT INTO inventory_order (user_id, inventory_order_total, inventory_order_date, inventory_order_name, inventory_order_address, payment_status, inventory_order_status, inventory_order_created_date) 
			VALUES (?, ?, ?, ?, ?, ?, ?, ?)
		";
		$statement = $connect->prepare($query);
		if ($statement) {
			$user_id = $_SESSION["user_id"];
			$inventory_order_total = 0;
			$inventory_order_date = $_POST['inventory_order_date'];
			$inventory_order_name = $_POST['inventory_order_name'];
			$inventory_order_address = $_POST['inventory_order_address'];
			$payment_status = $_POST['payment_status'];
			$inventory_order_status = 'active';
			$inventory_order_created_date = date("Y-m-d");
	
			$statement->bind_param("idssssss", $user_id, $inventory_order_total, $inventory_order_date, $inventory_order_name, $inventory_order_address, $payment_status, $inventory_order_status, $inventory_order_created_date);
			$statement->execute();
	
			// Get the last inserted ID
			$inventory_order_id = $statement->insert_id;
	
			if($inventory_order_id) {
				$total_amount = 0;
				for($count = 0; $count < count($_POST["product_id"]); $count++) {
					$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
					$sub_query = "
						INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax) 
						VALUES (?, ?, ?, ?, ?)
					";
					$sub_statement = $connect->prepare($sub_query);
					$sub_statement->bind_param("iiidd", $inventory_order_id, $_POST["product_id"][$count], $_POST["quantity"][$count], $product_details['price'], $product_details['tax']);
					$sub_statement->execute();
	
					$base_price = $product_details['price'] * $_POST["quantity"][$count];
					$tax = ($base_price / 100) * $product_details['tax'];
					$total_amount += ($base_price + $tax);
				}
				$update_query = "
					UPDATE inventory_order 
					SET inventory_order_total = ? 
					WHERE inventory_order_id = ?
				";
				$update_statement = $connect->prepare($update_query);
				$update_statement->bind_param("di", $total_amount, $inventory_order_id);
				$update_statement->execute();
	
				echo 'Order Created...<br />';
				echo $total_amount . '<br />';
				echo $inventory_order_id;
			} else {
				echo 'Failed to create order.';
			}
		} else {
			echo "Statement preparation failed: " . $connect->error;
		}
	}
	

	if($_POST['btn_action'] == 'fetch_single') {
		$query = "
			SELECT * FROM inventory_order WHERE inventory_order_id = ?
		";
		$statement = $connect->prepare($query);
		$statement->bind_param("i", $_POST["inventory_order_id"]);
		$statement->execute();
		$result = $statement->get_result();
		$output = array();
		if ($row = $result->fetch_assoc()) {
			$output['inventory_order_name'] = $row['inventory_order_name'];
			$output['inventory_order_date'] = $row['inventory_order_date'];
			$output['inventory_order_address'] = $row['inventory_order_address'];
			$output['payment_status'] = $row['payment_status'];
		}
		
		$sub_query = "
			SELECT * FROM inventory_order_product 
			WHERE inventory_order_id = ?
		";
		$sub_statement = $connect->prepare($sub_query);
		$sub_statement->bind_param("i", $_POST["inventory_order_id"]); 
		$sub_statement->execute();
		$sub_result = $sub_statement->get_result();
		$product_details = '';
		$count = 0;
		while ($sub_row = $sub_result->fetch_assoc()) {
			$product_details .= '
				<script>
				$(document).ready(function(){
					$("#product_id'.$count.'").selectpicker("val", '.$sub_row["product_id"].');
					$(".selectpicker").selectpicker();
				});
				</script>
				<span id="row'.$count.'">
					<div class="row">
						<div class="col-md-8">
							<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
								'.fill_product_list($connect, $sub_row["product_id"]).'
							</select>
							<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$sub_row["product_id"].'" />
						</div>
						<div class="col-md-3">
							<input type="text" name="quantity[]" class="form-control" value="'.$sub_row["quantity"].'" required />
						</div>
						<div class="col-md-1">
			';
	
			if($count == 0) {
				$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			} else {
				$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
			}
			$product_details .= '
						</div>
					</div>
				</div><br />
				</span>
			';
			$count++;
		}
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}
	

	if($_POST['btn_action'] == 'Edit') {
		$delete_query = "
			DELETE FROM inventory_order_product 
			WHERE inventory_order_id = ?
		";
		$delete_statement = $connect->prepare($delete_query);
		if ($delete_statement) {
			$delete_statement->bind_param("i", $_POST["inventory_order_id"]);
			$delete_statement->execute();
	
			if ($delete_statement->affected_rows >= 0) {
				$total_amount = 0;
				for($count = 0; $count < count($_POST["product_id"]); $count++) {
					$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
					$sub_query = "
						INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax) 
						VALUES (?, ?, ?, ?, ?)
					";
					$sub_statement = $connect->prepare($sub_query);
					if ($sub_statement) {
						$sub_statement->bind_param("iiidd", $_POST["inventory_order_id"], $_POST["product_id"][$count], $_POST["quantity"][$count], $product_details['price'], $product_details['tax']);
						$sub_statement->execute();
	
						$base_price = $product_details['price'] * $_POST["quantity"][$count];
						$tax = ($base_price / 100) * $product_details['tax'];
						$total_amount += ($base_price + $tax);
					} else {
						echo "Sub statement preparation failed: " . $connect->error;
					}
				}
	
				$update_query = "
					UPDATE inventory_order 
					SET inventory_order_name = ?, 
						inventory_order_date = ?, 
						inventory_order_address = ?, 
						inventory_order_total = ?, 
						payment_status = ?
					WHERE inventory_order_id = ?
				";
				$update_statement = $connect->prepare($update_query);
				if ($update_statement) {
					$update_statement->bind_param("sssdsi", $_POST["inventory_order_name"], $_POST["inventory_order_date"], $_POST["inventory_order_address"], $total_amount, $_POST["payment_status"], $_POST["inventory_order_id"]);
					$update_statement->execute();
	
					if($update_statement->affected_rows > 0) {
						echo 'Order Edited...';
					} else {
						echo 'Failed to edit order.';
					}
				} else {
					echo "Update statement preparation failed: " . $connect->error;
				}
			} else {
				echo 'Failed to delete existing products.';
			}
		} else {
			echo "Delete statement preparation failed: " . $connect->error;
		}
	}
	

	if($_POST['btn_action'] == 'delete') {
		$inventory_order_id = $_POST["inventory_order_id"];
		
		$query = "DELETE FROM inventory_order WHERE inventory_order_id = ?";
		$statement = mysqli_prepare($connect, $query);
		
		if ($statement) {
			mysqli_stmt_bind_param($statement, "i", $inventory_order_id);
			if (mysqli_stmt_execute($statement)) {
				echo 'Order successfully deleted';
			} else {
				echo 'Failed to delete Order';
			}
			mysqli_stmt_close($statement);
		} else {
			echo "Statement preparation failed: " . mysqli_error($connect);
		}
	}
	
	
}

?>