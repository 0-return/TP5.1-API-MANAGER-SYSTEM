<?php
namespace app\index\controller;

use app\index\common\controller\Init;
use think\facade\Request;

class Index extends Init
{
    function initialize()
    {
        //parent::_init();
    }

    public function index()
    {
    	return view();
    }

    public function begin()
    {
        $data = Request::post();
        return $this->request($data);
    }
    /**
     * 执行
     */
    public function request($data)
    {
        $param = http_build_query(json_decode($data['param'],1));
        if ($data['requestType'] == 'POST')
        {
            $response = Curl($data['url'],$param,1);
        }else{
            $response = Curl($data['url'],$param);
        }
        return $response;
    }
    /**
     * 保存访问
    */
    public function save($data)
    {

    }
}
