<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Pine-我的blog</title>
        <?php
            $this->load->view('layout/script.php');
        ?>
    </head>
    <body>
        <div>
            <div class="header">
                <div class="logo"></div>
                <div class="contents">
                    <ul>
                        <li>首页</li>
                        <li>分类</li>
                        <li>主题</li>
                        <li>原创</li>
                    </ul>
                </div>
            </div>
            <div class="main">
                <div class="left">
                    <div class="header"></div>
                    <div class="articles">
                        <div class="article">
                            <div class="title">
                                <span></span>
                                <span class="info"></span>
                            </div>
                            <div class="abstract"></div>
                        </div>
                        <div class="page"></div>
                    </div>
                </div>
                <div class="right">
                    
                </div>
            </div>
            <div class="footer">
                <a>自我介绍</a>|
                <a>联系我</a>|
                <a>给我留言</a>
            </div>
        </div>
    </body>
</html>
