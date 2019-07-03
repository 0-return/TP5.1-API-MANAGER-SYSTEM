<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Cookie;

/**
 * 获取ip地址
 * @param
 * @return void
 */
function getIP()
{
    if (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
    }
    elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    }
    elseif (getenv('HTTP_X_FORWARDED')) {
        $ip = getenv('HTTP_X_FORWARDED');
    }
    elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ip = getenv('HTTP_FORWARDED_FOR');

    }
    elseif (getenv('HTTP_FORWARDED')) {
        $ip = getenv('HTTP_FORWARDED');
    }
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
/**
 * md5加密工具
 * @param  string prefix [前缀]
 * @param  string $key [密钥]
 * @param  string $str [加密字符串]
 * @return  string
 */
function factoryMd5($str, $prefix = '', $key = '123456')
{

    $string = md5(md5($prefix).$str.md5($key));
    $str =  substr_replace($string,$prefix,1,0);
    return $str;
}

/**
 * 请求数据（含POST，GET模式）
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function Curl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

/**
 * note:无限分类(下拉样式)
 * auth:YW
 * input $obj 模型 $table对象 $current 选中的id $parentid父id $count累加次数 $field特殊字段
 * return htmlstr
 */
function cateTreeOption($obj ,$field = array(), $parentid = 0, $count = 0, $current = '')
{

    $where[$field['pid']] = $parentid;
    $where[$field['status']] = [$field['status'],'>','-1'];
    $res = \think\Db::table($obj)->where($where)->select();
    if (empty($res)) return;
    $optionHtml = '';
    $linstr = str_repeat("|——", $count);
    foreach ($res as $key => $value) {
        if ($value[$field['id']] == $current) {
            $optionHtml .= "<option selected value='{$value[$field['id']]}'>{$linstr} {$value[$field['title']]}</option>";
        } else {
            $optionHtml .= "<option value='{$value[$field['id']]}'>{$linstr} {$value[$field['title']]}</option>";
        }
        $optionHtml .= cateTreeOption($obj , $field, $value[$field['id']], $count + 1, $current);
    }
    return $optionHtml;
}

/**
 * note:目录创建
 * auth:YW
 * date:2018/07/03
 */
function makedir( $dir , $mode = 0700 ) {
    if(strpos($dir , "/" )){
        $dir_path = "" ;
        $dir_info = explode ( "/" , $dir );
        foreach($dir_info   as   $key => $value ){
            $dir_path .= $value ;
            if (!file_exists($dir_path )){
                @mkdir ( $dir_path , $mode ) or die ( "建立文件夹时失败了" );
                @chmod ( $dir_path , $mode );
            } else {
                $dir_path .= "/" ;
                continue ;
            }
            $dir_path .= "/" ;
        }
        return   $dir_path ;
    } else {
        @mkdir( $dir , $mode ) or die( "建立失败了,请检查权限" );
        @chmod ( $dir , $mode );
        return   $dir ;
    }
}

/**
 * auth YW
 * note 文件名
 * date 2018-08-23
 */
function createFile($path,$name)
{
    if (!is_dir($path)) mkdir($path);
    if (!file_exists($path.$name))
    {
        if (!is_writable($path.$name)) chmod($path.$name, 0777);
        $res = @file_put_contents($path.$name, '--'.PHP_EOL, FILE_APPEND);
    }
    if ($res > 0)
    {
        return true;
    }
}
/*******************************************20181117**********************************************************/
/**
 * note:6为码
 * auth:YW
 * date:2018/07/03
 */
function randCode($length = '')
{

    $code = mt_rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    return $code;
}

/**
 * note:聚合短信
 * auth:YW
 * date:2018/07/03
 */
function JhSms(&$data,&$config)
{
    //$obj = new Jhsms($config);
    $obj = new \app\common\controller\Jhsms($config);
    $sms = array(
        'mobile' => $data['phone'],
        'tpl_id' => $data['tpl_id'],
    );
    if (isset($data['tpl_value']) && !empty($data['tpl_value']))
    {
        $sms['tpl_value'] = $data['tpl_value'];
    }

    $res = $obj->Jh_send($sms);
    return $res;
}

/**
 * note:token生成
 * auth:YW
 * date:2018/12/09
 * return: str
 */
function makeToken(){
    $str = md5(uniqid(md5(microtime(true)), true)); //生成一个不会重复的字符串
    $str = sha1($str); //加密
    return $str;
}
/**
 * note:验证token
 * auth:YW
 * date:2018/12/14
 * return: arr&bool
 */
