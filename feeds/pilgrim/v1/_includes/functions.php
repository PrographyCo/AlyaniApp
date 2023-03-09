<?php
    require_once 'db.php';
    
    function validemail($email)
    {
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        } else {
            return false;
        }
    }
    
    function autorotate(Imagick $image)
    {
        
        switch ($image->getImageOrientation()) {
            case Imagick::ORIENTATION_TOPLEFT:
                break;
            case Imagick::ORIENTATION_TOPRIGHT:
                $image->flopImage();
                break;
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateImage("#000", 180);
                break;
            case Imagick::ORIENTATION_BOTTOMLEFT:
                $image->flopImage();
                $image->rotateImage("#000", 180);
                break;
            case Imagick::ORIENTATION_LEFTTOP:
                $image->flopImage();
                $image->rotateImage("#000", -90);
                break;
            case Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateImage("#000", 90);
                break;
            case Imagick::ORIENTATION_RIGHTBOTTOM:
                $image->flopImage();
                $image->rotateImage("#000", 90);
                break;
            case Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateImage("#000", -90);
                break;
            default: // Invalid orientation
                break;
        }
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        return $image;
    }
    
    function scale_image($url)
    {
        
        if (file_exists($url)) {
            $im = new imagick($url);
            
            $width = $im->getImageWidth();
            $height = $im->getImageHeight();
            
            if ($width > 200 || $height > 200) {
                
                $im->scaleImage(200, 200, true);
                $im->setImageCompression(true);
                $im->setCompression(Imagick::COMPRESSION_JPEG);
                $im->setCompressionQuality(90);
                $im->writeImage($url);
                $im->clear();
                $im->destroy();
                
            }
        }
    }
    
    function curl_download($url, $data)
    {
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        
        $result = curl_exec($ch);
        
        return $result;
    }
    
    function GUID()
    {
        
        if (function_exists('com_create_guid') === true) {
            return com_create_guid();
        }
        
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    
    function rand_string($length)
    {
        $str = "";
        $chars = "subinsblogabcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }
        return $str;
    }
    
    // Headers
    
    function headerSuccess()
    {
        
        http_response_code(200);
        
    }
    
    function headerBadRequest()
    {
        
        http_response_code(400);
        
    }
    
    function headerUnauthorized()
    {
        
        http_response_code(401);
        
    }
    
    function headerLogout()
    {
        
        http_response_code(402);
        
    }
    
    function checkParams($data, $expectedParams)
    {
        
        $missing = array();
        
        if (is_array($expectedParams) && count($expectedParams) > 0) foreach ($expectedParams as $paramArray) foreach ($paramArray as $type => $param) if (!array_key_exists($param, $data)) $missing[] = '(' . $type . ')' . $param;
        
        if (count($missing) > 0) {
            
            $items['message'] = 'Missing or invalid parameters: ' . implode(", ", $missing);
            headerBadRequest();
            outputJSON($items);
            exit();
            
        }
        
        return true;
    }
    
    function missingValues($data, $requiredParams)
    {
        
        $missing = array();
        
        if (is_array($requiredParams) && count($requiredParams) > 0) foreach ($requiredParams as $paramArray) foreach ($paramArray as $type => $param) {
            
            if (empty($data[$param])) $missing[] = $param;
            
        }
        
        if (count($missing) > 0) {
            
            $items['message'] = 'Missing values: ' . implode(", ", $missing);
            headerBadRequest();
            echo outputJSON($items);
            exit();
            
        }
        
        return true;
    }
    
    function outputJSON($output)
    {
        
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($output);
        exit();
        
    }
    
    function checkToken($user_type = 0)
    {
        
        global $db;
        
        $headers = apache_request_headers();
        
        if (!isset($headers['Accesstoken'])) {
            
            headerUnauthorized();
            $items['message'] = 'Missing Accesstoken';
            outputJSON($items);
            exit();
            
        } else {
            
            // Accesstoken token found, lets validate
            
            if ($user_type > 0) {
                
                $sql = $db->prepare("SELECT user_id, user_type FROM access_tokens WHERE token = :token AND user_type = :user_type AND expiredate >= :time");
                $sql->bindValue("token", $headers['Accesstoken']);
                $sql->bindValue("user_type", $user_type);
                $sql->bindValue("time", time());
                $sql->execute();
                
            } else {
                
                $sql = $db->prepare("SELECT user_id, user_type FROM access_tokens WHERE token = :token AND expiredate >= :time");
                $sql->bindValue("token", $headers['Accesstoken']);
                $sql->bindValue("time", time());
                $sql->execute();
                
            }
            
            
            if ($sql->rowCount() > 0) {
                
                $userinfo = $sql->fetch(PDO::FETCH_ASSOC);
                
                if ($userinfo['user_type'] == 1) {
                    
                    $return = getPilInfo($userinfo['user_id']);
                    
                } else {
                    
                    $return = getStaffInfo($userinfo['user_id']);
                    
                }
                
                if (!$return) {
                    
                    headerLogout();
                    $items['message'] = 'User not found or inactive';
                    outputJSON($items);
                    exit();
                    
                }
                
                return $return;
                
            } else {
                
                headerLogout();
                $items['message'] = 'Accesstoken not found or expired';
                outputJSON($items);
                exit();
                
            }
            
        }
    }
    
    function requiredInputs(array $fields) {
        foreach ($fields as $field)
        if (empty($_POST[$field]))
        {
            outputJSON(['message' => $field.' must be provided']);
            exit();
        }
    }
    function checkIfToken()
    {
        
        global $db;
        
        $headers = apache_request_headers();
        
        if (!$headers['Accesstoken']) return false;
        else {
            
            // Accesstoken token found, lets validate
            
            if ($user_type > 0) {
                
                $sql = $db->prepare("SELECT user_id, user_type FROM access_tokens WHERE token = :token AND user_type = :user_type AND expiredate >= :time");
                $sql->bindValue("token", $headers['Accesstoken']);
                $sql->bindValue("user_type", $user_type);
                $sql->bindValue("time", time());
                $sql->execute();
                
            } else {
                
                $sql = $db->prepare("SELECT user_id, user_type FROM access_tokens WHERE token = :token AND expiredate >= :time");
                $sql->bindValue("token", $headers['Accesstoken']);
                $sql->bindValue("time", time());
                $sql->execute();
                
            }
            
            
            if ($sql->rowCount() > 0) {
                
                $userinfo = $sql->fetch(PDO::FETCH_ASSOC);
                
                if ($userinfo['user_type'] == 1) {
                    
                    $return = getPilInfo($userinfo['user_id']);
                    
                } else {
                    
                    $return = getStaffInfo($userinfo['user_id']);
                    
                }
                
                if (!$return) {
                    
                    headerLogout();
                    $items['message'] = 'User not found or inactive';
                    outputJSON($items);
                    exit();
                    
                }
                
                return $return;
                
            } else {
                
                headerLogout();
                $items['message'] = 'Accesstoken not found or expired';
                outputJSON($items);
                exit();
                
            }
            
        }
    }
    
    
    function checkLang()
    {
        
        $headers = apache_request_headers();
        
        if (!isset($headers['Lang'])) {
            
            headerUnauthorized();
            $items['message'] = 'Missing Language (ar) (en) (ur)';
            outputJSON($items);
            exit();
            
        } else {
            
            if ($headers['Lang'] == 'ar') require_once '../../../dtMEk/lang/ar.php';
            else require_once '../../../dtMEk/lang/en.php';
            
            return $headers['Lang'];
            
        }
    }
    
    function newlog($page, $get, $post, $files)
    {
        
        $headers = apache_request_headers();
        
        date_default_timezone_set('Africa/Cairo');
        // Log Requests
        $log_data = '';
        $log_data .= date("d/m/Y h:i:s a") . " - ";
        $log_data .= $page . " \n ===================== \n";
        $log_data .= 'GET: ' . json_encode($get) . " \n";
        $log_data .= 'POST: ' . json_encode($post) . " \n";
        $log_data .= 'FILES: ' . json_encode($files) . " \n";
        $log_data .= 'HEADERS: ' . json_encode($headers) . " \n\n<hr />";
        
        $myfile = "logs/" . date("d-m-Y") . "-log.txt";
        if (file_exists($myfile)) {
            $fh = fopen($myfile, 'a+');
            fwrite($fh, $log_data);
        } else {
            $fh = fopen($myfile, 'w+');
            fwrite($fh, $log_data);
        }
        fclose($fh);
        // End of Log Requests
    }
    
    function SQLError($e)
    {
        return 'SQLERROR - DEBUGMODE: ' . $e->getMessage() . ' On Line (' . $e->getLine() . ')';
    }
    
    function getPilInfo($pil_id)
    {
        global $db;
        
        if (is_numeric($pil_id) && $pil_id > 0) {
            
            //updateUserLastActive($pil_id);
            
            $pilInfo = $db->query("SELECT pil_id, pil_name, pil_phone, pil_code, pil_photo, pil_verified, pil_pilc_id, pil_reservation_number FROM pils WHERE pil_id = $pil_id")->fetch(PDO::FETCH_ASSOC);
            $pilInfo['pil_photo'] = PIL_PHOTO_URL . '/' . $pilInfo['pil_photo'];
            return $pilInfo;
            
        } else {
            
            return false;
        }
    }
    
    function getStaffInfo($staff_id)
    {
        global $db;
        $staffInfo = null;
        
        if (is_numeric($staff_id) && $staff_id > 0) {
            
            $staffInfo = $db->query("SELECT staff_id, staff_name, staff_phones, staff_photo, staff_type FROM staff WHERE staff_id = $staff_id")->fetch(PDO::FETCH_ASSOC);
            $staffInfo['staff_photo'] = STAFF_PHOTO_URL . '/' . $staffInfo['staff_photo'];
            $staffInfo['staff_phones'] = explode(",", $staffInfo['staff_phones']);
            
            
        }
        return $staffInfo;
    }
    
    function getStaffInfoWithCity($staff_id, $lang)
    {
        global $db;
        $staffInfo = null;
        
        if (is_numeric($staff_id) && $staff_id > 0) {
            
            $staffInfo = $db->query("SELECT staff_id, staff_name, staff_phones, staff_photo, staff_type FROM staff WHERE staff_id = $staff_id")->fetch(PDO::FETCH_ASSOC);
            $staffInfo['staff_photo'] = STAFF_PHOTO_URL . '/' . $staffInfo['staff_photo'];
            $staffInfo['staff_phones'] = explode(",", $staffInfo['staff_phones']);
            
            // Check if this is a supervisor on a bus, then return the bus info
            $bus_id = $db->query("SELECT bus_id FROM buses WHERE bus_staff_id = " . $staff_id)->fetchColumn();
            if ($bus_id > 0) $staffInfo['bus_title'] = getBusTitle($bus_id, $lang);
            
            // Check if this is a supervisor on a bus, then return the city info
            $city_id = $db->query("SELECT bus_city_id FROM buses WHERE bus_staff_id = " . $staff_id)->fetchColumn();
            if ($city_id > 0) $staffInfo['city'] = getCityInfo($city_id, $lang);
            
        }
        return $staffInfo;
        
    }
    
    function getCityInfo($city_id, $lang)
    {
        
        global $db;
        $cityinfo = $db->query("SELECT city_id, city_title_$lang FROM cities WHERE city_id = $city_id")->fetch(PDO::FETCH_ASSOC);
        
        if ($cityinfo) {
            
            $cityinfo['city_title'] = $cityinfo['city_title_' . $lang];
            unset($cityinfo['city_title_' . $lang]);
            
            return $cityinfo;
            
        } else return "";
    }
    
    function getBusTitle($bus_id, $lang)
    {
        
        global $db;
        $bustitle = $db->query("SELECT bus_title FROM buses WHERE bus_id = $bus_id")->fetchColumn();
        
        if ($bustitle) return $bustitle;
        else return "";
    }
    
    function getGGStaffInfo($ggs_id, $lang)
    {
        global $db;
        $ggsInfo = null;
        
        if (is_numeric($ggs_id) && $ggs_id > 0) {
            
            $ggsInfo = $db->query("SELECT ggs_id, ggs_name, ggs_phones, ggs_photo, ggs_job_$lang FROM general_guide_staff WHERE ggs_id = $ggs_id")->fetch(PDO::FETCH_ASSOC);
            $ggsInfo['ggs_photo'] = GGSTAFF_PHOTO_URL . '/' . $ggsInfo['ggs_photo'];
            $ggsInfo['ggs_phones'] = explode(",", $ggsInfo['ggs_phones']);
            $ggsInfo['ggs_job'] = $ggsInfo['ggs_job_' . $lang];
            unset($ggsInfo['ggs_job_' . $lang]);
            
        }
        return $ggsInfo;
    }
    
    
    function newAccessToken($user_id, $user_type)
    {
        
        global $db;
        
        if (is_numeric($user_id) && $user_id > 0) {
            
            //updateUserLastActive($pil_id);
            
            $token = $token = bin2hex(openssl_random_pseudo_bytes(64));
            $expiredate = time() + (60 * 60 * 24 * 30); //1 month token
            
            // Insert access token to database
            $sqlins = $db->prepare("INSERT INTO access_tokens VALUES (:user_id, :user_type, :token, :expiredate)");
            $sqlins->bindValue("user_id", $user_id);
            $sqlins->bindValue("user_type", $user_type);
            $sqlins->bindValue("token", $token);
            $sqlins->bindValue("expiredate", $expiredate);
            $sqlins->execute();
            
            return $token;
        } else return false;
    }
    
    function updateUserLastActive($user_id)
    {
        
        global $db;
        
        if (is_numeric($user_id) && $user_id > 0) {
            // Update last login
            $db->query("UPDATE users SET user_lastactive = " . time() . " WHERE user_id = '" . $user_id . "'");
            return true;
        }
    }
    
    function sendSMSPil($pil_id, $code)
    {
        
        global $db;
        $return = array();
        
        $newphone = $db->query("SELECT newphone FROM pils_newphones WHERE pil_id = $pil_id")->fetchColumn();
        if ($newphone) {
            sendSMSPilNewPhone($pil_id, $code);
        }
        $pil_phone = $db->query("SELECT pil_phone FROM pils WHERE pil_id = $pil_id")->fetchColumn();
        $pil_phone = trim(str_replace(array("966", "+966"), "", $pil_phone));
        $pil_phone = '966' . $pil_phone;
        $pil_phone = ltrim($pil_phone, '0');
        
        if (is_numeric($pil_phone) && strlen($pil_phone) >= 11 && strlen($pil_phone) <= 13) {
            
            $msg = "Your verification code is " . $code;
            $sms = new Sms('alolayany', 'asdzxc987', 'alolayany');
            $teles = array($pil_phone);
            $result = $sms->send($teles, $msg, 0);
            $return['code'] = $result;
            
        } else $return['code'] = '0';
        
        return $return;
        
    }
    
    function sendSMSPilNewPhone($pil_id, $code)
    {
        
        global $db;
        $return = array();
        $pil_phone = $db->query("SELECT newphone FROM pils_newphones WHERE pil_id = $pil_id")->fetchColumn();
        $pil_phone = trim(str_replace(array("966", "+966"), "", $pil_phone));
        $pil_phone = '966' . $pil_phone;
        $pil_phone = ltrim($pil_phone, '0');
        
        if (is_numeric($pil_phone) && strlen($pil_phone) >= 11 && strlen($pil_phone) <= 13) {
            
            $msg = "Your verification code is " . $code;
            $sms = new Sms('alolayany', 'asdzxc987', 'alolayany');
            $teles = array($pil_phone);
            $result = $sms->send($teles, $msg, 0);
            $return['code'] = $result;
            
        } else $return['code'] = '0';
        
        return $return;
        
    }
    
    
    function sendSMSPilGeneral($pil_id, $message)
    {
        
        global $db;
        $pil_phone = $db->query("SELECT pil_phone FROM pils WHERE pil_id = $pil_id")->fetchColumn();
        $pil_phone = trim(str_replace(array("966", "+966"), "", $pil_phone));
        $pil_phone = '966' . $pil_phone;
        $pil_phone = ltrim($pil_phone, '0');
        
        if (is_numeric($pil_phone) && strlen($pil_phone) >= 11 && strlen($pil_phone) <= 13) {
            
            $msg = $message;
            $sms = new Sms('alolayany', 'asdzxc987', 'alolayany');
            $teles = array($pil_phone);
            $result = $sms->send($teles, $msg, 0);
            $return['code'] = $result;
            
        }
        
    }
    
    function sendSMSStaffGeneral($staff_id, $message)
    {
        
        global $db;
        $staff_phones = $db->query("SELECT staff_phones FROM staff WHERE staff_id = $staff_id")->fetchColumn();
        $phones_array = explode(",", $staff_phones);
        
        if (is_array($phones_array) && count($phones_array) > 0) {
            
            foreach ($phones_array as $phone) {
                
                $phone = trim(str_replace(array("966", "+966"), "", $phone));
                $phone = '966' . $phone;
                $pil_phone = ltrim($pil_phone, '0');
                
                if (is_numeric($phone) && strlen($phone) >= 11 && strlen($phone) <= 13) {
                    
                    $msg = $message;
                    $sms = new Sms('alolayany', 'asdzxc987', 'alolayany');
                    $teles = array($phone);
                    $result = $sms->send($phone, $msg, 0);
                    $return['code'] = $result;
                    
                }
            }
        }
    }
    
    
    function checkCodePil($pil_id, $code)
    {
        
        global $db;
        
        if (is_numeric($pil_id) && is_numeric($code)) {
            
            // ByPass due to SMS error
            //if ($code == 1234) return true;
            
            $chk = $db->query("SELECT pil_id FROM pils_codes WHERE code = $code")->fetchColumn();
            if ($chk == $pil_id || $code == 1234) {
                
                // Verify is Haj and return pil_verified = 1;
                $sqlupd = $db->query("UPDATE pils SET pil_verified = 1 WHERE pil_id = $pil_id");
                
                // Check if he has newphone
                $newphone = $db->query("SELECT newphone FROM pils_newphones WHERE pil_id = $pil_id")->fetchColumn();
                if ($newphone) {
                    
                    $sqlupd2 = $db->prepare("UPDATE pils SET pil_phone = :newphone WHERE pil_id = $pil_id");
                    $sqlupd2->bindValue("newphone", $newphone);
                    $sqlupd2->execute();
                    
                    $sqldel1 = $db->query("DELETE FROM pils_newphones WHERE pil_id = $pil_id");
                    
                }
                return true;
                
            } else return false;
            
        } else return false;
        
    }
