<?php
    @unlink('install.php');
    @unlink('install2.php');
?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Control Panel Installation</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- iCheck -->
    <link href="plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css"/>
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="index.php"><b>Install New Control Panel</a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">

        <div class="alert alert-success text-center">
            <strong>Well done!</strong> Installation Complete.<br/><br/>You can now <a href="login.php">login</a>
        </div>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

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
