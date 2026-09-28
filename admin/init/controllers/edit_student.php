<?php
  require_once "../model/class_model.php";
	require_authenticated_session('user_id');

	if(ISSET($_POST)){
		$conn = new class_model();

		$first_name = trim($_POST['first_name']);
		$middle_name = trim($_POST['middle_name']);
		$last_name = trim($_POST['last_name']);
		$strand = trim($_POST['strand']);
	    $grade_level = trim($_POST['grade_level']);
		$date_ofbirth = trim($_POST['date_ofbirth']);
	    $gender = trim($_POST['gender']);
		$complete_address = trim($_POST['complete_address']);
	    $email_address = trim($_POST['email_address']);
		$mobile_number = trim($_POST['mobile_number']);
	    $username = trim($_POST['username']);
		$password = trim($_POST['password']);
	    $account_status = trim($_POST['account_status']);
		$student_id = trim($_POST['student_id']);

		$strand = $conn->edit_student($first_name, $middle_name, $last_name, $strand, $grade_level, $date_ofbirth, $gender, $complete_address, $email_address, $mobile_number, $username, $password, $account_status, $student_id);
		if($strand == TRUE){
		    echo '<div class="alert alert-success">Edit Student Successfully!</div><script> setTimeout(function() {  window.history.go(-1); }, 1000); </script>';

		  }else{
			echo '<div class="alert alert-danger">Edit Student Failed!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';
		}
	}
?>

