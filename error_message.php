<?php

class Error_Message
{
    //エラー項目の名前
    private static $_name_list = array(
         'name_first'     => '姓',
         'name_last'      => '名',
         'sex'            => '性別',
         'post_first'     => '郵便番号',
         'post_last'      => '郵便番号',
         'prefecture'     => '都道府県',
         'mail_address'   => 'メールアドレス',
         'other_descript' => 'その他の詳細'
    );
    //エラーの内容
    private static $_kind_list = array(
        'noText'    => '入力',
        'noChoise'  => '選択',
        'overText'  => '字以内で入力',
        'illegal'   => '正しく入力',
        'overLap'   => '変更'

    );

    //エラーが一つでもあるか
    private static $_has_error = false;

    //生成されたエラーのテキストをすべて格納
    private static $_all_error_string = array();

    private $_name;  //エラー項目
    private $_kind;  //エラー内容
    private $_value; //'50'字以内などの値

    public function __construct($error_name, $error_kind, $error_value) {
        $this->_name  = Error_Message::$_name_list[$error_name];
        $this->_kind  = Error_Message::$_kind_list[$error_kind];
        $this->_value = $error_value;

        Error_Message::$_has_error = true;

        $this->_addErrorString();
    }

    //一つでも生成されたら、エラーがあるのでtrue
    public static function hasError() { return Error_Message::$_has_error; }

    //全エラーの文字列を取得
    public static function getAllErrorString() { return Error_Message::$_all_error_string; }

    //このエラーの表示を一覧に追加
    private function _addErrorString() {
        $text = sprintf('%sを%s%sしてください。', $this->_name, $this->_value, $this->_kind);
        array_push(Error_Message::$_all_error_string, $text);
    }
}
