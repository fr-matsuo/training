<?php

//文字列を安全なものに変換したものを返す
function getSecureText($text) {
    return htmlspecialchars($text, ENT_QUOTES);
}

//最初の字が指定した文字群か
function isMBCharsPosFirst($text, $char_array) {
    foreach ($char_array as $char) {
        $pos = mb_strpos($text, $char);
        if ($pos === 0) return true;
    }
    return false;
}

//最期の字が指定した文字群か
function isMBCharsPosLast($text, $char_array) {
    foreach ($char_array as $char) {
        $pos = mb_strrpos($text, $char);
        if ($pos === mb_strlen($text) - 1) return true;
    }
    return false;
}

//文字配列orその配列の要素をトリムしたものを返す
function getTrimedText($text) {
    $space_list = array(' ', '　');

    while (isMBCharsPosFirst($text, $space_list)) {
        $text = mb_substr($text, 1);
    }
    while (isMBCharsPosLast($text, $space_list)) {
        $text = mb_substr($text, 0, mb_strlen($text)-1);
    }

    return $text;
}

//文字列をフォーマットしたものを返す
function getFormatedText($text, $index) {
    //これらの関数を上から再帰的に適用する
    $format_functions = array(
        'getTrimedText',
        'getSecureText'
    );

    if ($index == count($format_functions)) {
        return $text;
    } else {
        $next_text = $format_functions[$index]($text);
        $index++;
        return getFormatedText($next_text, $index);
    }
}

//入力値をフォーマットしたものを返す
function getFormatedTextArray($text_array) {
    $formated_array = array();

    foreach ($text_array as $key => $value) {
        if (is_array($value)) {
            $add = array();
            foreach ($value as $array_key => $array_value) {
                $add += array($array_key => getFormatedText($array_value, 0));
            }
            $formated_array += array($key => $add);
        } else {
            $formated_array += array($key => getFormatedText($value, 0));
        }
    }

    return $formated_array;
}

