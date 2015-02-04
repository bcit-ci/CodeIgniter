$( document ).ready(function() {
    // Shift nav in mobile when clicking the menu.
    $(document).on('click', "[data-toggle='wy-nav-top']", function() {
      $("[data-toggle='wy-nav-shift']").toggleClass("shift");
      $("[data-toggle='rst-versions']").toggleClass("shift");
    });
    // Close menu when you click a link.
    $(document).on('click', ".wy-menu-vertical .current ul li a", function() {
      $("[data-toggle='wy-nav-shift']").removeClass("shift");
      $("[data-toggle='rst-versions']").toggleClass("shift");
    });
    $(document).on('click', "[data-toggle='rst-current-version']", function() {
      $("[data-toggle='rst-versions']").toggleClass("shift-up");
    });  
    // Make tables responsive
    $("table.docutils:not(.field-list)").wrap("<div class='wy-table-responsive'></div>");	
	// ---
	// START DOC MODIFICATION BY RUFNEX
	// v1.0 04.02.2015
	// Add ToogleButton to get FullWidth-View by Johannes Gamperl codeigniter.de		
	var ciNav = '<style >#nav { background-color: #494949; margin: 0; padding: 0;display:none;}#nav2 { background: url(data:image/jpeg;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAARgAA/+4ADkFkb2JlAGTAAAAAAf/bAIQABAMDAwMDBAMDBAYEAwQGBwUEBAUHCAYGBwYGCAoICQkJCQgKCgwMDAwMCgwMDQ0MDBERERERFBQUFBQUFBQUFAEEBQUIBwgPCgoPFA4ODhQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgAMgAzAwERAAIRAQMRAf/EAFkAAQADAQAAAAAAAAAAAAAAAAABBQcIAQEAAAAAAAAAAAAAAAAAAAAAEAABAgYDAAAAAAAAAAAAAAAAAVERAtMEFJRVBxgRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/AMRAAAAAAAA7a87dZcCu3e1wHnbrLgV272uA87dZcCu3e1wHnbrLgV272uA87dZcCu3e1wHnbrLgV272uA87dZcCu3e1wN/wJGAYEjAMCRgGBIwDAkYBgSMAwJGAsoIwCCMAgjAIIwCCMAgjAIIwEgAAAAAAAAAAAAAAAAAAAAAAAH//2Q==) repeat-x scroll left top transparent; margin: 0; padding: 0 310px 0 0; text-align: right;display:none;}#nav_inner { background-color: transparent; font-family: Lucida Grande,Verdana,Geneva,sans-serif; font-size: 11px; margin: 0; padding: 8px 12px 0 20px;}table.ciNav { background-color: #494949; width: 100%; }table.ciNav ul { margin: 10px; margin-top:0; padding: 5px; }table.ciNav td li { font-size:0.82em; margin-left: 20px; list-style-image: url(data:image/gif;base64,R0lGODlhCwAJALMJAO7u7uTk5PLy8unp6fb29t7e3vj4+Li4uIWFheTk5AAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAAkALAAAAAALAAkAAAQoMJ1JqTQ4Z3SI98jHCWSJkByArCyiHkMsIzEX3DeCc0Xv+4hEa5iIAAA7); }table.ciNav h3 { margin:0; margin-left: 10px; }table.ciNav h3.first { margin-bottom: 20px; }table.ciNav h3 a { color:#fff;text-decoration: none; font-size:12px; }table.ciNav td li a { color:#fff;text-decoration: none; font-size:11px; line-height:1.4em; font-weight: 300; color: #aaa; }table.ciNav td.td_sep {padding-left:20px; background: url(data:image/jpeg;base64,/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAARgAA/+4ADkFkb2JlAGTAAAAAAf/bAIQABAMDAwMDBAMDBAYEAwQGBwUEBAUHCAYGBwYGCAoICQkJCQgKCgwMDAwMCgwMDQ0MDBERERERFBQUFBQUFBQUFAEEBQUIBwgPCgoPFA4ODhQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQU/8AAEQgACAACAwERAAIRAQMRAf/EAEsAAQAAAAAAAAAAAAAAAAAAAAcBAQAAAAAAAAAAAAAAAAAAAAAQAQEAAAAAAAAAAAAAAAAAAADVEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwAesg//2Q==) repeat-y scroll left top transparent;}</style>';
	ciNav += '<div style="background:#494949;"><div id="nav"><div id="nav_inner">';
	ciNav += '<table class="ciNav"><tr><td valign="top"><h3 class="first"><a href="../general/welcome.html">Welcome to CodeIgniter</a></h3><h3><a href="../installation/index.html">Installation Instructions</a></h3><ul><li><a href="../installation/downloads.html">Downloading CodeIgniter</a></li><li><a href="../installation/index.html">Installation Instructions</a></li><li><a href="../installation/upgrading.html">Upgrading From a Previous Version</a></li><li><a href="../installation/troubleshooting.html">Troubleshooting</a></li></ul><h3><a href="../overview/index.html">CodeIgniter Overview</a></h3><ul><li><a href="../overview/getting_started.html">Getting Started</a></li><li><a href="../overview/at_a_glance.html">CodeIgniter at a Glance</a></li><li><a href="../overview/features.html">Supported Features</a></li><li><a href="../overview/appflow.html">Application Flow Chart</a></li><li><a href="../overview/mvc.html">Model-View-Controller</a></li><li><a href="../overview/goals.html">Architectural Goals</a></li></ul><h3><a href="../tutorial/index.html">Tutorial</a></h3><ul><li><a href="../tutorial/static_pages.html">Static pages</a></li><li><a href="../tutorial/news_section.html">News section</a></li><li><a href="../tutorial/create_news_items.html">Create news items</a></li><li><a href="../tutorial/conclusion.html">Conclusion</a></li></ul><h3><a href="../contributing/index.html">Contributing to CodeIgniter</a></h3><ul><li class="toctree-l2"><a href="../documentation/index.html">Writing CodeIgniter Documentation</a></li><li class="toctree-l2"><a href="../DCO.html">Developer&#8217;s Certificate of Origin 1.1</a></li></ul></td><td valign="top" class="td_sep"><h3><a href="../general/index.html">General Topics</a></h3><ul><li><a href="../general/urls.html">CodeIgniter URLs</a></li><li><a href="../general/controllers.html">Controllers</a></li><li><a href="../general/reserved_names.html">Reserved Names</a></li><li><a href="../general/views.html">Views</a></li><li><a href="../general/models.html">Models</a></li><li><a href="../general/helpers.html">Helpers</a></li><li><a href="../general/libraries.html">Using CodeIgniter Libraries</a></li><li><a href="../general/creating_libraries.html">Creating Libraries</a></li><li><a href="../general/drivers.html">Using CodeIgniter Drivers</a></li><li><a href="../general/creating_drivers.html">Creating Drivers</a></li><li><a href="../general/core_classes.html">Creating Core System Classes</a></li><li><a href="../general/ancillary_classes.html">Creating Ancillary Classes</a></li><li><a href="../general/hooks.html">Hooks - Extending the Framework Core</a></li><li><a href="../general/autoloader.html">Auto-loading Resources</a></li><li><a href="../general/common_functions.html">Common Functions</a></li><li><a href="../general/compatibility_functions.html">Compatibility Functions</a></li><li><a href="../general/routing.html">URI Routing</a></li><li><a href="../general/errors.html">Error Handling</a></li><li><a href="../general/caching.html">Caching</a></li><li><a href="../general/profiling.html">Profiling Your Application</a></li><li><a href="../general/cli.html">Running via the CLI</a></li><li><a href="../general/managing_apps.html">Managing your Applications</a></li><li><a href="../general/environments.html">Handling Multiple Environments</a></li><li><a href="../general/alternative_php.html">Alternate PHP Syntax for View Files</a></li><li><a href="../general/security.html">Security</a></li><li><a href="../general/styleguide.html">PHP Style Guide</a></li></ul></td><td valign="top" class="td_sep"><h3><a href="../libraries/index.html">Libraries</a></h3><ul><li><a href="../libraries/benchmark.html">Benchmarking Class</a></li><li><a href="../libraries/caching.html">Caching Driver</a></li><li><a href="../libraries/calendar.html">Calendaring Class</a></li><li><a href="../libraries/cart.html">Shopping Cart Class</a></li><li><a href="../libraries/config.html">Config Class</a></li><li><a href="../libraries/email.html">Email Class</a></li><li><a href="../libraries/encrypt.html">Encrypt Class</a></li><li><a href="../libraries/encryption.html">Encryption Library</a></li><li><a href="../libraries/file_uploading.html">File Uploading Class</a></li><li><a href="../libraries/form_validation.html">Form Validation</a></li><li><a href="../libraries/ftp.html">FTP Class</a></li><li><a href="../libraries/image_lib.html">Image Manipulation Class</a></li><li><a href="../libraries/input.html">Input Class</a></li><li><a href="../libraries/javascript.html">Javascript Class</a></li><li><a href="../libraries/language.html">Language Class</a></li><li><a href="../libraries/loader.html">Loader Class</a></li><li><a href="../libraries/migration.html">Migrations Class</a></li><li><a href="../libraries/output.html">Output Class</a></li><li><a href="../libraries/pagination.html">Pagination Class</a></li><li><a href="../libraries/parser.html">Template Parser Class</a></li><li><a href="../libraries/security.html">Security Class</a></li><li><a href="../libraries/sessions.html">Session Library</a></li><li><a href="../libraries/table.html">HTML Table Class</a></li><li><a href="../libraries/trackback.html">Trackback Class</a></li><li><a href="../libraries/typography.html">Typography Class</a></li><li><a href="../libraries/unit_testing.html">Unit Testing Class</a></li><li><a href="../libraries/uri.html">URI Class</a></li><li><a href="../libraries/user_agent.html">User Agent Class</a></li><li><a href="../libraries/xmlrpc.html">XML-RPC and XML-RPC Server Classes</a></li><li><a href="../libraries/zip.html">Zip Encoding Class</a></li></ul></td><td valign="top" class="td_sep"><h3><a href="../database/index.html">Database Reference</a></h3><ul><li><a href="../database/examples.html">Quick Start: Usage Examples</a></li><li><a href="../database/configuration.html">Database Configuration</a></li><li><a href="../database/connecting.html">Connecting to a Database</a></li><li><a href="../database/queries.html">Running Queries</a></li><li><a href="../database/results.html">Generating Query Results</a></li><li><a href="../database/helpers.html">Query Helper Functions</a></li><li><a href="../database/query_builder.html">Query Builder Class</a></li><li><a href="../database/transactions.html">Transactions</a></li><li><a href="../database/metadata.html">Getting MetaData</a></li><li><a href="../database/call_function.html">Custom Function Calls</a></li><li><a href="../database/caching.html">Query Caching</a></li><li><a href="../database/forge.html">Database Manipulation with Database Forge</a></li><li><a href="../database/utilities.html">Database Utilities Class</a></li><li><a href="../database/db_driver_reference.html">Database Driver Reference</a></li></ul></td><td valign="top" class="td_sep"><h3><a href="../helpers/index.html">Helpers</a></h3><ul><li><a href="../helpers/array_helper.html">Array Helper</a></li><li><a href="../helpers/captcha_helper.html">CAPTCHA Helper</a></li><li><a href="../helpers/cookie_helper.html">Cookie Helper</a></li><li><a href="../helpers/date_helper.html">Date Helper</a></li><li><a href="../helpers/directory_helper.html">Directory Helper</a></li><li><a href="../helpers/download_helper.html">Download Helper</a></li><li><a href="../helpers/email_helper.html">Email Helper</a></li><li><a href="../helpers/file_helper.html">File Helper</a></li><li><a href="../helpers/form_helper.html">Form Helper</a></li><li><a href="../helpers/html_helper.html">HTML Helper</a></li><li><a href="../helpers/inflector_helper.html">Inflector Helper</a></li><li><a href="../helpers/language_helper.html">Language Helper</a></li><li><a href="../helpers/number_helper.html">Number Helper</a></li><li><a href="../helpers/path_helper.html">Path Helper</a></li><li><a href="../helpers/security_helper.html">Security Helper</a></li><li><a href="../helpers/smiley_helper.html">Smiley Helper</a></li><li><a href="../helpers/string_helper.html">String Helper</a></li><li><a href="../helpers/text_helper.html">Text Helper</a></li><li><a href="../helpers/typography_helper.html">Typography Helper</a></li><li><a href="../helpers/url_helper.html">URL Helper</a></li><li><a href="../helpers/xml_helper.html">XML Helper</a></li></ul></td></tr></table>';
	ciNav += '</div></div><div id="nav2"><a name="top"></a><a href="#" id="openToc"><img src="data:image/jpeg;base64,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" title="Toggle Table of Contents" alt="Toggle Table of Contents" /></a></div></div>';
	$('body').prepend(ciNav);
	//
	var a = ['Index', 'CodeIgniter User Guide¶', 'Change Log¶', 'Developer’s Certificate of Origin 1.1¶', 'The MIT License (MIT)¶'];
	if ($.inArray($('h1').text(), a) > 0 || $('h2').text() == 'Search Results')
	{
		$('table.ciNav a').each(function(){
			$(this).attr('href', $(this).attr("href").replace('../', ''));
		});	
		console.log(1111);
	}
	//
	$('#openToc').click(function(){
		$('#nav').slideToggle();
	});
	$('.wy-breadcrumbs').append('<div style="float:right;"><div style="text-decoration:underline;color:blue;margin-left:5px;" id="closeMe"><img title="toc" alt="toc" src="data:image/gif;base64,R0lGODlhFAAUAJEAAAAAADMzM////wAAACH5BAUUAAIALAAAAAAUABQAAAImlI+py+0PU5gRBRDM3DxbWoXis42X13USOLauUIqnlsaH/eY6UwAAOw==" /></div></div>');
	$('#closeMe').toggle(
		function()
		{
			setCookie('ciNav', true, 365);
			$('#nav2').show();	
			$('#topMenu').remove();	
			$('body').css({ background:'none' });
			$('.wy-nav-content-wrap').css({ background:'none', 'margin-left':0 });
			$('.wy-breadcrumbs').append('<div style="float:right;"><div style="float:left;" id="topMenu">'+$('.wy-form').parent().html()+'</div></div>');$('.wy-nav-side').toggle();	
		},
		function()
		{
			setCookie('ciNav', false, 365);
			$('#topMenu').remove();
			$('#nav').hide();
			$('#nav2').hide();			
			$('body').css({ background:'#edf0f2;' });
			$('.wy-nav-content-wrap').css({ background:'none repeat scroll 0 0 #fcfcfc;', 'margin-left':'300px' });
			$('.wy-nav-side').show();
		}
	);
	if (getCookie('ciNav') == 'true')
	{
		$('#closeMe').trigger('click');
		//$('#nav').slideToggle();
	}
	// END MODIFICATION ---
});

// Rufnex Cookie functions
function setCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname+"="+cvalue+"; "+expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return false;
}
// End

window.SphinxRtdTheme = (function (jquery) {
    var stickyNav = (function () {
        var navBar,
            win,
            stickyNavCssClass = 'stickynav',
            applyStickNav = function () {
                if (navBar.height() <= win.height()) {
                    navBar.addClass(stickyNavCssClass);
                } else {
                    navBar.removeClass(stickyNavCssClass);
                }
            },
            enable = function () {
                applyStickNav();
                win.on('resize', applyStickNav);
            },
            init = function () {
                navBar = jquery('nav.wy-nav-side:first');
                win    = jquery(window);
            };
        jquery(init);
        return {
            enable : enable
        };
    }());
    return {
        StickyNav : stickyNav
    };
}($));
