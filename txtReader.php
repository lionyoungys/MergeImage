<?php
/**
 * Created by PhpStorm.
 * User: 帅
 * Date: 2017/2/28
 * Time: 17:22
 */
$fileName = './txt/jueshiwushen.txt';

/**
 * php-文件读取
 * @param string $fileName: 文件全路径
 * @return void
 */
function txtReader($fileName) {
    $fp = fopen($fileName,'rb');
    $pattern = '/^第\\d+章/';    //匹配章节正则
    while(!feof($fp)) {
        set_time_limit(0);
        $content = iconv('GBK','utf-8//IGNORE',fgets($fp));
        $ft = ftell($fp);
        fseek($fp,$ft);
        $patternInfo = preg_match($pattern,$content);
        if ($patternInfo) {
            $content .= '<span style="color: red">章</span>';
        }
        print $content.'<br/>';
        flush();
        ob_flush();
    }
    fclose($fp);
}
txtReader($fileName);
