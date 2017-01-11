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
        <title>文章列表</title>
        <?php 
            //正确的引入方式
            $this->load->view('layout/script.php');
        ?>
        <style>
            .newAticle{
                font-size: 20px;
                float: right;
            }
        </style>
    </head>
    <body>
        
        <?php
            echo "欢迎来到文章列表";
        ?>
        <a href="article/newAticle" target="_blank" class="newAticle">写新文章</a>
    </body>
</html>
