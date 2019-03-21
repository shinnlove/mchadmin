<?php

namespace app\index\controller;

use think\Request;
use think\Session;
use think\Db;

/**
 * 请求登录系统控制器。
 * @author 赵臣升。
 * CreateTime:2015/07/18 21:31:36.
 */
class IndexRequest extends PCRequestGuest {
    /**
     * 总店请求登录。
     */
    public function login() {
        if (!Request::instance()->isPost()) _404("页面不存在", U('Index/Enterprise/main', '', '', true)); // 禁止非法访问

        // 定义全局的错误码
        $ajaxresult = array(
            'errCode' => 10001,
            'errMsg' => "网络繁忙，请稍后再试！"
        );

        // 接收账号密码
        $logindata = array(
            'account' => input('account', ''),
            'password' => input('password', '')
        );

        // 以下几种情况直接毙掉返回
//        if (Session::get('verify')!= md5($_POST ['verify'])) {
//            $ajaxresult ['errCode'] = 10002;
//            $ajaxresult ['errMsg'] = "验证码已过期，请刷新后重试！";
//            return json($ajaxresult);
//        }
        if (empty ($logindata ['account']) || empty ($logindata ['password'])) {
            $ajaxresult ['errCode'] = 10003; // 服务器未能接收到数据
            $ajaxresult ['errMsg'] = "网络繁忙，请重新登录！";
            return json($ajaxresult);
        }

        $checkaccount = array(
            'account' => $logindata ['account'],
            'password' => md5($logindata ['password']),
            'is_del' => 0
        );
        $existinfo = Db::name('enterprise')->where($checkaccount)->find(); // 从账号密码表里查询
        if ($existinfo) {
            // 验证是否过期
            $service_start = strtotime($existinfo ['service_start_time']); // 服务开始日期整型
            $service_end = strtotime($existinfo ['service_end_time']); // 服务结束日期整型
            $timenow = time(); // 获取当前时间
            if ($timenow >= $service_start && $timenow <= $service_end) {
                // 服务有效期内
                $emap = array(
                    'e_id' => $existinfo ['e_id'],
                    //'is_del' => 0
                );
                $einfo = Db::name('einfo_manage')->where($emap)->find(); // 查询出登录企业信息

                // 放入session缓存中，这里5版本写法
                Session::set('curEnterprise', $einfo);

                $ajaxresult ['errCode'] = 0;
                $ajaxresult ['errMsg'] = "登录成功，欢迎您回来，" . $einfo ['e_name'] . "！";
            } else {
                $ajaxresult ['errCode'] = 10004;
                $ajaxresult ['errMsg'] = "您的账号已过服务期，请重新激活账号！";
            }
        } else {
            $ajaxresult ['errCode'] = 10005;
            $ajaxresult ['errMsg'] = "用户名或密码错误！";
        }
        return json($ajaxresult); // 将信息返回给前台
    }
}

?>