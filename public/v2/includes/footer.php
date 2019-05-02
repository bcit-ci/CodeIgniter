<footer class="footer">
                <div class="footer-content center-relative">

                    <div class="footer-logo">
                        <img src="images/logo.png" alt="Meelo">
                    </div>        

                    <div class="footer-mail">                                
                        <a href="mailto:hello@upriselive.com">hello@upriselive.com</a>            
                    </div>

                    <div class="footer-phone">            
                        <a href="tel:123456789">+123.456.789</a>               
                    </div>

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

        <script src="js/jquery.js"></script>			                                       
        <script src="js/jquery.sticky.js"></script>			                                               
        <script src='js/imagesloaded.pkgd.js'></script>                
        <script src='js/jquery.fitvids.js'></script>                
        <script src='js/jquery.smartmenus.min.js'></script>                                 
        <script src='js/isotope.pkgd.js'></script>                                                 
        <script src='js/owl.carousel.min.js'></script>                                                         
        <script src='js/main.js'></script>
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