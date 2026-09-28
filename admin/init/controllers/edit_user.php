<?php
  require_once "../model/class_model.php";
	require_authenticated_session('user_id');
	if(ISSET($_POST)){
		$conn = new class_model();

		$complete_name = trim($_POST['complete_name']);
		$designation = trim($_POST['designation']);
		$email_address = trim($_POST['email_address']);
		$phone_number = trim($_POST['phone_number']);
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$status = trim($_POST['status']);
		$user_id = trim($_POST['user_id']);

		$user = $conn->edit_user($complete_name, $designation, $email_address, $phone_number, $username, $password, $status, $user_id);
		if($user == TRUE){
		    echo '<div class="alert alert-success">Edit User Successfully!</div><script> setTimeout(function() {  window.history.go(-1); }, 1000); </script>';

		  }else{
			echo '<div class="alert alert-danger">Edit User Failed!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';
		}
	}
?>

