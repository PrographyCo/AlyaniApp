<?php
    require '_includes/init.php';
    header('Content-Type: application/json');
    
    $lang = checkLang();
    $userinfo = checkToken();
    
    if (isset($_GET['get-competition'])) {
        
        $stmt = $db->prepare("SELECT * FROM competitions ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        
        
        if ($lang == 'ar') {
            $competition['name'] = $result['name_ar'];
            $competition['about'] = $result['about_ar'];
        } else if ($lang == 'en') {
            $competition['name'] = $result['name_en'];
            $competition['about'] = $result['about_en'];
        } else if ($lang == 'ur') {
            $competition['name'] = $result['name_ur'];
            $competition['about'] = $result['about_ur'];
        }
        
        $competition['id'] = $result['id'];
        $items['success'] = true;
        $items['data'] = $competition;
        outputJSON($items);
    }
    
    if (isset($_GET['competition_id'])) {
        
        $stmt = $db->prepare("SELECT * FROM competition_questions WHERE competition_id = ?");
        $stmt->execute(array($_GET['competition_id']));
        $qs = $stmt->fetchAll();
        $check = $stmt->rowCount();
        if ($check < 1) {
            $items['msg'] = " Not Found questions of competition";
            outputJSON($items);
        } else {
            
            $question = [];
            foreach ($qs as $q) {
                
                if ($lang == 'ar') {
                    $question['question'] = $q['question_ar'];
                } else if ($lang == 'en') {
                    $question['question'] = $q['question_en'];
                } else if ($lang == 'ur') {
                    $question['question'] = $q['question_ur'];
                }
                $question['id'] = $q['id'];
                
                $stmt = $db->prepare("SELECT * FROM  competition_questions_choices WHERE question_id = ?");
                $stmt->execute(array($q['id']));
                $answers = $stmt->fetchAll();
                $an = [];
                foreach ($answers as $ans) {
                    
                    if ($lang == 'ar') {
                        $answer['choice'] = $ans['choice_ar'];
                    } else if ($lang == 'en') {
                        $answer['choice'] = $ans['choice_en'];
                    } else if ($lang == 'ur') {
                        $answer['choice'] = $ans['choice_en'];
                    }
                    $answer['id'] = $ans['id'];
                    $an[] = $answer;
                }
                $question['choices'] = $an;
                $values[] = $question;
            }
            
            
            $items['success'] = true;
            $items['data'] = $values;
            outputJSON($items);
            
        }
        
        
    }
