<?php
    // Fetching Functions
    function fetchOurLocations($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['loc_id'] = $row['loc_id'];
                $data['loc_title'] = $row['loc_title_' . $lang];
                $data['loc_photo'] = OURLOCATIONS_PHOTO_URL . '/' . $row['loc_photo'];
                $data['loc_lat'] = $row['loc_lat'];
                $data['loc_lng'] = $row['loc_lng'];
                $data['loc_phones'] = explode(",", $row['loc_phones']);
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchOurCompanies($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['comp_id'] = $row['comp_id'];
                $data['comp_title'] = $row['comp_title_' . $lang];
                $data['comp_address'] = $row['comp_addr_' . $lang];
                $data['comp_photo'] = OURCOMPANIES_PHOTO_URL . '/' . $row['comp_photo'];
                $data['comp_lat'] = $row['comp_lat'];
                $data['comp_lng'] = $row['comp_lng'];
                $data['comp_phone'] = $row['comp_phone'];
                $data['comp_fb'] = $row['comp_fb'];
                $data['comp_twitter'] = $row['comp_twitter'];
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchPromos($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['promo_id'] = $row['promo_id'];
                $data['promo_title'] = $row['promo_title_' . $lang];
                $data['promo_body'] = $row['promo_body_' . $lang];
                $data['promo_photo'] = PROMOS_PHOTO_URL . '/' . $row['promo_photo'];
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchHajAlbum($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['hp_id'] = $row['hp_id'];
                $data['hp_title'] = $row['hp_title_' . $lang];
                $data['hp_photo'] = HAJALBUM_PHOTO_URL . '/' . $row['hp_photo'];
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchAboutUs($sql, $lang)
    {
        
        $data = array();
        
        $auinfo = $sql->fetch(PDO::FETCH_ASSOC);
        
        $data['au_type'] = $auinfo['au_type'];
        $data['au_body'] = '';
        $data['au_videourl'] = '';
        $data['au_youtube_id'] = '';
        
        if ($auinfo['au_type'] == 1) $data['au_body'] = $auinfo['au_body_' . $lang];
        elseif ($auinfo['au_type'] == 2) $data['au_videourl'] = ABOUTUSVID_PHOTO_URL . '/' . $auinfo['au_videofile'];
        elseif ($auinfo['au_type'] == 3) $data['au_youtube_id'] = $auinfo['au_youtube_id'];
        
        return $data;
    }
    
    function fetchFAQ($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['faq_id'] = $row['faq_id'];
                $data['faq_title'] = $row['faq_title_' . $lang];
                $data['faq_answer'] = $row['faq_answer_' . $lang];
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchRatingQuestion($sql, $lang)
    {
        
        global $db;
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['q_id'] = $row['q_id'];
                $data['q_title'] = $row['q_title_' . $lang];
                
                $answers = array();
                
                $sqlanswers = $db->query("SELECT * FROM questions_answers WHERE qa_q_id = " . $row['q_id']);
                while ($rowa = $sqlanswers->fetch(PDO::FETCH_ASSOC)) {
                    
                    $answers['qa_id'] = $rowa['qa_id'];
                    $answers['qa_answer'] = $rowa['qa_answer_' . $lang];
                    
                    $data['q_answers'] = $answers;
                }
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchNews($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['news_id'] = $row['news_id'];
                $data['news_body'] = $row['news_body_' . $lang];
                $data['news_photo'] = NEWS_PHOTO_URL . '/' . $row['news_photo'];
                $data['news_lastupdated'] = $row['news_lastupdated'];
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
    }
    
    function fetchHajGuide($sql, $lang)
    {
        
        global $db;
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['gcat_id'] = $row['gcat_id'];
                $data['gcat_title'] = $row['gcat_title_' . $lang];
                $data['gcat_articles'] = array();
                
                // bring articles
                $sqla = $db->query("SELECT * FROM guide_articles WHERE ga_gcat_id = " . $row['gcat_id'] . " AND ga_active = 1 ORDER BY ga_order");
                while ($rowa = $sqla->fetch(PDO::FETCH_ASSOC)) {
                    
                    $article = array();
                    
                    $article['ga_id'] = $rowa['ga_id'];
                    $article['ga_title'] = $rowa['ga_title_' . $lang];
                    $article['ga_type'] = $rowa['ga_type'];
                    $article['ga_body'] = '';
                    $article['ga_file'] = '';
                    if ($rowa['ga_type'] == 1) $article['ga_body'] = $rowa['ga_body_' . $lang];
                    elseif ($rowa['ga_type'] == 2) $article['ga_file'] = GA_PHOTO_URL . '/' . $rowa['ga_file_' . $lang];
                    
                    $data['gcat_articles'][] = $article;
                    
                }
                
                $return[] = $data;
                
            }
            
        } else return false;
        
        return $return;
        
        
    }
    
    function fetchContactInfo($sql, $lang)
    {
        
        $return = array();
        
        $phones = array();
        $emails = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                if ($row['ci_type'] == 1) $phones[] = $row['ci_value'];
                elseif ($row['ci_type'] == 2) $emails[] = $row['ci_value'];
                
            }
            
        } else return false;
        
        $return['phones'] = $phones;
        $return['emails'] = $emails;
        
        return $return;
        
    }
    
    function fetchSocialInfo($sql, $lang)
    {
        
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) $return[$row['ws_title']] = $row['ws_url'];
            
        } else return false;
        
        return $return;
        
    }
    
    function fetchPilInfo($pil_id, $lang)
    {
        
        global $db;
        $return = array();
        
        if (is_numeric($pil_id) && $pil_id > 0) {
            
            if ($lang == 'ur') $country_lang = 'ar';
            else $country_lang = $lang;
            
            $pilinfo = $db->query("SELECT p.*, c.country_title_$country_lang, ci.city_title_$lang, cl.pilc_title_$lang, b.bus_staff_id, b.bus_title FROM pils p
      LEFT OUTER JOIN countries c ON p.pil_country_id = c.country_id
      LEFT OUTER JOIN cities ci ON p.pil_city_id = ci.city_id
      LEFT OUTER JOIN pils_classes cl ON p.pil_pilc_id = cl.pilc_id
      LEFT OUTER JOIN buses b ON p.pil_bus_id = b.bus_id
      WHERE p.pil_id = $pil_id")->fetch(PDO::FETCH_ASSOC);
            
            $return['pil_id'] = $pilinfo['pil_id'];
            $return['pil_name'] = $pilinfo['pil_name'];
            $return['pil_class'] = $pilinfo['pilc_title_' . $lang];
            $return['pil_country'] = $pilinfo['country_title_' . $lang];
            $return['pil_phone'] = $pilinfo['pil_phone'];
            $return['pil_nationalid'] = $pilinfo['pil_nationalid'];
            $return['pil_reservation_number'] = $pilinfo['pil_reservation_number'];
            $return['pil_code'] = $pilinfo['pil_code'];
            $return['pil_city'] = $pilinfo['city_title_' . $lang];
            $return['pil_photo'] = PIL_PHOTO_URL . '/' . $pilinfo['pil_photo'];
            $return['pil_gender'] = $pilinfo['pil_gender'];
            $return['pil_qrcode'] = PILQR_PHOTO_URL . '/' . $pilinfo['pil_qrcode'];
            $return['pil_card'] = CP_URL . '/pilcard.php?id=' . $pilinfo['pil_id'] . '';
            $return['pil_bus'] = $pilinfo['bus_title'];
            $return['pil_bus_supervisor'] = getStaffInfo($pilinfo['bus_staff_id']);
            $return['city_supervisors'] = getCityStaff($pilinfo['pil_city_id']);
            $return['accomodation'] = getPilAccomo($pilinfo['pil_code'], $lang);
            
        } else return false;
        
        return $return;
        
    }
    
    function fetchStaff($sql)
    {
        
        global $db;
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $return[] = getStaffInfo($row['staff_id']);
                
            }
            
        }
        
        return $return;
    }
    
    function fetchNotifications($sql, $lang)
    {
        
        global $db;
        $return = array();
        
        if ($sql) {
            
            while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                
                $data['noti_id'] = $row['noti_id'];
                $data['noti_body'] = $row['noti_body_' . $lang];
                $data['noti_dateadded'] = $row['noti_dateadded'];
                
                $return[] = $data;
                
            }
            
        }
        
        return $return;
    }
    
    function fetchCityInfo($row, $lang)
    {
        
        global $db;
        
        $return['city_id'] = $row['city_id'];
        $return['city_title'] = $row['city_title_' . $lang];
        $return['city_supervisors'] = array();
        // get city supervisors
        $sql = $db->query("SELECT staff_id FROM cities_staff WHERE city_id = " . $row['city_id']);
        while ($row2 = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return['city_supervisors'][] = getStaffInfo($row2['staff_id']);
            
        }
        
        return $return;
        
    }
    
    function fetchGeneralGuideInfo($row, $lang)
    {
        
        global $db;
        
        $return['gg_id'] = $row['gg_id'];
        $return['gg_title'] = $row['gg_title_' . $lang];
        $return['gg_descr'] = $row['gg_descr_' . $lang];
        $return['gg_photo'] = GG_PHOTO_URL . '/' . $row['gg_photo'];
        $return['gg_supervisors'] = array();
        // get city supervisors
        $sql = $db->query("SELECT ggs_id FROM gg_staff WHERE gg_id = " . $row['gg_id']);
        while ($row2 = $sql->fetch(PDO::FETCH_ASSOC)) {
            
            $return['gg_supervisors'][] = getGGStaffInfo($row2['ggs_id'], $lang);
            
        }
        
        return $return;
        
    }
    
    function fetchFeedback($row, $lang)
    {
        
        $return['feedback_id'] = $row['feedb_id'];
        $return['feedback_type'] = $row['feedb_type'];
        
        if ($lang == 'ar') {
            
            if ($row['feedb_type'] == 1) $return['feedback_type_description'] = 'استفسار';
            elseif ($row['feedb_type'] == 2) $return['feedback_type_description'] = 'شكوى';
            elseif ($row['feedb_type'] == 3) $return['feedback_type_description'] = 'مقترح';
            
        } else {
            
            if ($row['feedb_type'] == 1) $return['feedback_type_description'] = 'Enquiry';
            elseif ($row['feedb_type'] == 2) $return['feedback_type_description'] = 'Complain';
            elseif ($row['feedb_type'] == 3) $return['feedback_type_description'] = 'Suggestion';
            
        }
        
        $return['feedback_name'] = $row['feedb_name'];
        $return['feedback_phone'] = $row['feedb_phone'];
        $return['feedback_email'] = $row['feedb_email'];
        $return['feedback_message'] = $row['feedb_message'];
        $return['feedback_date'] = $row['feedb_dateadded'];
        
        return $return;
    }
