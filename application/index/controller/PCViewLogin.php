<?php

namespace app\index\controller;

use think\Controller;
use think\Session;

/**
 * PC端登录控制器，检测当前企业是否登录。
 * @author 赵臣升。
 * CreateTime:2015/07/18 16:20:36.
 */
class PCViewLogin extends Controller {
    /**
     * 初始化控制登录。
     */
    public function _initialize() {
        if (! Session::get("curEnterprise")) {
            $this->redirect ( 'Index/Index/index' );
        }
    }
}
?>