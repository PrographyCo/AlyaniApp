<?

  require '../../../init.php';
  require '../../../db.php';
  if (!is_numeric($_GET['id'])) die();
  $id = $_GET['id'];

  $empinfo = $db->query("SELECT * FROM employees WHERE emp_id = $id")->fetch(PDO::FETCH_ASSOC);
  $chosen_id = $db->query("SELECT design_id FROM chosen_designs WHERE design_type = 2")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: ArbFONTS;
            src: url(ArbFONTS-Mohammad-Bold-normal.ttf);
        }
    </style>
  </head>
  <body style="font-family:ArbFONTS; position: relative;width: 991px;  height: 1403px; margin: 0 auto; color: #555555;">

      <div class="main" style="background-image: url('<?=$chosen_id;?>.png');background-repeat: no-repeat;background-size: 100% 100%;display: block;width: 100%;height: 100%;max-height: 100%;">
        <div style="">
            <div id="pic" style="padding:5px;float: left;width: 100%; margin-top:280px;height:380px;width:320px;margin-left: 55px;background: #fff;border:1px solid #006e60;display: block;">
                <img  src="../../../media/emps_photos/<?=$empinfo['emp_photo'];?>" style="max-width: 320px;width: 320px;float:left;max-height: 380px;" />

            </div>
      </div>
        <div id="details" style="display: block;float: right;width: 100%; text-align: right;padding-top:100px;">
            <h3 style="direction: rtl;font-size: 50px;color:#010103;margin: 60px 0;padding-right: 45px; font-weight: bold;">اسم الموظف: <?=$empinfo['emp_name'];?></h3>
            <h3 style="direction: rtl;font-size: 50px;color:#010103;margin: 60px 0;padding-right: 45px; font-weight: bold;">المسمى الوظيفي: <?=$empinfo['emp_jobtitle'];?></h3>
            <h3 style="direction: rtl;font-size: 50px;color:#010103;margin: 60px 0;padding-right: 45px; font-weight: bold;">الرقم الوظيفي: <?=$empinfo['emp_jobid'];?></h3>
        </div>

      </div>
  </body>
</html>
