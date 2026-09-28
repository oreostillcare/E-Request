<?php
  require_once "../model/class_model.php";;
	if(ISSET($_POST)){
		$conn = new class_model();
		$strand_name = trim($_POST['strand_name']);
		$strand_description = trim($_POST['strand_description']);
		$strand_id = trim($_POST['strand_id']);
		$strand = $conn->edit_strand($strand_name, $strand_description, $strand_id);
		if($strand == TRUE){
		    echo '<div class="alert alert-success">Edit Strand Successfully!</div><script> setTimeout(function() {  window.history.go(-1); }, 1000); </script>';

		  }else{
			echo '<div class="alert alert-danger">Edit Strand Failed!</div><script> setTimeout(function() {  window.history.go(-0); }, 1000); </script>';
		}
	}
?>

