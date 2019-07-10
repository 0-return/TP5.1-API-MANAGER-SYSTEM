<?php
/**
 * Created by PhpStorm.
 * User: EVOL
 * Date: 2018/11/13
 * Time: 21:13
 */
namespace app\admin\behaviors;
use think\Controller;
class Msg extends Controller
{
    private $msg = array(
        'success'               => '操作成功',
        'error'                 => '操作失败',
        'fail'                  => '操作无效',
        'check_auth'            => '你没有权限，请联系管理员',
        'check_serch'           => '请输入要查询的信息',
        'check_unique'          => '记录已存在，请不要重复添加',
        'check_next'            => '无法删除，该类目下还有信息',
        'check_upload_1'        => '上传成功',
        'check_upload_0'        => '上传失败',
    );

    public function run()
    {
        return $this->msg;
    }
}