<?php
    
    // Major Functions
    
    function getOurLocations($lang)
    {
        global $db;
        $sql = $db->query("SELECT loc_id, loc_title_$lang, loc_photo, loc_phones, loc_lat, loc_lng FROM ourlocations WHERE loc_active = 1 ORDER BY loc_order");
        return fetchOurLocations($sql, $lang);
    }
    
    function getOurCompanies($lang)
    {
        global $db;
        $sql = $db->query("SELECT comp_id, comp_title_$lang, comp_addr_$lang, comp_photo, comp_lat, comp_lng, comp_phone, comp_fb, comp_twitter FROM ourcompanies WHERE comp_active = 1 ORDER BY comp_order");
        return fetchOurCompanies($sql, $lang);
    }
    
    function getPromos($lang)
    {
        global $db;
        $sql = $db->query("SELECT promo_id, promo_title_$lang, promo_body_$lang, promo_photo FROM promos WHERE promo_active = 1 ORDER BY promo_order");
        return fetchPromos($sql, $lang);
    }
    
    function getHajAlbum($lang)
    {
        global $db;
        $sql = $db->query("SELECT hp_id, hp_title_$lang, hp_photo FROM haj_album WHERE hp_active = 1 ORDER BY hp_order");
        return fetchHajAlbum($sql, $lang);
    }
    
    function getAboutUs($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM aboutus WHERE au_id = 1");
        return fetchAboutUs($sql, $lang);
    }
    
    function getFAQs($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM faqs WHERE faq_active = 1 ORDER BY faq_order");
        return fetchFAQ($sql, $lang);
    }
    
    function getRatingQuestions($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM questions WHERE q_active = 1 ORDER BY q_order");
        return fetchRatingQuestion($sql, $lang);
    }
    
    function getRatingQuestionsLink()
    {
        global $db;
        $link = $db->query("SELECT s_value FROM settings WHERE s_id = 1")->fetchColumn();
        return $link;
    }
    
    function getNews($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM news WHERE news_active = 1 ORDER BY news_order");
        return fetchNews($sql, $lang);
    }
    
    function getHajGuide($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM guide_categories WHERE gcat_active = 1 ORDER BY gcat_order");
        return fetchHajGuide($sql, $lang);
    }
    
    function getContactInfo($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM contactinfo ORDER BY ci_type, ci_order");
        return fetchContactInfo($sql, $lang);
    }
    
    function getSocialInfo($lang)
    {
        global $db;
        $sql = $db->query("SELECT * FROM socials");
        return fetchSocialInfo($sql, $lang);
    }
    
    function getPilInfoExtended($pilinfo, $lang)
    {
        global $db;
        
        if ($pilinfo) return fetchPilInfo($pilinfo['pil_id'], $lang);
        else return false;
        
    }
    
    function getCityStaff($city_id)
    {
        
        global $db;
        if (is_numeric($city_id) && $city_id > 0) {
            
            $sql = $db->query("SELECT staff_id FROM cities_staff WHERE city_id = $city_id");
            return fetchStaff($sql);
            
        } else return false;
        
    }
    
    function getNotifications($pilinfo, $lang)
    {
        
        global $db;
        
        $lang = 'ur' ? 'ar' : $lang;
        
        if (isset($pilinfo['pil_id']) && $pilinfo['pil_id'] > 0) {
            
            // this is a pilgrim
            $sql = $db->query("SELECT noti_id, noti_body_$lang, noti_dateadded FROM notifications WHERE noti_user_type = 1 AND noti_user_id = " . $pilinfo['pil_id'] . " ORDER BY noti_dateadded DESC");
            return fetchNotifications($sql, $lang);
            
        } elseif (isset($pilinfo['staff_id']) && $pilinfo['staff_id'] > 0) {
            
            // this is a staff
            $sql = $db->query("SELECT noti_id, noti_body_$lang, noti_dateadded FROM notifications WHERE noti_user_type = 2 AND noti_user_id = " . $pilinfo['staff_id'] . " ORDER BY noti_dateadded DESC");
            return fetchNotifications($sql, $lang);
            
        } else {
            
            // not logged in
            $sql = $db->query("SELECT noti_id, noti_body_$lang, noti_dateadded FROM notifications WHERE noti_user_type = 0 AND noti_user_id = 0 ORDER BY noti_dateadded DESC");
            return fetchNotifications($sql, $lang);
            
            
        }
        
        return false;
        
    }
    
    function getStaffCitiesIDs($staff_id)
    {
        
        global $db;
        $return = array(0);
        $sql = $db->query("SELECT city_id FROM cities_staff WHERE staff_id = $staff_id");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = $row['city_id'];
            
        }
        
        return $return;
        
    }
    
    function getStaffBusesIDs($staff_id)
    {
        
        global $db;
        $return = array(0);
        $sql = $db->query("SELECT bus_id FROM cities_staff WHERE staff_id = $staff_id");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = $row['city_id'];
            
        }
        
        return $return;
        
    }
    
    function getStaffPilgrims($staff_id = 0, $search, $cities, $pilinfo, $lang, $from, $maxresults)
    {
        
        global $db;
        $return = array();
        
        if ($search) $sqlmore = " AND (pil_name LIKE :search OR pil_code LIKE :search OR pil_phone LIKE :search OR pil_nationalid LIKE :search)";
        
        if (is_array($cities) && $cities > 0) {
            
            $sqlmore2 = " AND pil_city_id IN (" . implode(',', $cities) . ")";
            
        }
        
        if ($maxresults > 0) $sqlmore3 = "LIMIT :from, :maxresults";
        
        if ($pilinfo['staff_type'] == 1) {
            
            if ($staff_id) $sqlmore4 = " AND pil_bus_id IN (SELECT bus_id FROM buses WHERE bus_staff_id = $staff_id)";
            
            // Manager
            $sql = $db->prepare("SELECT pil_id FROM pils WHERE pil_active = 1 $sqlmore $sqlmore2 $sqlmore4 $sqlmore3");
            if ($maxresults > 0) {
                
                $sql->bindValue("from", (int)$from, PDO::PARAM_INT);
                $sql->bindValue("maxresults", (int)$maxresults, PDO::PARAM_INT);
                
            }
            if ($search) $sql->bindValue("search", "%" . $search . "%");
            $sql->execute();
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $return[] = getPilInfoExtended($row, $lang);
                
            }
            
        } elseif ($pilinfo['staff_type'] == 2) {
            
            if (is_array($cities) && $cities > 0) {
                
                $sqlmore2 = " AND pil_city_id IN (" . implode(',', $cities) . ")";
                
            }
            
            if (!$staff_id) $staff_id = $pilinfo['staff_id'];
            
            // Supervisor
            $sql = $db->prepare("SELECT pil_id FROM pils WHERE pil_bus_id IN (SELECT bus_id FROM buses WHERE bus_staff_id = $staff_id) $sqlmore $sqlmore2 $sqlmore3");
            if ($maxresults > 0) {
                
                $sql->bindValue("from", (int)$from, PDO::PARAM_INT);
                $sql->bindValue("maxresults", (int)$maxresults, PDO::PARAM_INT);
                
            }
            if ($search) $sql->bindValue("search", "%" . $search . "%");
            $sql->execute();
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $return[] = getPilInfoExtended($row, $lang);
                
            }
            
        }
        
        return $return;
        
    }
    
    function getMuftis()
    {
        
        global $db;
        $return = array();
        $sql = $db->query("SELECT staff_id FROM staff WHERE staff_type = 3 AND staff_active = 1 ORDER BY staff_name");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = getStaffInfo($row['staff_id']);
            
        }
        
        return $return;
        
    }
    
    function getClassMuftis($pilinfo, $lang)
    {
        
        global $db;
        $return = array();
        $sql = $db->query("SELECT staff_id FROM staff WHERE staff_type = 3 AND staff_active = 1 AND staff_id IN (SELECT staff_id FROM pils_classes_muftis WHERE pilc_id = " . $pilinfo['pil_pilc_id'] . ") ORDER BY staff_name");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = getStaffInfo($row['staff_id']);
            
        }
        
        return $return;
        
    }
    
    function getClassSupervisors($pilinfo, $lang)
    {
        
        global $db;
        $return = array();
        
        // City 1 (Mena) Staff idea
        $city1_staff_id = $db->query("SELECT pilc_city1_staff_id FROM pils_classes WHERE pilc_id = " . $pilinfo['pil_pilc_id'])->fetchColumn();
        $return['mena'] = getStaffInfo($city1_staff_id);
        
        // City 2 (Mozdalefa) Staff idea
        $city2_staff_id = $db->query("SELECT pilc_city2_staff_id FROM pils_classes WHERE pilc_id = " . $pilinfo['pil_pilc_id'])->fetchColumn();
        $return['mozdalefa'] = getStaffInfo($city2_staff_id);
        
        // City 3 (Arafa) Staff idea
        $city3_staff_id = $db->query("SELECT pilc_city3_staff_id FROM pils_classes WHERE pilc_id = " . $pilinfo['pil_pilc_id'])->fetchColumn();
        $return['arafa'] = getStaffInfo($city3_staff_id);
        
        return $return;
        
    }
    
    
    function getCities($lang)
    {
        
        global $db;
        $return = array();
        $sql = $db->query("SELECT * FROM cities WHERE city_active = 1 ORDER BY city_order");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = fetchCityInfo($row, $lang);
            
        }
        
        return $return;
        
    }
    
    function getGeneralGuide($lang)
    {
        
        global $db;
        $return = array();
        $sql = $db->query("SELECT * FROM general_guide WHERE gg_active = 1 ORDER BY gg_order");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = fetchGeneralGuideInfo($row, $lang);
            
        }
        
        return $return;
        
    }
    
    function getPilAccomo($pil_code, $lang)
    {
        
        global $db;
        $return = array();
        
        $accomo_enabled = $db->query("SELECT s_value FROM settings WHERE s_id = 10")->fetchColumn();
        if (!$accomo_enabled) return $return;
        
        $accomoinfo = $db->query("SELECT * FROM pils_accomo WHERE pil_code = '$pil_code'")->fetch(PDO::FETCH_ASSOC);
        if ($accomoinfo) {
            
            
            if ($accomoinfo['suite_id'] != 0) {
                
                // Suites
                $suite_title = $db->query("SELECT suite_title FROM suites WHERE suite_id = " . $accomoinfo['suite_id'])->fetchColumn();
                $hall_title = $db->query("SELECT hall_title FROM suites_halls WHERE hall_id = " . $accomoinfo['hall_id'])->fetchColumn();
                if (!empty($suite_title)) {
                    $accomo['title'] = HM_Suite;
                    $accomo['value'] = $suite_title;
                    $accomo['type'] = "3";
                    $return[] = $accomo;
                }
                
                
                if (!empty($hall_title)) {
                    
                    
                    $accomo['title'] = HM_Hall;
                    $accomo['value'] = $hall_title;
                    $accomo['type'] = "3";
                    $return[] = $accomo;
                    
                }
                
                
                if ($accomoinfo['extratype_id'] == 1) {
                    
                    if ($lang == 'ar') $accomo['title'] = LBL_Chair1;
                    elseif ($lang == 'en') $accomo['title'] = LBL_Chair1;
                    elseif ($lang == 'ur') $accomo['title'] = 'نشست';
                    
                    $accomo['value'] = $accomoinfo['extratype_text'];
                    $accomo['type'] = "3";
                    $return[] = $accomo;
                    
                } elseif ($accomoinfo['extratype_id'] == 2) {
                    
                    if ($lang == 'ar') $accomo['title'] = LBL_Chair2;
                    elseif ($lang == 'en') $accomo['title'] = LBL_Chair2;
                    elseif ($lang == 'ur') $accomo['title'] = 'آرام کرسی';
                    
                    $accomo['value'] = $accomoinfo['extratype_text'];
                    $accomo['type'] = "3";
                    $return[] = $accomo;
                    
                } elseif ($accomoinfo['extratype_id'] == 3) {
                    
                    if ($lang == 'ar') $accomo['title'] = LBL_Bed;
                    elseif ($lang == 'en') $accomo['title'] = LBL_Bed;
                    elseif ($lang == 'ur') $accomo['title'] = 'ایک بیڈ';
                    
                    $accomo['value'] = $accomoinfo['extratype_text'];
                    $accomo['type'] = "3";
                    $return[] = $accomo;
                    
                }
                
                
            }
            if ($accomoinfo['bld_id'] != 0) {
                
                // building
                $bld_title = $db->query("SELECT bld_title FROM buildings WHERE bld_id = " . $accomoinfo['bld_id'])->fetchColumn();
                $floor_title = $db->query("SELECT floor_title FROM buildings_floors WHERE floor_id = " . $accomoinfo['floor_id'])->fetchColumn();
                $room_title = $db->query("SELECT room_title FROM buildings_rooms WHERE room_id = " . $accomoinfo['room_id'])->fetchColumn();
                
                
                if ($accomoinfo['pil_accomo_type'] == 2) {
                    
                    if ($lang == 'ar') $accomo['title'] = HM_Building;
                    elseif ($lang == 'en') $accomo['title'] = HM_Building;
                    elseif ($lang == 'ur') $accomo['title'] = 'اندیکاٹنگ';
                    
                } elseif ($accomoinfo['pil_accomo_type'] == 5) {
                    
                    if ($lang == 'ar') $accomo['title'] = LBL_Premises;
                    elseif ($lang == 'en') $accomo['title'] = LBL_Premises;
                    elseif ($lang == 'ur') $accomo['title'] = 'اماریہ';
                    
                } else {
                    if ($lang == 'ar') $accomo['title'] = 'مبنى';
                    elseif ($lang == 'en') $accomo['title'] = 'Bulding';
                    elseif ($lang == 'ur') $accomo['title'] = 'عمارت';
                }
                
                $accomo['value'] = $bld_title;
                $accomo['type'] = "3";
                $return[] = $accomo;
                
                if ($lang == 'ar') $accomo['title'] = HM_Floor;
                elseif ($lang == 'en') $accomo['title'] = HM_Floor;
                elseif ($lang == 'ur') $accomo['title'] = 'کا کردار';
                
                $accomo['value'] = $floor_title;
                $return[] = $accomo;
                
                if ($lang == 'ar') $accomo['title'] = HM_Room;
                elseif ($lang == 'en') $accomo['title'] = HM_Room;
                elseif ($lang == 'ur') $accomo['title'] = 'کمرہ';
                $accomo['value'] = $room_title;
                $accomo['type'] = "3";
                $return[] = $accomo;
                
            }
            if ($accomoinfo['tent_id'] != 0) {
                
                // tent
                $tent_title = $db->query("SELECT tent_title , type , tent_type  FROM tents WHERE tent_id = " . $accomoinfo['tent_id'])->fetch();
                $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = " . $accomoinfo['tent_id'])->fetchColumn();
                if ($tent_title['type'] == 1) {
                    
                    if ($tent_title['tent_type'] == 1) {
                        
                        if ($lang == 'ar') $accomo['title'] = LBL_TentType1;
                        elseif ($lang == 'en') $accomo['title'] = LBL_TentType1;
                        elseif ($lang == 'ur') $accomo['title'] = 'شکل';
                        
                    } elseif ($tent_title['tent_type'] == 2) {
                        
                        if ($lang == 'ar') $accomo['title'] = LBL_TentType2;
                        elseif ($lang == 'en') $accomo['title'] = LBL_TentType2;
                        elseif ($lang == 'ur') $accomo['title'] = 'شکل';
                        
                    }
                    
                    
                    $accomo['value'] = $tent_title['tent_title'];
                    $accomo['type'] = "1";
                    $return[] = $accomo;
                    
                    
                }
                
                
            }
            
            // ---------
            if ($accomoinfo['halls_id'] != 0) {
                
                // tent
                $tent_title = $db->query("SELECT tent_title , type , tent_type FROM tents WHERE tent_id = " . $accomoinfo['halls_id'])->fetch();
                $tent_type = $db->query("SELECT tent_type FROM tents WHERE tent_id = " . $accomoinfo['halls_id'])->fetchColumn();
                if ($tent_title['type'] == 2) {
                    
                    if ($tent_title['tent_type'] == 1) {
                        
                        if ($lang == 'ar') $accomo['title'] = LBL_TentType1;
                        elseif ($lang == 'en') $accomo['title'] = LBL_TentType1;
                        elseif ($lang == 'ur') $accomo['title'] = 'شکل';
                        
                    } elseif ($tent_title['tent_type'] == 2) {
                        
                        if ($lang == 'ar') $accomo['title'] = LBL_TentType2 . ' ' . arafa;
                        elseif ($lang == 'en') $accomo['title'] = LBL_TentType2 . ' ' . arafa;
                        elseif ($lang == 'ur') $accomo['title'] = 'شکل';
                        
                    }
                    
                }
                
                
                $accomo['value'] = $tent_title['tent_title'];
                $accomo['type'] = "2";
                $accomo['seat'] = $accomoinfo['seats'] == 0 ? without_seat : with_seat;
                $return[] = $accomo;
                
                
                if ($lang == 'ar') $accomo['title'] = seat;
                elseif ($lang == 'en') $accomo['title'] = seat;
                elseif ($lang == 'ur') $accomo['title'] = 'جگہ، مقام';
                
                // $accomo['value'] = $accomoinfo['seats'] == 0 ? without_seat : with_seat;
                
                
                $seattitle = 0;
                
                
                if ($accomoinfo['seats'] != 0) {
                    
                    
                    $stmt = $db->prepare("SELECT * FROM pils_accomo WHERE  halls_id = ? AND seats = ?");
                    $stmt->execute(array($accomoinfo['halls_id'], 1));
                    $seats = $stmt->fetchAll();
                    $check = $stmt->rowCount();
                    if ($check > 0) {
                        foreach ($seats as $seat) {
                            $seattitle++;
                            if ($seat['pil_code'] == $accomoinfo['pil_code']) {
                                
                                break;
                            }
                        }
                    }
                } else {
                    $seattitle = without_seat;
                }
                
                
                $accomo['value'] = "$seattitle";
                $return[] = $accomo;
                
            }
            
            
            if ($accomoinfo['bus_id'] != 0) {
                
                // bus
                $bus_title = $db->query("SELECT bus_title FROM buses WHERE bus_id = " . $accomoinfo['bus_id'])->fetchColumn();
                
                $accomo['title'] = HM_Bus;
                $accomo['value'] = $bus_title;
                $accomo['type'] = 4;
                $return[] = $accomo;
                
            }
            
        }
        
        return $return;
        
    }
    
    function getFeedback($lang)
    {
        
        global $db;
        $return = array();
        $sql = $db->query("SELECT * FROM feedback
    ORDER BY feedb_dateadded DESC");
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = fetchFeedback($row, $lang);
            
        }
        
        return $return;
        
    }
    
    function getAllSupervisors($search, $cities, $lang, $from, $maxresults)
    {
        
        global $db;
        $return = array();
        
        if ($search) $sqlmore = " AND (staff_name LIKE :search OR staff_email LIKE :search OR staff_phones LIKE :search)";
        if (is_array($cities) && $cities > 0) {
            
            $sqlmore2 = "AND (staff_id IN (SELECT staff_id FROM cities_staff WHERE city_id IN (" . implode(',', $cities) . ")) OR staff_id IN (SELECT bus_staff_id FROM buses WHERE bus_city_id IN (" . implode(',', $cities) . ")))";
            
        }
        
        if ($maxresults > 0) $sqlmore3 = "LIMIT :from, :maxresults";
        
        // Supervisors
        $sql = $db->prepare("SELECT staff_id FROM staff WHERE staff_type = 2 AND staff_active = 1 AND staff_priv = 2 $sqlmore $sqlmore2 $sqlmore3");
        if ($maxresults > 0) {
            $sql->bindValue("from", (int)$from, PDO::PARAM_INT);
            $sql->bindValue("maxresults", (int)$maxresults, PDO::PARAM_INT);
        }
        if ($search) $sql->bindValue("search", "%" . $search . "%");
        $sql->execute();
        
        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return[] = getStaffInfoWithCity($row['staff_id'], $lang);
            
        }
        
        return $return;
    }
    
    function getNewNotificationsCount($pilinfo, $lang, $device_uuid, $platform)
    {
        global $db;
        if (isset($pilinfo['pil_id']) && $pilinfo['pil_id'] > 0) {
            
            // this is a pilgrim
            $lastnoti_id = $db->query("SELECT noti_id FROM notifications_read WHERE noti_user_type = 1 AND noti_user_id = " . $pilinfo['pil_id'] . "")->fetchColumn() ?: 0;
            $count = $db->query("SELECT COUNT(noti_id) FROM notifications WHERE noti_user_type = 1 AND noti_user_id = " . $pilinfo['pil_id'] . " AND noti_id > $lastnoti_id")->fetchColumn();
            return (int)$count;
            
        } elseif (isset($pilinfo['staff_id']) && $pilinfo['staff_id'] > 0) {
            
            // this is a staff
            $lastnoti_id = $db->query("SELECT noti_id FROM notifications_read WHERE noti_user_type = 2 AND noti_user_id = " . $pilinfo['staff_id'] . "")->fetchColumn() ?: 0;
            $count = $db->query("SELECT COUNT(noti_id) FROM notifications WHERE noti_user_type = 2 AND noti_user_id = " . $pilinfo['staff_id'] . " AND noti_id > $lastnoti_id")->fetchColumn();
            return (int)$count;
            
        } else {
            
            // this is not logged in
            $lastnoti_id = $db->query("SELECT noti_id FROM notifications_read_guest WHERE device_uuid = '$device_uuid' AND platform = '" . $platform . "'")->fetchColumn() ?: 0;
            $count = $db->query("SELECT COUNT(noti_id) FROM notifications WHERE noti_user_type = 0 AND noti_user_id = 0 AND noti_id > $lastnoti_id")->fetchColumn();
            return (int)$count;
            
        }
        
        return 0;
    }
