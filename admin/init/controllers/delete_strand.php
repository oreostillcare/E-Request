<?php
  require_once "../model/class_model.php";;
	require_authenticated_session('user_id');
	if(ISSET($_POST)){
		$conn = new class_model();
		$strand_id = trim($_POST['strand_id']);
		$strand = $conn->delete_strand($strand_id);
		if($strand == TRUE){
		    echo '<div class="alert alert-success">Delete Strand Successfully!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';

		  }else{
			echo '<div class="alert alert-danger">Delete Strand Failed!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';
		}
	}
?>

