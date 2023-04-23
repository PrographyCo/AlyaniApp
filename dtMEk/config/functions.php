<?php /** @noinspection ALL */
    
    require_once 'db.php';
    require_once 'pushfunctions.php';
    
    /**
     * @throws Exception
     */
    function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return com_create_guid();
        }
        
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', random_int(0, 65535), random_int(0, 65535), random_int(0, 65535), random_int(16384, 20479), random_int(32768, 49151), random_int(0, 65535), random_int(0, 65535), random_int(0, 65535));
    }
    
    /**
     * @throws Exception
     */
    function genRandCode($length = 30): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    function time_left(int $seconds): string
    {
        $return = '';
        $seconds = (int)abs($seconds);
        if ($seconds / 60 >= 1) {
            $minutes = floor($seconds / 60);
            
            if ($minutes / 60 >= 1) { # Hours
                
                $hours = floor($minutes / 60);
                
                if ($hours / 24 >= 1) { #days
                    $days = floor($hours / 24);
                    
                    if ($days / 7 >= 1) { #weeks
                        $weeks = floor($days / 7);
                        $return = "$weeks Week" . (($weeks >= 2) ? 's' : '');
                    } #end of weeks
                    
                    $return = implode(',', [$return, "$days day" . (($days >= 2) ? 's' : '')]);
                } #end of days
                
                $hours = $hours - (floor($hours / 24)) * 24;
                $return = implode(',', [$return, "$hours hr" . (($hours >= 2) ? 's' : '')]);
            } #end of Hours
            
            $minutes = $minutes - (floor($minutes / 60)) * 60;
            $return = implode(',', [$return, "$minutes min" . (($minutes >= 2) ? 's' : '')]);
        } #end of minutes
        
        return $return;
    }
    
    function suitesToAcc($gender): bool|array
    {
        global $db;
        return $db->query("SELECT suite_id, suite_title FROM suites WHERE suite_gender = '$gender' AND suite_id IN (SELECT hall_suite_id FROM suites_halls WHERE hall_id IN (" . implode(',', suitesHallsToAcc()) . "))")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function suitesHallsToAcc(): array
    {
        
        global $db;
        $return_halls = array(0);
        $sql = $db->query("SELECT hall_id, hall_capacity FROM suites_halls");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE hall_id = " . $row['hall_id'])->fetchColumn();
            
            if ($occu < $row['hall_capacity']) $return_halls[] = $row['hall_id'];
        }
        
        return $return_halls;
    }
    
    function hallsToAcc($suite_id): array
    {
        
        global $db;
        $return_halls = array();
        $sql = $db->query("SELECT hall_id, hall_title, hall_capacity FROM suites_halls WHERE hall_suite_id = $suite_id");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE hall_id = " . $row['hall_id'])->fetchColumn();
            
            if ($occu < $row['hall_capacity']) $return_halls[] = $row;
            
        }
        
        return $return_halls;
    }
    
    function hallsToAccWithPredefined($suite_id, $halls): array
    {
        
        global $db;
        $return_halls = array();
        $sql = $db->query("SELECT hall_id, hall_title, hall_capacity FROM suites_halls WHERE hall_suite_id = $suite_id AND hall_id IN (" . implode(',', $halls) . ")");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE hall_id = " . $row['hall_id'])->fetchColumn();
            
            if ($occu < $row['hall_capacity']) $return_halls[] = $row;
            
        }
        
        return $return_halls;
    }
    
    function buildingsToAcc($gender, $pil_accomo_type): bool|array
    {
        
        global $db;
        $return = array();
        $sqlmore = "";
        
        if ($pil_accomo_type == 2) $sqlmore = "bld_type = 1";
        elseif ($pil_accomo_type == 5) $sqlmore = "bld_type = 2";
        
        $return = $db->query("SELECT bld_id, bld_title FROM buildings WHERE $sqlmore AND bld_id IN (SELECT floor_bld_id FROM buildings_floors WHERE floor_id IN (SELECT room_floor_id FROM buildings_rooms WHERE room_gender = '$gender' AND room_id IN (" . implode(',', floorsRoomsToAcc()) . ")))")->fetchAll(PDO::FETCH_ASSOC);
        return $return;
    }
    
    function floorsToAcc($bld_id, $gender): bool|array
    {
        global $db;
        return $db->query("SELECT floor_id, floor_title FROM buildings_floors WHERE floor_bld_id = $bld_id AND floor_active = 1 AND floor_id IN (SELECT room_floor_id FROM buildings_rooms WHERE room_gender = '$gender' AND room_id IN (" . implode(',', floorsRoomsToAcc()) . "))")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function floorsRoomsToAcc(): array
    {
        
        global $db;
        $return_rooms = array(0);
        $sql = $db->query("SELECT room_id, room_capacity FROM buildings_rooms WHERE room_active = 1");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE room_id = " . $row['room_id'])->fetchColumn();
            
            if ($occu < $row['room_capacity']) $return_rooms[] = $row['room_id'];
            
        }
        
        return $return_rooms;
    }
    
    function roomsToAcc($floor_id, $gender): array
    {
        
        global $db;
        $return_rooms = array();
        $sql = $db->query("SELECT room_id, room_capacity FROM buildings_rooms WHERE room_floor_id = $floor_id AND room_gender = '$gender' AND room_active = 1");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE room_id = " . $row['room_id'])->fetchColumn();
            
            if ($occu < $row['room_capacity']) $return_rooms[] = $row['room_id'];
            
        }
        
        return $return_rooms;
    }
    
    function roomsToAccWithPredefined($floor_id, $rooms, $gender): array
    {
        
        global $db;
        $return_rooms = array();
        
        if (is_array($rooms) && count($rooms) > 0) $sqlmore1 = " AND room_id IN (" . implode(',', $rooms) . ")";
        
        $sql = $db->query("SELECT room_id, room_capacity FROM buildings_rooms WHERE room_floor_id = $floor_id AND room_gender = '$gender' AND room_active = 1 $sqlmore1");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE room_id = " . $row['room_id'])->fetchColumn();
            
            if ($occu < $row['room_capacity']) $return_rooms[] = $row['room_id'];
            
        }
        
        return $return_rooms;
    }
    
    function tentsToAcc($id, $gender, $type): array
    {
        
        global $db;
        $return_tents = array();
        if ($type == 1) {
            $col = 'tent_id';
        } elseif ($type == 2) {
            $col = 'halls_id';
        }
        $sql = $db->query("SELECT tent_id, tent_title, tent_capacity FROM tents WHERE tent_id = $id AND tent_active = 1 AND type = $type AND tent_gender = '$gender'");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE $col = " . $row['tent_id'])->fetchColumn();
            
            if ($occu < $row['tent_capacity']) $return_tents[] = $row;
            
        }
        
        return $return_tents;
    }
    
    function busesToAcc(): array
    {
        
        global $db;
        $return_buses = array();
        $sql = $db->query("SELECT bus_id, bus_title, bus_seats FROM buses WHERE bus_active = 1");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE bus_id = " . $row['bus_id'])->fetchColumn();
            
            if ($occu < $row['bus_seats']) $return_buses[] = $row;
            
        }
        
        return $return_buses;
    }
    
    function sortPilsFamilies(array $pils)
    {
        $families = [];
        foreach ($pils as $pil) {
            if (!array_key_exists($pil['pil_reservation_number'], $families)) {
                $families[$pil['pil_reservation_number']] = [];
            }
            $families[$pil['pil_reservation_number']][] = $pil;
        }
        return $families;
    }
    
    function getPilCodeForFamily(array $family)
    {
        $pil_codes = [];
        foreach ($family as $pil) {
            $pil_codes[] = $pil['pil_code'];
        }
        return $pil_codes;
    }
    
    function busesToAccWithPredefined($buses, $city_id, $count): array
    {
        
        global $db;
        $return_buses = array();
        
        if (is_array($buses) && count($buses) > 0) $sqlmore1 = " AND bus_id IN (" . implode(',', $buses) . ")";
        
        $sql = $db->query("SELECT bus_id, bus_title, bus_seats FROM buses WHERE bus_active = 1 AND bus_city_id = $city_id $sqlmore1 ORDER BY bus_order");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_bus_id = " . $row['bus_id'])->fetchColumn();
            $seats = ((int)$row['bus_seats']) - (int)$occu;
            if ($seats > 0 && $seats >= $count) $return_buses[] = $row;
            
        }
        
        return $return_buses;
    }
    
    
    function AccomoSuites($suites, $halls, $extratype_id, $pil_code, $pil_gender, $type = 'pil'): bool
    {
        
        global $db;
        
        $available_suites = $db->query("SELECT suite_id FROM suites WHERE suite_gender = '$pil_gender' AND suite_id IN (" . implode(',', $suites) . ") AND suite_id IN (SELECT hall_suite_id FROM suites_halls WHERE hall_id IN (" . implode(',', suitesHallsToAcc()) . "))")->fetchAll(PDO::FETCH_COLUMN);
        if (is_array($available_suites) && count($available_suites) > 0) {
            
            if (is_array($halls) && count($halls) > 0) $available_halls = hallsToAccWithPredefined($available_suites[0], $halls);
            else $available_halls = hallsToAcc($available_suites[0]);
            
            if (is_array($available_halls) && count($available_halls) > 0) {
                
                if ($extratype_id > 0) {
                    
                    $countpils = $db->query("SELECT COUNT(pil_id) FROM pils")->fetchColumn();
                    for ($i = 1; $i <= $countpils; $i++) {
                        
                        $reserved = $db->query("SELECT pil_code FROM pils_accomo WHERE pil_code != '$pil_code' AND suite_id = " . $available_suites[0] . " AND hall_id = " . $available_halls[0]['hall_id'] . " /* AND extratype_id = " . $_POST['extratype_id'] . " */ AND extratype_text = '$i'")->fetchColumn();
                        if (!$reserved) {
                            
                            $extratype_text = $i;
                            break;
                            
                        }
                    }
                    
                } else {
                    $extratype_text = '';
                }
                
                // Insert
                
                $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
                                 :pil_code,
                                 :type,
                                 1,
                                 :suite_id,
                                 :hall_id,
                                 0,
                                 0,
                                 0,
                                 0,
                                 0,
                                 0,
                                 0,
                                 :extratype_id,
                                 :extratype_text
                               ) ON DUPLICATE KEY UPDATE pil_accomo_type = 1, suite_id = :suite_id, hall_id = :hall_id, bld_id = 0, floor_id = 0, room_id = 0, tent_id = 0, halls_id = 0 , seats = 0,  bus_id = 0, extratype_id = :extratype_id, extratype_text = :extratype_text");
                
                $sqlac->bindValue("pil_code", $pil_code);
                $sqlac->bindValue("type", $type);
                $sqlac->bindValue("suite_id", $available_suites[0]);
                $sqlac->bindValue("hall_id", $available_halls[0]['hall_id']);
                $sqlac->bindValue("extratype_id", $extratype_id);
                $sqlac->bindValue("extratype_text", $extratype_text);
                $sqlac->execute();
                
                
                return true;
                
            } else return false;
            
        } else return false;
        
    }
    
    function AccomoBuildings($buildings, $floors, $rooms, $pil_code, $pil_gender, $type = 'pil'): bool
    {
        
        global $db;
        
        $available_buildings = $db->query("SELECT bld_id, bld_type FROM buildings WHERE bld_id IN (SELECT floor_bld_id FROM buildings_floors WHERE floor_id IN (SELECT room_floor_id FROM buildings_rooms WHERE room_gender = '$pil_gender' AND room_id IN (" . implode(',', floorsRoomsToAcc()) . ")))")->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($available_buildings) && count($available_buildings) > 0) {
            
            if (is_array($floors) && count($floors) > 0) $sqlmore1 = " AND floor_id IN (" . implode(',', $floors) . ")";
            $available_floors = $db->query("SELECT floor_id FROM buildings_floors WHERE floor_bld_id = " . $available_buildings[0]['bld_id'] . " AND floor_active = 1 $sqlmore1 AND floor_id IN (SELECT room_floor_id FROM buildings_rooms WHERE room_gender = '$pil_gender' AND room_id IN (" . implode(',', floorsRoomsToAcc()) . "))")->fetchAll(PDO::FETCH_COLUMN);
            
            if (is_array($available_floors) && count($available_floors) > 0) {
                
                if (is_array($rooms) && count($rooms) > 0) $available_rooms = roomsToAccWithPredefined($available_floors[0], $rooms, $pil_gender);
                else $available_rooms = roomsToAcc($available_floors[0], $pil_gender);
                
                if (is_array($available_rooms) && count($available_rooms) > 0) {
                    
                    $extratype_id = $available_buildings[0]['bld_type'];
                    
                    // Insert
                    
                    $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
                                    :pil_code,
                                    :type,
                                    2,
                                    0,
                                    0,
                                    :bld_id,
                                    :floor_id,
                                    :room_id,
                                    0,
                                    0,
                                    0,
                                    0,
                                    :extratype_id,
                                    ''
                                ) ON DUPLICATE KEY UPDATE pil_accomo_type = 2, suite_id = 0, hall_id = 0, bld_id = :bld_id, floor_id = :floor_id, room_id = :room_id, tent_id = 0, halls_id = 0 , seats = 0, bus_id = 0, extratype_id = :extratype_id, extratype_text = ''");
                    
                    $sqlac->bindValue("pil_code", $pil_code);
                    $sqlac->bindValue("type", $type);
                    $sqlac->bindValue("bld_id", $available_buildings[0]['bld_id']);
                    $sqlac->bindValue("floor_id", $available_floors[0]);
                    $sqlac->bindValue("room_id", $available_rooms[0]);
                    $sqlac->bindValue("extratype_id", $extratype_id);
                    
                    $sqlac->execute();
                    
                    return true;
                    
                } else return false;
            } else return false;
        } else return false;
        
    }
    
    function AccomoTents($tents, $pil_code, $pil_gender, $type = 'pil'): bool
    {
        
        global $db;
        
        
        $available_tents = tentsToAcc($tents, $pil_gender, 1);
        if (is_array($available_tents) && count($available_tents) > 0) {
            
            // Insert
            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
                                :pil_code,
                                :type,
                                3,
                                0,
                                0,
                                0,
                                0,
                                0,
                                :tent_id,
                                0,
                                0,
                                0,
                                '',
                                ''
                             ) ON DUPLICATE KEY UPDATE pil_accomo_type = 3, suite_id = 0, hall_id = 0, bld_id = 0, floor_id = 0, room_id = 0, tent_id = :tent_id,  halls_id = 0 , seats = 0, bus_id = 0, extratype_id = '', extratype_text = ''");
            
            $sqlac->bindValue("pil_code", $pil_code);
            $sqlac->bindValue("pil_code", $type);
            $sqlac->bindValue("tent_id", $available_tents[0]['tent_id']);
            
            $sqlac->execute();
            
            return true;
            
        } else return false;
        
        
    }
    
    
    function Accomohallsarfa($tents, $pil_code, $pil_gender, $seats, $type = "pil"): bool
    {
        global $db;
        
        $available_tents = tentsToAcc($tents, $pil_gender, 2);
        
        if (is_array($available_tents) && count($available_tents) > 0) {
            
                // Insert
            $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
                        :pil_code,
                        :type,
                        10,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        :halls_id,
                        :seats,
                        0,
                        '',
                        ''
                     ) ON DUPLICATE KEY UPDATE pil_accomo_type = 10, suite_id = 0, hall_id = 0, bld_id = 0, floor_id = 0, room_id = 0, tent_id = 0,  halls_id = :halls_id , seats = :seats, bus_id = 0, extratype_id = '', extratype_text = ''");
            
                $sqlac->bindValue("pil_code", $pil_code);
                $sqlac->bindValue("type", $type);
                $sqlac->bindValue("halls_id", $available_tents[0]['tent_id']);
                $sqlac->bindValue("seats", $seats);
                $sqlac->execute();
            
            return true;
            
        } else return false;
        
    }
    
    
    function AccomoBuses($buses, $pil_codes = [], $pil_gender, $pil_city_id, $city_id, $type = 'pil')
    {
        
        global $db;
        
        $available_buses = busesToAccWithPredefined($buses, $city_id, count($pil_codes));
        if (is_array($available_buses) && count($available_buses) > 0) {
            
            foreach ($pil_codes as $pil_code) {
                
                $sqlac = $db->prepare("UPDATE pils SET pil_bus_id = :bus_id WHERE pil_code = :pil_code");
                
                $sqlac->bindValue("pil_code", $pil_code);
                $sqlac->bindValue("bus_id", $available_buses[0]['bus_id']);
                $sqlac->execute();
                
                $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE pil_code = ? AND type = ?");
                $stmt->execute(array($pil_code, $type));
                $check = $stmt->rowCount();
                
                if ($check > 0) {
                    // Update
                    $sqlac = $db->prepare("UPDATE pils_accomo SET bus_id = ? WHERE pil_code = ? AND type = ?");
                    $sqlac->execute(array($available_buses[0]['bus_id'], $pil_code, $type));
                    
                    $result = LBL_ACCOMO_UPDATED;
                    
                    
                } else {
                    // Insert
                    $sqlac = $db->prepare("INSERT INTO pils_accomo VALUES (
                            :pil_code,
                            :type,
                            5,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            0,
                            :bus_id,
                            '',
                            '') ON DUPLICATE KEY UPDATE pil_accomo_type = 5, suite_id = 0, hall_id = 0, bld_id = 0, floor_id = 0, room_id = 0, tent_id = 0,  halls_id = 0 , seats = 0, bus_id = :bus_id, extratype_id = '', extratype_text = ''");
                    
                    $sqlac->bindValue("pil_code", $pil_code);
                    $sqlac->bindValue("type", $type);
                    $sqlac->bindValue("bus_id", $available_buses[0]['bus_id']);
                    $sqlac->execute();
                    
                }
            }
            
            return $available_buses[0]['bus_id'];
            
        } else return false;
        
    }
    
    function sendSMSPilGeneral($pil_id, $message): bool
    {
        
        global $db;
        $pil_phone = $db->query("SELECT pil_phone FROM pils WHERE pil_id = $pil_id")->fetchColumn();
        $pil_phone = trim(str_replace(array("966", "+966"), "", $pil_phone));
        $pil_phone = '966' . $pil_phone;
        $return = false;
        
        if (is_numeric($pil_phone) && strlen($pil_phone) >= 11 && strlen($pil_phone) <= 13) {
            
            //$msg = urlencode($message);
            $sms = new Sms('alolayany', 'asdzxc987', 'alolayany');
            $teles = array($pil_phone);
            $return = $sms->send($teles, $message, 0);
            
        }
        
        return $return;
        
    }
    
    function sendSMSStaffGeneral($staff_id, $message): void
    {
        
        global $db;
        $staff_phones = $db->query("SELECT staff_phones FROM staff WHERE staff_id = $staff_id")->fetchColumn();
        $phones_array = explode(",", $staff_phones);
        
        if (is_array($phones_array) && count($phones_array) > 0) {
            
            foreach ($phones_array as $phone) {
                
                $phone = trim(str_replace(array("966", "+966"), "", $phone));
                $phone = '966' . $phone;
                
                if (is_numeric($phone) && strlen($phone) >= 11 && strlen($phone) <= 13) {
                    
                    //$msg = urlencode($message);
                    $sms = new Sms('alolayany', 'asdzxc987', 'alolayany');
                    $teles = array($phone);
                    $result = $sms->send($teles, $message, 0);
                    $return['code'] = $result;
                    
                }
            }
        }
    }
    
    function sendWelcomeMessagePil($pil_id): void
    {
        
        global $db;
        
        $message = trim($db->query("SELECT s_value FROM settings WHERE s_id = 12")->fetchColumn());
        if ($message) {
            
            $pil_name = $db->query("SELECT pil_name FROM pils WHERE pil_id = $pil_id")->fetchColumn();
            $message = str_replace("{{name}}", $pil_name, $message);
            sendSMSPilGeneral($pil_id, $message);
            
        }
        
    }
    
    function AvailableToAccomo($suites, $halls, $bld_type, $buildings, $floors, $rooms, $tents, $gender, $halls_arfa)
    {
// return $halls_arfa;
        global $db;
        $totals = 0;
        $sqlmore1 = $sqlmore2 = '';
        
        // Suites && Halls
        if (is_array($suites)) {
            
            if ($gender) $sqlmore1 = "AND hall_suite_id IN (SELECT suite_id FROM suites WHERE suite_active = 1 AND suite_gender = '$gender')";
            if (is_array($halls) && count($halls) > 0) $sqlmore2 = "AND hall_id IN (" . (implode(',', $halls)) . ")";
            
            $sql = $db->query("SELECT hall_id, hall_capacity FROM suites_halls WHERE hall_active = 1 AND hall_suite_id IN (" . implode(',', $suites) . ") $sqlmore1 $sqlmore2");
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE hall_id = " . $row['hall_id'])->fetchColumn();
                $totals += $row['hall_capacity'] - $occu;
                
            }
            
        }
        
        // Buildings / Floors / Rooms
        if ($bld_type > 0) {
            
            $sqlmore = ["room_floor_id IN (SELECT floor_id FROM buildings_floors WHERE floor_bld_id IN (SELECT bld_id FROM buildings WHERE bld_type = $bld_type))"];
            
            if (is_array($buildings) && !empty($buildings)) $sqlmore[] = "room_floor_id IN (SELECT floor_id FROM buildings_floors WHERE floor_bld_id IN (" . implode(',', $buildings) . "))";
            if (is_array($floors) && !empty($floors)) $sqlmore[] = "room_floor_id IN (" . implode(',', $floors) . ")";
            if (is_array($rooms) && !empty($rooms)) $sqlmore[] = "room_id IN (" . implode(',', $rooms) . ")";
            if ($gender) $sqlmore[] = "room_gender = '$gender'";
            
            $query = "SELECT room_id, room_capacity FROM buildings_rooms WHERE " . implode(' AND ', $sqlmore);
            $sql = $db->query($query);
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE room_id = " . $row['room_id'])->fetchColumn();
                $totals += $row['room_capacity'] - $occu;
                
            }
        }
        
        // Tents
        if (is_array($tents) && !empty($tents) && $tents[0] !== "") {
            
            if ($gender) $sqlmore1 = "AND tent_gender = '$gender'";
            $sql = $db->query("SELECT tent_id, tent_capacity FROM tents WHERE tent_active = 1 AND type = 1 AND  tent_id IN (" . implode(',', $tents ?? ['']) . ") $sqlmore1");
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE tent_id = " . $row['tent_id'])->fetchColumn();
                $totals += $row['tent_capacity'] - $occu;
                
            }
            
        }
        
        // halls_arfa
        if (is_array($halls_arfa)) {
            
            if ($gender) $sqlmore1 = "AND tent_gender = '$gender'";
            
            $sql = $db->query("SELECT tent_id, tent_capacity FROM tents WHERE tent_active = 1  AND type = 2 AND tent_id IN (" . implode(',', $halls_arfa ?? ['']) . ") $sqlmore1");
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $occu = $db->query("SELECT COUNT(pil_code) FROM pils_accomo WHERE halls_id = " . $row['tent_id'])->fetchColumn();
                $totals += $row['tent_capacity'] - $occu;
                
            }
            
        }
        
        // Return the totals
        return $totals;
    }
    
    function AvailableToAccomoBuses($buses)
    {
        
        global $db;
        $totals = 0;
        
        // Buses
        if (is_array($buses)) {
            
            $sql = $db->query("SELECT bus_id, bus_seats FROM buses WHERE bus_active = 1 AND bus_id IN (" . implode(',', $buses) . ")");
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $occu = $db->query("SELECT COUNT(pil_id) FROM pils WHERE pil_active = 1 AND pil_bus_id = " . $row['bus_id'])->fetchColumn();
                $totals += $row['bus_seats'] - $occu;
                
            }
            
        }
        
        // Return the totals
        return $totals;
    }
