<?

	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	require 'config.inc.php';

	if ($session->logged_in) {

		header("Location: index.php");
		exit();
	}

	?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?=PROJ_TITLE;?> | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
   <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
    <link href="plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<link href="plugins/iCheck/all.css" rel="stylesheet" type="text/css" />
    <link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <link href="plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
    <link href="plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="css/bootstrap-colorpicker.css" media="all" rel="stylesheet" type="text/css" />
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css" />
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <!-- <a href="index.php"><b><?=PROJ_TITLE;?></b></a> -->
				<img src="logo-card-header.png" style="width:250px; padding-top:10%" />
      </div><!-- /.login-logo -->
      <div class="login-box-body">
	      <?
		      if ($session->error) {

			      echo '<div class="text-center">'.$session->error.'</div>';
		      }
		      ?>
        <p class="login-box-msg">Sign in to start your session</p>
        <form method="post">
          <div class="form-group has-feedback">
            <input type="email" class="form-control" placeholder="Email" name="email" id="email" />
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Password" name="password" id="password" />
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">
              <div class="checkbox icheck">
                <label>
                  <input type="checkbox" name="remember" id="remember" /> Remember Me
                </label>
              </div>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div><!-- /.col -->
          </div>
          <input name="sublogin" id="sublogin" type="hidden" value="1" />
        </form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

		<footer>
			<center>
				<strong>Copyright@Alolyani Company <?=date("Y");?>.</strong> All rights reserved. Developed by <b>Atiaf Apps</b>
			</center>
		</footer>

    <!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>
