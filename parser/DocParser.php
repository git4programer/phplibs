<?php

/**
 * Doc 类及方法的注释
 */
class DocParser
{
    private static $p;
    private $param = array();

    public static function getInstance(){
        if(self::$p == null){
            self::$p = new DocParser ();
        }
        return self::$p;
    }

    /**
     * 处理注释的情况
     */
    public function parser($str){
        $this->param = array();
        if(preg_match ( '#^/\*\*(.*)\*/#s', $str, $comment ) == false ){
            return $this->param;
        }
        $comment = trim ( $comment [1] );
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false){
            return $this->param;
        }

        foreach($lines[1] as $line){
            if (empty ( $line )){
                continue;
            }
            $line = trim($line);
            if (strpos( $line, '@' ) === 0) {
                if (strpos( $line, ' ' ) > 0) {
                    // Get the parameter namek
                    $param = trim(substr( $line, strpos($line,'@') + 1, strpos ( $line, ' ' ) - 1 ));
                    $value = trim(substr( $line, strlen ( $param ) + 2 )); // Get the value
                } else {
                    $param = trim(substr ( $line, strpos($line,'@') + 1));
                    $value = '';
                }
                $this->param[$param] = $value;
            }
        }
        return $this->param;
    }

    /**
     * @获取对应注释的类型
     */
    public function getType($type){
        if(isset($this->param[$type])){
            return $this->param($type);
        } else {
            return '';
        }
    }
}
