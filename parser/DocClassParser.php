<?php
require_once('DocParser.php');

/**
 * @desc 一个分析类的注释的类
 */
class DocClassParser
{
    static private $classdata = array();

    static public function parserClass($class){
        $ref = new ReflectionClass($class);
        self::$classdata['name'] = $ref->getName();
        self::$classdata['classdoc'] = DocParser::getInstance()->parser($ref->getDocComment());
        self::$classdata['pubfunctions'] = array();
        $functions = $ref->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($functions as $kf => &$function) {
            if($function->isPublic()){
                self::$classdata['pubfunctions'][$kf]['name'] = $function->getName();
                self::$classdata['pubfunctions'][$kf]['functiondoc'] = DocParser::getInstance()->parser($function->getDocComment());
            }
        }
        return self::$classdata;
    }

}
