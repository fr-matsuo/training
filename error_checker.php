<?php 

require_once('DB_connection.php');
require_once('check_function_data.php');

//名前や性別など、一つの項目についてのエラーチェックを行う。
class Error_Checker
{
    private $_show_name;            //項目名　名前　性別　など
    private $_error_array;          //エラー一覧
    private $_check_func_array;      //チェック一回分の情報　を複数持つ配列
    private $_max_turn;             //最遅チェック順

    public function __construct($show_name, $check_func_array) {
        $this->_show_name        = $show_name;
        $this->_check_func_array = $check_func_array;
        $this->_error_array = array();

        //最大turnを検索
        $this->_max_turn = 0;
        foreach ($this->_check_func_array as $func) {
            $turn = 0;
            while ($func->isTurn($turn) == false) {
                $turn++;
            }

            if ($turn > $this->_max_turn) $this->_max_turn = $turn;
        }
    }

    //この項目についてのエラーをチェックし、配列として返す。
    public function getCheckResult() {
        $functions = $this->_check_func_array;

        for ($turn = 0; $turn <= $this->_max_turn; $turn++) {//実行順番毎にチェック
            $end_flag = false;
            foreach ($functions as $func_data) {
                if ($func_data->isTurn($turn) == false) continue;

                $is_error = $func_data->check($this->_error_array, $turn);
                unset($func_data);

                if ($is_error || empty($functions)) {  //チェックを実行してエラーがあるor全てチェックした
                    $end_flag = true;
                }
            }
            if ($end_flag) break;
        }
        return $this->_error_array;
    }
}
