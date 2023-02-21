<?php
    require '_includes/init.php';
    newlog($_SERVER['REQUEST_URI'], $_GET, $_POST, $_FILES);
    $pilinfo = checkIfToken();
    $lang = checkLang();
    
    $data = $_POST;
    
    try {
        
        $items['success'] = true;
        $items['message'] = '';
        
        $items['data']['force_photo_change_male'] = (boolean)$db->query("SELECT s_value FROM settings WHERE s_id = 4")->fetchColumn();
        $items['data']['force_photo_change_female'] = (boolean)$db->query("SELECT s_value FROM settings WHERE s_id = 5")->fetchColumn();
        if ($lang === 'ar') $items['data']['offline_message'] = $db->query("SELECT s_value FROM settings WHERE s_id = 7")->fetchColumn();
        if ($lang === 'en') $items['data']['offline_message'] = $db->query("SELECT s_value FROM settings WHERE s_id = 8")->fetchColumn();
        if ($lang === 'ur') $items['data']['offline_message'] = $db->query("SELECT s_value FROM settings WHERE s_id = 9")->fetchColumn();
        
        $frontimg = $db->query("SELECT s_value FROM settings WHERE s_id = 13")->fetchColumn();
        if ($frontimg) $items['data']['frontimg'] = FRONTIMG_URL . '/' . $frontimg;
        else $items['data']['frontimg'] = FRONTIMG_URL . '/default.png';
        
        // Our locations
        $items['data']['ourlocations'] = getOurLocations($lang);
        
        // Our companies
        $items['data']['ourcompanies'] = getOurCompanies($lang);
        
        // Cities
        $items['data']['cities'] = getCities($lang);
        
        // Our promotions
        $items['data']['promos'] = getPromos($lang);
        
        // Haj Album
        $items['data']['hajalbum'] = getHajAlbum($lang);
        
        // About us
        $items['data']['aboutus'] = getAboutUs($lang);
        
        // FAQs
        $items['data']['faqs'] = getFAQs($lang);
        
        // Rating Questions
        //$items['data']['ratingquestions'] = getRatingQuestions($lang);
        
        // Rating Questions Link
        $items['data']['ratingquestionslink'] = getRatingQuestionsLink();
        
        // News
        $items['data']['news'] = getNews($lang);
        
        // Haj Guide
        $items['data']['hajguide'] = getHajGuide($lang);
        
        // Contact Info
        $items['data']['contactinfo'] = getContactInfo($lang);
        
        // Social Info
        $items['data']['socialinfo'] = getSocialInfo($lang);
        
        // General Guide
        $items['data']['general_guide'] = getGeneralGuide($lang);
        
        if ($pilinfo['pil_id'] && $pilinfo['pil_id'] > 0) {
            
            // Pilgrim Info
            $items['data']['pilgriminfo'] = getPilInfoExtended($pilinfo, $lang);
            
            // Muftis
            $items['data']['muftis'] = getClassMuftis($pilinfo, $lang);
            
            // Locations Supervisors
            $items['data']['locations_supervisors'] = getClassSupervisors($pilinfo, $lang);
            
        } elseif ($pilinfo['staff_id'] && $pilinfo['staff_id'] > 0) {
            
            // Staff Info
            $items['data']['staffinfo'] = getStaffInfo($pilinfo['staff_id']);
            
            // Staff Pilgrims
            $items['data']['pilgrims'] = 'listPils.php API';
            
            // check if manager, then show complains, inquiries
            if ($pilinfo['staff_type'] == 1) {
                
                // Manager
                $items['data']['feedback'] = getFeedback($lang);
                
            }
            
        }
        
        
        // Notifications
        $items['data']['notifications'] = getNotifications($pilinfo, $lang);
        
        // New Notifications Count
        $items['data']['new_notifications_count'] = getNewNotificationsCount($pilinfo, $lang, $data['device_uuid'], $data['platform']);
        
    } catch (PDOException $e) {
        
        headerBadRequest();
        $items['success'] = false;
        $items['message'] = SQLError($e);
    }
    
    outputJSON($items);
