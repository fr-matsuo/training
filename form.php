<?php
require_once('error_checker.php');
require_once('format_text.php');
require_once('select_box.php');
require_once('prefecture_data.php');

/*以下表示用*/
$HOBBYS = array('music' => '音楽鑑賞','movie' => '映画鑑賞','other' => 'その他');

$NAMES = array(
    'name_first', 'name_last', 'sex', 'post_first', 'post_last',
    'prefecture','mail_address', 'other_descript', 'opinion'
);

$formated_post = getFormatedTextArray($_POST);

$man_checked   = (getPOST('sex', $formated_post) == "男性")? "checked" : "";
$woman_checked = (getPOST('sex', $formated_post) == "女性")? "checked" : "";

$check_list = getPOSTArray('hobby', $formated_post);

//値のエラーをチェックし、エラー一覧を返す
function checkErrors($post_data) {
    //エラーチェックの引数リスト作成
    $name_check_functions = array(
        new Check_Function_Data('name_first', $post_data['name_first'], 'checkIsNoText', 0),
        new Check_Function_Data('name_last',  $post_data['name_last'] , 'checkIsNoText', 0),
        new Check_Function_Data('name_first', $post_data['name_first'], 'checkIsOverText', 1, 50),
        new Check_Function_Data('name_last',  $post_data['name_last'] , 'checkIsOverText', 1, 50)
    );
    $name_checker = new Error_Checker(
        '名前',
        $name_check_functions
    );

    $sex_value = isset($post_data['sex']) ? $post_data['sex'] : '';
    $sex_check_functions = array(
        new Check_Function_Data('sex', $sex_value, 'checkIsNoChoise', 0)
    );
    $sex_checker = new Error_Checker(
        '性別',
        $sex_check_functions
    );

    $post_check_functions = array(
        new Check_Function_Data('post_first', $post_data['post_first'], 'checkIsNoText', 0),
        new Check_Function_Data('post_last',  $post_data['post_last'],  'checkIsNoText', 1),
        new Check_Function_Data('post_first', $post_data['post_first'], 'checkIsIllegal', 2),
        new Check_Function_Data('post_last',  $post_data['post_last'],  'checkIsIllegal', 3)
    );
    $post_checker = new Error_Checker(
        '郵便番号',
        $post_check_functions
    );

    $prefecture_check_functions = array(
        new Check_Function_Data('prefecture', $post_data['prefecture'], 'checkIsEmptyValue', 0, '--')
    );
    $prefecture_checker = new Error_Checker(
        '都道府県',
        $prefecture_check_functions
    );

    $mail_address_check_functions = array(
        new Check_Function_Data('mail_address', $post_data['mail_address'], 'checkIsNoText',  0),
        new Check_Function_Data('mail_address', $post_data['mail_address'], 'checkIsIllegal', 1),
        new Check_Function_Data('mail_address', $post_data['mail_address'], 'checkIsOverLap', 2)
    );
    $mail_address_checker = new Error_Checker(
        'メールアドレス',
        $mail_address_check_functions
    );

    $hobby_check_functions = array();
    if (isset($post_data['hobby']) && in_array('その他', $post_data['hobby'])) {
        $other_hobby_value = isset($post_data['other_descript']) ? $post_data['other_descript'] : '';
        array_push(
            $hobby_check_functions,
            new Check_Function_Data('other_descript', $other_hobby_value, 'checkIsNoText', 0)
        );
    }
    $hobby_checker = new Error_Checker(
        '趣味',
        $hobby_check_functions
    );

    $checkers = array(
        $name_checker,
        $sex_checker,
        $post_checker,
        $prefecture_checker,
        $mail_address_checker,
        $hobby_checker
    );

    //エラー一覧を取得
    $errors = array();
    foreach ($checkers as $checker) {
        array_push($errors, $checker->getCheckResult());
    }

    return $errors;
}

//次のページに行けるならジャンプする関数。入力をform.phpに戻し、エラーがないならformCheck.phpへジャンプ
function checkJump($post_data) {
    if (empty($post_data) || isset($post_data['return'])) return;
    
    checkErrors($post_data);
    if (Error_Message::hasError() == false ) {
        print "onLoad='document.checkForm.submit();'";
    }      
}

//エラーがあればエラー一覧を表示
function showError($post_data) {
    if (empty($post_data) || isset($post_data['return'])) return;

    if (Error_Message::hasError()) {
        $error_texts = Error_Message::getAllErrorString();
        foreach ($error_texts as $error) {
            printf("%s<br>", $error);
        }
    } else {
        print "<input type='submit' value='dummy'>";
    }
}

//ポストデータがなければ空文字を返す、あればその文字列を返す
function getPOST($key, $post_data) {
    return (isset($post_data[$key])) ? $post_data[$key] : '';
}

//getPOSTの配列版
function getPOSTArray($key, $post_data) {
    $ret = array();
    if (!isset($post_data[$key]) || !is_array($post_data[$key])) return $ret;
    
    foreach ($post_data[$key] as $key => $value) {
        $ret += array($key => getFormatedText($value,0));
    }
    return $ret;
}

//ポストの値があれば表示、なければ空白を表示
function showPOST($key, $post_data) {
    print getPOST($key, $post_data);
}

function showPrefectures() {
    Prefecture_Data::constructSelectBox(getPOST('prefecture',$_POST));
}

function showHobbys($post_data, $hobby_list) {
    foreach ($hobby_list as $key => $elm) {
        $checked = '';

        if (empty($check_list) == false) {
            $checked = (in_array($elm, $check_list)) ? 'checked' : '';
        }
        printf("<input type='checkbox' id='%s' name='hobby[]' value='%s' %s><label for='%s'>%s</label>",
                      $key, $elm, $checked, $key, $elm);
    }
    printf("<input type='text' name='other_descript' id='other_descript value='%s'>", getPOST('other_descript', $post_data));
}

function writeHiddenParams($post_data, $name_list) {
    foreach ($name_list as $name) {
        $input = (isset($post_data[$name])) ? $post_data[$name] : '';
        printf("<input type='hidden' name='%s' value='%s'>", $name, $input);
    }
    if (isset($post_data['hobby'])) {
        foreach ($post_data['hobby'] as $hobby) {
            printf("<input type='hidden' name='hobby[]' value='%s'>", $hobby);
        }
    }

    //その他の趣味の詳細が入力されていたら、その他にチェックを付与
    if (!empty($post_data['other_descript']) && !in_array('その他', $post_data['hobby'])) {
        print "<input type='hidden' name='hobby[]' value='その他'>";
    }
}

//showPrefectures();
include('form.html.php');
