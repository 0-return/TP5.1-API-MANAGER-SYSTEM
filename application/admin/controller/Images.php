<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2019/5/10
 * Time: 20:21
 */
namespace app\admin\controller;
use app\admin\common\controller\Init;
use think\Db;

class Images extends Init
{
    private $fields = array(
        'title' => '标题',
        'ext' => '类型',
    );
    public function initialize()
    {
        parent::_init();
        $this->table = $this->config['prefix']."source_images";
    }

    public function index()
    {
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }

        $map[] = ['status','>','-1'];
        $where['where'] = $map;
        $this->_list('',$where,'','','');
        $this->assign('fields',$this->fields);
        return view();
    }

    public function source()
    {
        $map = $this->_search();
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $map[] = ['status','>','-1'];
        $where['where'] = $map;
        $res = $this->_list('',$where,true);
        $list = array(
            'page' => $res->render(),
            'list' => $res->items(),
            'count' => $res->total(),
            'web_url' => $this->config['assist']['web_url'],
        );
        self::returnMsg(10000,'获取成功',$list);
    }

    /**
     * @auth YW
     * @date 2018.11.19
     * @purpose 图片上传
     * @return void
     */
    public function upload()
    {
        $file = request()->file('image');
        $img = '';
        if (is_array($file))
        {
            foreach ($file as $key => $value)
            {
                $img[$key] = $this->mvfile($value);
            }
        }else{
            $img[] = $this->mvfile($file);
        }

        $imgArr = array(
            'url' => $this->config['assist']['web_url'],
            'img' => $img,
        );

        if ($this->savefile($img))
        {
            self::returnMsg(10000,'上传成功',$imgArr);
        }else{
            self::returnMsg(10000,'上传失败');
        }

    }
    /**
     * @auth YW
     * @date 2018.11.21
     * @purpose 图片移动
     * @return void
     */
    private function mvfile(&$files)
    {
        $path = strtolower($this->config['assist']['upload']).'/images/'.date('Y-m-d',time());
        $info = $files->validate(['size'=>10240000,'ext'=>'jpg,png,gif'])->rule('uniqid')->move($path);
        if ($info)
        {
            $img['info'] = $info->getInfo();
            $img['path'] = '/'.$this->config['assist']['upload'].'/images/'.date('Y-m-d',time()).'/'.$info->getSaveName();
            return $img;
        }else{
            self::returnMsg(10000,$info->getError());
        }
    }
    /**
     * @auth YW
     * @date 2018.11.22
     * @purpose 保存图片
     * @return void
     */
    private function savefile(&$list)
    {
        $count = count($list);
        $i = 0;
        foreach ($list as $ky => $vl)
        {
            $data['source'] = $vl['path'];
            $data['type'] = $vl['info']['type'];
            $data['title'] = $vl['info']['name'];
            $data['size'] = $vl['info']['size'];
            $data['status'] = '1';
            $data['add_time'] = time();
            $res = Db::table($this->table)->insert($data);
            if ($res)
            {
                $i++;
            }
        }
        if ($count == $i){
            return true;
        }
    }
    /**
     * @auth YW
     * @date 2018.11.28
     * @purpose 删除图片
     * @return void
     */
    private function delfile(&$list)
    {

    }

}