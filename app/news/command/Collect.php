<?php
/**
 * Created by PhpStorm.
 * User: 祥贵
 * Date: 2017/12/26
 * Time: 9:58
 */
namespace app\news\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class Collect extends Command
{


    protected function configure()
    {
        $this->setName('collect')->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {
        $newid = Db::name("btc_news")->max("newsflash_id");
        $curdate = date("Y-m-d");
        $bishijie = $this->http_get_content("http://api.bishijie.com/Newsflash/getNewsList/?client=android&page=1");
        $bsj_arr = json_decode($bishijie,true);
        foreach ($bsj_arr[$curdate]['buttom'] as $bsj){
            if($bsj['newsflash_id']>$newid) {
                Db::name("btc_news")->insert(array("newsflash_id"=>$bsj['newsflash_id'],"content"=>$bsj['content'],"top"=>$bsj['top']));
                $output->writeln("insert a news" . $bsj['newsflash_id']);
            }
            else break;

        }

    }

    protected function http_get_content($url, $cache = false){
        // 定义当前页面请求的cache key
        $key = md5($url);
        // 如果使用cache时只读一次
        if($cache){
            $file_contents = $_SESSION[$key];
            if(!empty($file_contents)) return $file_contents;
        }

        // 通过curl模拟请求页面
        $ch = curl_init();
        // 设置超时时间
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        // 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));
        curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
        //curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);

        curl_close($ch);

        // 匹配出当前页的charset
        $charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";
        //$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";

        // 非utf8编码时转码
        if($charset != 'utf-8'){
            $file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);
        }
        // 将结果记录到session中，方便下次直接读取
        $_SESSION[$key] = $file_contents;

        return $file_contents;
    }

    protected function http_post_content($url, $data,$cache = false){
        // 定义当前页面请求的cache key
        $key = md5($url);
        // 如果使用cache时只读一次
        if($cache){
            $file_contents = $_SESSION[$key];
            if(!empty($file_contents)) return $file_contents;
        }

        // 通过curl模拟请求页面
        $ch = curl_init();
        // 设置超时时间
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        // 以下内容模拟来源及代理还有agent,避免被dns加速工具拦截
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:111.222.333.4', 'CLIENT-IP:111.222.333.4'));
        curl_setopt($ch, CURLOPT_REFERER, "http://www.baidu.com");
        //curl_setopt($ch, CURLOPT_PROXY, "http://111.222.333.4:110");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $file_contents = curl_exec($ch);


        curl_close($ch);

        // 匹配出当前页的charset
        $charset = preg_match("/<meta.+?charset=[^\w]?([-\w]+)/i", $file_contents, $temp) ? strtolower($temp[1]) : "";
        //$title = preg_match("/<title>(.*)<\/title>/isU", $file_contents, $temp) ? $temp[1] : "";

        // 非utf8编码时转码
        //if($charset != 'utf-8'){
        //    $file_contents = iconv(strtoupper($charset), "UTF-8", $file_contents);
        //}
        // 将结果记录到session中，方便下次直接读取
        $_SESSION[$key] = $file_contents;

        return $file_contents;
    }
}