<?php
namespace app\common\controller;
use think\facade\Request;
use think\facade\Cookie;

trait Send
{
    /**
     * 返回成功
     */
    public static function returnMsg($code = 10000,$message = '',$data = [])
    {
        $res['code'] = (int)$code;
        $res['message'] = !empty($message)?'提示：'.$message:'';
        $res['data'] = is_array($data) ? $data : ['info'=>$data];
        echo json_encode($res); exit(0);
    }
    /**
     * 返回成功带token
     */
    public static function returnMsgAndToken($code = 10000,$message = '',$data = [])
    {
        $res['code'] = (int)$code;
        $res['message'] = !empty($message)?'提示：'.$message:'';
        $res['data'] = is_array($data) ? $data : ['info'=>$data];
        $user = json_decode(Cookie::get(Request::module().'_info'),1);
        $res['uid'] = $user['id'];
        $res['token'] = $user['token'];
        echo json_encode($res); exit(0);
    }
}

