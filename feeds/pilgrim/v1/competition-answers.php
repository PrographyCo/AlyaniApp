<?php
    require '_includes/init.php';
    header('Content-Type: application/json');
    
    $lang = checkLang();
    $userinfo = checkToken();
    
    requiredInputs(['competition_id','questions']);
    
    if (isset($_POST['competition_id'])) {
        
        $competition_id = $_POST['competition_id'];
        $user_id = $userinfo['pil_id'];
        $questions = $_POST['questions'];
        
        $total = 0;
        foreach ($questions as $question) {
            
            $stmt = $db->prepare("SELECT * FROM competition_questions_choices WHERE id = ? AND correct = ?");
            $stmt->execute(array($question['answer'], 1));
            $correct = $stmt->fetch();
            if ($correct['id'] == $question['answer']) {
                $degree = 1;
            } else {
                $degree = 0;
            }
            $total = $total + $degree;
            $stmt = $db->prepare("INSERT INTO competition_answers (user_id,question_id,choice_id,result) VALUES (:user_id,:question_id,:choice_id,:result)");
            $stmt->execute(array(
                'user_id' => $user_id,
                'question_id' => $question['id'],
                'choice_id' => $question['answer'],
                'result' => $degree
            ));
            
            
        }
        $stmt = $db->prepare("SELECT * FROM competition_questions WHERE competition_id = ? ");
        $stmt->execute(array($competition_id));
        $total_q = $stmt->rowCount();
        
        $stmt = $db->prepare("INSERT INTO competition_result (user_id,competition_id,result,total) VALUES (:user_id,:competition_id,:result,:total)");
        $stmt->execute(array(
            'user_id' => $user_id,
            'competition_id' => $competition_id,
            'result' => $total,
            'total' => $total_q
        ));
        
        $items['success'] = true;
        $items['result'] = $total . ' / ' . $total_q;
        if ($lang == 'en') {
            $items['message'] = 'The test was successfully answered';
        } elseif ($lang == 'ar') {
            $items['message'] = 'تمت الاجابه على الاختبار بنجاح';
        } elseif ($lang == 'ur') {
            $items['message'] = 'ٹیسٹ کا کامیابی سے جواب دیا گیا۔';
        }
        
        outputJSON($items);
    }
?>
