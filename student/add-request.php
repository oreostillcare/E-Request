       <?php include('main_header/header.php');?>
        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
         <?php include('left_sidebar/sidebar.php');?>
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="container-fluid  dashboard-content">
               <!-- ============================================================== -->
                <!-- pageheader -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                             <h2 class="pageheader-title"><i class="fa fa-fw fa-file"></i> Add Request </h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Request</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader -->
                <!-- ============================================================== -->

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="card influencer-profile-data">
                                        <div class="card-body">
                                             <div class="" id="message"></div>
                                            <form id="validationform" name="docu_forms" data-parsley-validate="" novalidate="" method="POST">
                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right"><i class="fa fa-file"></i> Request Info</label>
                                                </div>
                                                <?php 

                                                  function createRandomcnumber() {
                                                      $chars = "003232303232023232023456789";
                                                      srand((double)microtime()*1000000);
                                                      $i = 0;
                                                      $control = '' ;
                                                      while ($i <= 3) {

                                                        $num = rand() % 33;

                                                        $tmp = substr($chars, $num, 1);

                                                        $control = $control . $tmp;

                                                        $i++;

                                                      }
                                                      return $control;
                                                     }
                                                     $cNumber ='CTRL-'.createRandomcnumber();


                                                ?>
                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Control No.</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum" type="text" value="<?= $cNumber.''.$_SESSION['student_id']; ?>" name="control_no" required="" placeholder="" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                 <?php
                                                      $conn = new class_model();
                                                      $getstudno = $conn->student_profile($student_id);
                                                   ?>
                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Student ID</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum"  name="studentID_no" value="<?= $getstudno['studentID_no']; ?>" type="text" required="" placeholder="" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Document Name</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum"  type="text" name="document_name" required="" placeholder="" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Purpose of Requesting</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum"  type="text" name="purpose_ofrequesting" required="" placeholder="" class="form-control">
                                                    </div>
                                                </div>
                                                 <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">No. of Copies</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum"  type="text" name="no_ofcopies" required="" placeholder="" class="form-control">
                                                    </div>
                                                </div>

                                                 <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Date Request</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum"  type="date" name="date_request" required="" placeholder="" class="form-control">
                                                    </div>
                                                </div>

<!--                                                  <div class="form-group row">
                                                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Date Releasing</label>
                                                    <div class="col-12 col-sm-8 col-lg-6">
                                                        <input data-parsley-type="alphanum"  type="date" name="date_releasing" required="" placeholder="" class="form-control">
                                                    </div>
                                                </div>
 -->
                                                </div>
                                                <div class="form-group row text-right">
                                                    <div class="col col-sm-10 col-lg-9 offset-sm-1 offset-lg-0">
                                                        <input type="text" name="student_id" value="<?= $_SESSION['student_id'];?>" class="form-control" hidden>
                                                        <button type="button" class="btn btn-space btn-primary" id="add-request">Submit</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
           
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <script src="../assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../assets/vendor/parsley/parsley.js"></script>
    <script src="../assets/libs/js/main-js.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
          var firstName = $('#firstName').text();
          var lastName = $('#lastName').text();
          var intials = $('#firstName').text().charAt(0) + $('#lastName').text().charAt(0);
          var profileImage = $('#profileImage').text(intials);
        });
    </script>
      <script>
          document.addEventListener('DOMContentLoaded', () => {
              let btn = document.querySelector('#add-request');
              btn.addEventListener('click', () => {

                  const control_no = document.querySelector('input[name=control_no]').value;
                  const studentID_no = document.querySelector('input[name=studentID_no]').value;
                  const document_name = document.querySelector('input[name=document_name]').value;
                  const purpose_ofrequesting = document.querySelector('input[name=purpose_ofrequesting]').value;
                  const no_ofcopies = document.querySelector('input[name=no_ofcopies]').value;
                  const date_request = document.querySelector('input[name=date_request]').value;
                  // const date_releasing = document.querySelector('input[name=date_releasing]').value;
                  const student_id = document.querySelector('input[name=student_id]').value;

                  var data = new FormData(this.form);

                  data.append('control_no', control_no);
                  data.append('studentID_no', studentID_no);
                  data.append('document_name', document_name);
                  data.append('purpose_ofrequesting', purpose_ofrequesting);
                  data.append('no_ofcopies', no_ofcopies);
                  data.append('date_request', date_request);
                  // data.append('date_releasing', date_releasing);
                  data.append('student_id', student_id);


              if (control_no === '' ||  studentID_no ==='' ||  document_name ==='' ||  purpose_ofrequesting ===''||  no_ofcopies ===''||  date_request ===''){
                      $('#message').html('<div class="alert alert-danger"> Required All Fields!</div>');
                    }else{
                       $.ajax({
                        url: '../init/controllers/add_request.php',
                          type: "POST",
                          data: data,
                          processData: false,
                          contentType: false,
                          async: false,
                          cache: false,
                        success: function(response) {
                          $("#message").html(response);
                           window.scrollTo(0, 0);
                          },
                          error: function(response) {
                            console.log("Failed");
                          }
                      });
                   }

              });
          });
      </script>



<!--     <script>
    $('#form').parsley();
    </script> -->
    <script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
    </script>

</body>
 
</html>