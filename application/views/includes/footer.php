<footer class="footer">
                <div class="footer-content center-relative">
                    <div class="footer-logo">
                        <img src="public/images/footer_logo.png" alt="Upriselive" />
                    </div>        
                    <div class="footer-logo-divider"></div>
                    <div class="footer-mail">            
                        <a href="mailto:hello@upriselive.com">hello@upriselive.com</a>
                    </div>
                    <div class="footer-social-divider"></div>
                    <div class="social-holder">
                        <a href="#">
                            <span class="fa fa-twitter"></span>
                        </a>
                        <a href="#">
                            <span class="fa fa-facebook"></span>
                        </a>
                        <a href="#">
                            <span class="fa fa-behance"></span>
                        </a>
                        <a href="#">
                            <span class="fa fa-dribbble"></span>
                        </a>
                    </div>

                    <div class="copyright-holder">© Upriselive 2019. All rights reserved.</div>
                </div>
            </footer>
        </div>

        <script src="public/js/jquery.js"></script>			                                       
        <script src="public/js/jquery.sticky.js"></script>			                                       
        <script src="public/js/tipper.js"></script>			                                       
        <script src="public/js/jarallax.js"></script>			                                       
        <script src="public/js/jarallax-element.min.js"></script>			                                       
        <script src='public/js/imagesloaded.pkgd.js'></script>                
        <script src='public/js/jquery.fitvids.js'></script>                
        <script src='public/js/jquery.smartmenus.min.js'></script>                                 
        <script src='public/js/isotope.pkgd.js'></script>                                                 
        <script src='public/js/owl.carousel.min.js'></script>                          
        <script src='public/js/jquery.sticky-kit.min.js'></script>                          
        <script src='public/js/main.js'></script>
        <script type="text/javascript">

           jQuery("document").ready(function() {
          // The DOM is ready!
          // The rest of your code goes here!
            var form = document.getElementById("submit-form");

            form.onsubmit = function (e) {
              // stop the regular form submission
              e.preventDefault();

              // collect the form data while iterating over the inputs
              var data = {};
              for (var i = 0, ii = form.length; i < ii; ++i) {
                var input = form[i];
                if (input.name) {
                  data[input.name] = input.value;
                }
              }

             
              jQuery.ajax({
                type: "POST",
                url: "https://api.fungifting.com/lead/upriselive",
                data: data,
                success: function(data){ 
                  window.location.href = "/thanks.php"; 
                },
                error : function() {console.log("Failed");}

              });


              
            };
      });

      </script>

    </body>
</html>