function verifyToken($obj,$table = '',$where = '',$list,$config,$ischeck = false){

    $res = $table?$obj->table($table)->where($where)->field('id,token')->find():$obj->where($where)->field('id,token')->find(); unset($where);
    if ($res)
    {
        if ($ischeck)               //动态变更
        {
            $data['token'] = makeToken();
            $where['uid'] = $list['uid'];
            $res = $table?$obj->table($table)->where($where)->save($data):$obj->where($where)->save($data);
        }else{                      //登录变更
            $data['token'] = $list['token'];
            $res = true;
        }

        if ($res)
        {
            $user = array('uid' => $list['uid'], 'token' => $data['token']);
            $timeout = isset($config['mtimeout']) && !empty($config['mtimeout'])?$config['mtimeout']:604800;
            cookie('user',$user,$timeout);
            return $data;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
/**
 * note:将空数据信息改成''或""
 * auth:YW
 * date:2018/12/14
 * return: arr&bool
 */
function data2empty($data = '')
{
    if (is_array($data))
    {
        foreach ($data as $key => $value)
        {

            if (is_array($value))
            {

                $res[$key] = data2empty($value);
            }else{
                $res[$key] = $value == '' || $value == null?'':$value;

            }
        }
    }else{
        $res =  $data == '' || $data == null?'':$data;
    }
    return $res;

}


/**
 * note:获取用户信息
 * auth:YW
 * date:2018/01/07
 * input: $module 模块名称
 * return: array
 */
function getUser($module = '')
{
    $user = json_decode(Cookie::get($module . '_info'),1);
    return $user;
}

/**
 * note:存储用户信息
 * auth:yw
 * date:2018/09/13
 * input: $module 模块名称，$data 用户信息，$config 配置文件
 * return: void
 */
function putUser($module = '',$data = '',$config = '')
{
    if (empty($config['timeout'])) $config['timeout'] = '30';
    $conf['expire'] = intval(time()+3600*$config['timeout']);
    $conf['path'] = '/';
    cookie($module.'_info',json_encode($data),$conf);
    Session($module.'_token',$data['token']);
}




/**
 * note:删除空的数组（最高支持三维数组）
 * auth:YW
 * date:2018/07/13
 */
function paramFormart($data)
{

    foreach ($data as $key => $value)
    {
        if (is_array($value))
        {

            foreach ($value as $ky => $vl)
            {
                if ($vl = '' || $vl = "")
                {
                    unset($value[$vl]);
                }else{
                    $value[$ky] = $vl;
                }
            }
            $data[$key] = $value;
        }else{
            if ($value == '' || $vl = "")
            {

                unset($data[$key]);
            }else{

                $data[$key] = $value;
            }

        }
    }
    return $data;
}
/**
 * note:订单编号
 * auth:杨炜
 * return string
 */
function getStrGuid()
{
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));

    $hyphen = chr(45);// "-"
    $uuid = substr($charid, 0, 8) . $hyphen
        . substr($charid, 8, 4) . $hyphen
        . substr($charid, 12, 4) . $hyphen
        . substr($charid, 16, 4);
    return $uuid;
}

/**
 * note:生成指定长度的纯数字字符串
 * auth:杨炜
 * input 长度
 * return $iden 前缀标识 $len长度
 */
function randStr($iden = '', $len = 16, $group = 4)
{
    $str = '';
    for ($j = 1; $j <= $len; $j++) {
        if ($j % $group == 0) {
            $str .= mt_rand(0, 9) . '-';
        } else {
            $str .= mt_rand(0, 9);
        }
    }
    $str = trim($str, '-');
    return $str;
}

/**
 * note:生成指定长度的纯数字字符串
 * auth:杨炜
 * input 长度
 * return $iden 前缀标识 $len长度
 */
function randNum($iden = '', $len = 16, $group = 4)
{
    $str = '';
    for ($j = 1; $j <= $len; $j++) {
        if ($j % $group == 0) {
            $str .= mt_rand(0, 9);
        } else {
            $str .= mt_rand(0, 9);
        }
    }
    $str = trim($str, '-');
    return $str;
}




/**
*note:获取分类信息串（通用）
*auth:YW
*date:2018/06/25
*return str
*/
function getCateStr($obj, $table = '', $pid, $field, $field_str = array())
{

    $where[$field_str['condition']] = $pid;
    $str = '';
    if (empty($table))
    {
        $res = $obj->where($where)->field($field)->find();

    }else{
        $res = $obj->table($table)->where($where)->field($field)->find();

    }

    if ($res)
    {
        $str .= get_type_str($obj, $table, $res[$field_str['pid']], $field, $field_str).'>';
        $str .= $res[$field_str['flag']];

        return trim($str,'>');
    }

}

/**
 *note:二维数组随机合并
 *auth:YW
 *date:2018/06/25
 *return str
 */
function shuffleMergeArray($array1, $array2)
{
    $mergeArray = array();
    $sum = count($array1) + count($array2);
    for ($k = $sum; $k > 0; $k--) {
        $number = mt_rand(1, 2);
        if ($number == 1) {
            $mergeArray[] = $array2 ? array_shift($array2) : array_shift($array1);
        } else {
            $mergeArray[] = $array1 ? array_shift($array1) : array_shift($array2);
        }
    }
    return $mergeArray;
}
