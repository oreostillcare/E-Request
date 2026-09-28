<?php
  require_once "../model/class_model.php";;
	$student_id = require_authenticated_session('student_id');
	if(ISSET($_POST)){
		$conn = new class_model();
		$request_id = trim($_POST['request_id']);
		$req = $conn->delete_request($request_id, $student_id);
		if($req == TRUE){
		    echo '<div class="alert alert-success">Delete Request Successfully!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';

		  }else{
			echo '<div class="alert alert-danger">Delete Request Failed!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';
		}
	}
?>

