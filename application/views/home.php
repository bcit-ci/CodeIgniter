<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php include('includes/header.php'); ?>

    <body class="page-template-onepage">
        <div class="site-wrapper">   
            
            <?php include('includes/menu.php'); ?>

            <div id="content" class="site-content center-relative"> 
                <div id="home" class="section no-page-title">                   
                    <div class="section-wrapper block content-1170 center-relative">                                                
                        <div class="content-wrapper">                           
                            <h1 class="big-text">
                                Get A FREE <br>                               
                                Music Video
                            </h1>
                            <div class="button-holder text-left">
                                <a href="#enroll" class="button">GET IN NOW</a>
                            </div>
                        </div>                        
                    </div>
                </div> 
                
                <div id="services" class="section">                   
                    <div class="page-title-holder">
                        <h2 class="entry-title">HOW IT WORKS</h2>
                    </div>
                    <div class="section-wrapper block content-1170 center-relative">                                                
                        <div class="content-wrapper">

                            <div class="one_third ">
                                <div class="service-holder">
                                    <p class="service-num">1</p>
                                    <div class="service-txt">
                                        <h4>Enter Song Details</h4>
                                        <p>
                                            Curabitur cursus mattis ligula a maximus pellentesque in purus malesuada pharetra eros.
                                        </p>
                                        <br>
                                        <div class="button-holder text-left">
                                            <a href="#enroll" class="button-dot">
                                                <span>MORE</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="one_third ">
                                <div class="service-holder">
                                    <p class="service-num">2</p>
                                    <div class="service-txt">
                                        <h4>Song Get Selected</h4>
                                        <p>
                                            Est sem integer suscipit enim quis dictum feugiat etiam pellentesque curabitur donec porttitor.
                                        </p>
                                        <br>
                                        <div class="button-holder text-left">
                                            <a href="#enroll" class="button-dot">
                                                <span>MORE</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="one_third last">
                                <div class="service-holder">
                                    <p class="service-num">3</p>
                                    <div class="service-txt">
                                        <h4>Record Your FREE Video</h4>
                                        <p>
                                            Donec vel est sem integer suscipit enim quis lorem posuere vestibulum metus tempor vitae.
                                        </p>
                                        <br>
                                        <div class="button-holder text-left">
                                            <a href="#enroll" class="button-dot">
                                                <span>MORE</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                            <div class="clear"></div>
                        </div>                        
                    </div>
                </div>

                <div id="enroll" class="section">                   
                    <div class="page-title-holder">
                        <h2 class="entry-title">ENROLL NOW</h2>
                    </div>
                    <div class="section-wrapper block content-1170 center-relative">                                                
                        <div class="content-wrapper">
                            <div class="one_half ">
                                <p class="title-description-up">100% FREE</p>
                                <h3 class="entry-title medium-text">
                                    Lets make your <br>
                                    Music Video now
                                </h3>
                                <p>No matter what stage you're at in your career, Upriselive's got your back. We can get your video to reach over a million users worldwide.</p>
                                <br>
                                <div class="social">
                                    <a href="#" target="_blank">
                                        <span class="fa fa-facebook"></span>
                                    </a>
                                </div>
                                <div class="social">
                                    <a href="#" target="_blank">
                                        <span class="fa fa-twitter"></span>
                                    </a>
                                </div>
                                <div class="social">
                                    <a href="#" target="_blank">
                                        <span class="fa fa-instagram"></span>
                                    </a>
                                </div>
                                <div class="social">
                                    <a href="#" target="_blank">
                                        <span class="fa fa-vimeo"></span>
                                    </a>
                                </div>
                                <div class="social">
                                    <a href="#" target="_blank">
                                        <span class="fa fa-behance"></span>
                                    </a>
                                </div>
                            </div>

                            <div class="one_half last ">
                                <div class="contact-form">
                                <?php echo form_open($action = "home/request", 
                                                        $attributes = array("id" => "submit-form"
                                                    ));?>
                                        <p><input type="text" placeholder="Your name" name="name"></p>
                                        <p><input type="email" placeholder="Your email address" name="email"></p>
                                        <p><input type="text" placeholder="Your phone number" name="phonenumber"></p>
                                        <p><input type="text" placeholder="Your song link ( i.e on soundcloud, naijaloaded, tooxclsuive, etc )" name="link"></p>
                                        <p class="contact-submit-holder"><input type="submit" value="SUBMIT"></p>
                                    <?php echo form_close();?>
                                </div>
                            </div>                            
                            <div class="clear"></div>

                        </div>                        
                    </div>
                </div> 
            </div>

            
<?php include('includes/footer.php'); ?>