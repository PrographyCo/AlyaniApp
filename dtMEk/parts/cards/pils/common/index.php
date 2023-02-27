<?
require '../../../init.php';
require '../../../db.php';
if (!is_numeric($_GET['id'])) die();
$id = $_GET['id'];

$pilinfo = $db->query("SELECT p.*, c.country_title_ar, ci.city_title_ar, cl.pilc_title_ar, cl.pilc_text_id, b.bus_staff_id, b.bus_title FROM pils p
  LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
  LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
  LEFT OUTER JOIN pils_classes cl ON p.pil_pilc_id = cl.pilc_id
  LEFT OUTER JOIN buses b ON p.pil_bus_id = b.bus_id
  WHERE p.pil_id = $id")->fetch(PDO::FETCH_ASSOC);

$campno = $db->query("SELECT pilc_text_text FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();

if ($pilinfo['pilc_text_id'] == 1) $cardtext = 'مخيم';
else $cardtext = 'عماره';

$pilc_design_id = $db->query("SELECT pilc_design_id FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();
$pilc_phones = $db->query("SELECT pilc_phones FROM pils_classes WHERE pilc_id = ".$pilinfo['pil_pilc_id'])->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        @font-face {
            font-family: ArbFONTS;
            src: url(ArbFONTS-Mohammad-Bold-normal.ttf);
        }
        .item-setting{
                font-size: 36px;
                color:#37383a;
                direction: rtl;
                margin: 16px 0;
        }
        #details{
             display: block;
            float: right;
            width: 100%;
            text-align: right;
            padding-top: 84px;
            position: absolute;
            top: 53%
        }
    </style>
  </head>
  <body style="font-family:'ArbFONTS'; position: relative;width: 991px;  height: 1600px; margin: 0 auto; color: #555555;">
      <div class="main" style="background-image: url('<?=$pilc_design_id;?>.png');background-repeat: no-repeat;background-size: 100% 100%;display: block;width: 100%;height: 100%;max-height: 100%;">

    <header style="padding: 50px 0 40px;margin-bottom: 8px;display: block;float: right;width: 100%;">
      <div id="logo" style="display:block;width:100%; float: left;">
        <img src="../../../media/pils_qrcodes/<?=$pilinfo['pil_qrcode'];?>" style="display: block;width:250px;height: 250px;padding-left: 67px;">
      </div>
    </header>
      <div id="pic" style="display: block;float: right;width: 100%; margin-top: 5px">
          <img  src="../../../media/pils/<?=$pilinfo['pil_photo'];?>" style="display: inline-block; float:right;width:383px;margin-right: 60px;height:350px;background:#fff;" />
          <div style="display: inline-block; float:left;width:520px;height:140px;background: #fff;margin-top: 90px;">
              <p style="font-size: 36px;color:#37383a; display: block;width:100%;text-align: right;margin: 0; font-weight: bold;padding: 10px 0;">البرنامج : <?=$pilinfo['pilc_title_ar'];?></p>
              <p style="font-size: 36px;color:#37383a; display: block;width:100%;text-align: right;margin: 0; font-weight: bold;"><?=$cardtext;?> : <span style="; font-family: Verdana"><?=$campno;?></span></p>
          </div>
      </div>
        <div id="details">
           <div class="container row" dir="rtl">
            <h3 class="col-lg-12 item-setting">اسم الحاج:&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana; font-weight:bold"><?=$pilinfo['pil_name'];?></span></h3>
            <h3 class="col-lg-12 item-setting">رقم الهوية:&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana"><?=$pilinfo['pil_nationalid'];?></span></h3>
            <h3 class="col-lg-12 item-setting">رقم الحاج:&nbsp;&nbsp;&nbsp;<span style="font-family: Verdana"><?=$pilinfo['pil_code'];?></span></h3>
            </div>

            <?
            $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '".$pilinfo['pil_code']."'")->fetch(PDO::FETCH_ASSOC);
              if ($accomoinfo) {

              if ($accomoinfo['suite_id'] != 0) {
                     echo '<div class="container row" dir="rtl">';
                      echo '<p  class="col-lg-3 item-setting" >'.menaresidence.'</p>';
       
                // Suites
                $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = ".$accomoinfo['suite_id'])->fetchColumn();
                $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = ".$accomoinfo['hall_id'])->fetchColumn();

                        echo '
                         <p class="col-lg-3 item-setting"> الجناح:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$suite_title.'</span></p>
                        <p class="col-lg-3 item-setting" >الصالة:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$hall_title.'</span></p>
                       '   ;
                        

                if ($accomoinfo['extratype_text']) {
                  
                  $paddingwidth = '25%';
                  if ($accomoinfo['extratype_id'] == 1) echo '<p class="col-lg-3 item-setting">كرسي:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                  elseif ($accomoinfo['extratype_id'] == 2) echo '<p class="col-lg-3 item-setting">مقعد:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                  elseif ($accomoinfo['extratype_id'] == 3) echo '<p class="col-lg-3 item-setting">سرير:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$accomoinfo['extratype_text'].'</span></p>';
                }
                 
                        
                echo '</div>';
              }elseif ($accomoinfo['bld_id'] != 0) {

                // building
                $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = ".$accomoinfo['bld_id'])->fetchColumn();
                $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = ".$accomoinfo['floor_id'])->fetchColumn();
                $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = ".$accomoinfo['room_id'])->fetchColumn();
            echo '<div class="container row" dir="rtl">'; 
             echo '<p class="col-lg-3 item-setting" >'.menaresidence.':</p>';
                echo '<p class="col-lg-3 item-setting" >الغرفة:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$room_title.'</span></p>
                <p class="col-lg-3 item-setting" >الدور:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$floor_title.'</span></p>
                <p class="col-lg-3 item-setting" >المبنى:&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$bld_title.'</span></p>
                ';
             
             echo '</div>';
              }elseif ($accomoinfo['tent_id'] != 0) {

                // tent
                $tent_title = $db->query("SELECT tent_title FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();
                $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = ".$accomoinfo['tent_id'])->fetchColumn();

                if ($tent_type == 1) $tent_text = LBL_TentType1;
                elseif ($tent_type == 2) $tent_text = LBL_TentType2;
             echo '<div class="container row" dir="rtl">';  
                  echo '<p class="col-lg-6 item-setting">'.menaresidence.':</p>';
                echo '<p class="col-lg-6 item-setting">'.$tent_text.':&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$tent_title.'</span></p>';
            
               echo '</div>';
              }
               if ($accomoinfo['halls_id'] != 0) {

                // tent
                $tent_title = $db->query("SELECT tent_title FROM tents WHERE tent_id = ".$accomoinfo['halls_id'])->fetchColumn();
                $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = ".$accomoinfo['halls_id'])->fetchColumn();

                if ($tent_type == 1) $tent_text = LBL_TentType1;
                elseif ($tent_type == 2) $tent_text = LBL_TentType2;
                
                
                  $seattitle = 0;
                  
                  $stmt=$db->prepare("SELECT * FROM pils_accomo WHERE  pil_code = ?");
                                    $stmt->execute(array($pilinfo['pil_code']));
                                    $accomo=$stmt->fetch();
                                    if(!empty($accomo)){
                                        
                                          if($accomo['seats'] != 0){
                                                        
                                                            
                                                            
                                                $stmt=$db->prepare("SELECT * FROM pils_accomo WHERE  halls_id = ? AND seats = ?");
                                                $stmt->execute(array($accomo['halls_id'],1));
                                                $seats=$stmt->fetchAll();
                                                $check = $stmt->rowCount();
                                                if($check > 0){
                                                         foreach($seats as $seat){
                                                        $seattitle++;
                                                        if($seat['pil_code'] == $pilinfo['pil_code']){
                                                            
                                                            break;
                                                        }
                                                    }
                                                }
                                        }else{
                                            $seattitle = without_seat;
                                        }
                                
                                }else{
                                      $seattitle = without_seat;
                                }
                       echo '<div class="container row" dir="rtl">';      
                       
                                    echo '                                     
                                            <p class="col-lg-3 item-setting">'.Arafaresidence.'</p>
                                            <p class="col-lg-4 item-setting">'.$tent_text.':&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$tent_title.'</span></p>
                                            <p class="col-lg-4 item-setting">'.seat.':&nbsp;&nbsp;&nbsp;<span style="font-size:36px; font-weight:bolder; font-family: Verdana">'.$seattitle.'</span></p>
                                    ';
                      echo '</div>';
              }
              

            }
            ?>

        </div>
        <div id="tel" style="position:absolute; left:0; right:0; display: block;width: 100%; text-align: center;bottom: 20px;">
            <span style="color: #fff;font-weight: bold;font-size: 46px; -webkit-text-stroke-width: 2px; -webkit-text-stroke-color: black;"><?=$pilc_phones;?></span>
        </div>
      </div>
  </body>
</html>
