<?php

class Security_test extends CI_TestCase {

	public function set_up()
	{
		// Set cookie for security test
		$_COOKIE['ci_csrf_cookie'] = md5(uniqid(mt_rand(), TRUE));

		// Set config for Security class
		$this->ci_set_config('csrf_protection', TRUE);
		$this->ci_set_config('csrf_token_name', 'ci_csrf_token');
		$this->ci_set_config('csrf_cookie_name', 'ci_csrf_cookie');

		$this->security = new Mock_Core_Security();
	}

	// --------------------------------------------------------------------

	public function test_csrf_verify()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->assertInstanceOf('CI_Security', $this->security->csrf_verify());
	}

	// --------------------------------------------------------------------

	public function test_csrf_verify_invalid()
	{
		// Without issuing $_POST[csrf_token_name], this request will triggering CSRF error
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->setExpectedException('RuntimeException', 'CI Error: The action you have requested is not allowed');

		$this->security->csrf_verify();
	}

	// --------------------------------------------------------------------

	public function test_csrf_verify_valid()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST[$this->security->csrf_token_name] = $this->security->csrf_hash;

		$this->assertInstanceOf('CI_Security', $this->security->csrf_verify());
	}

	// --------------------------------------------------------------------

	public function test_get_csrf_hash()
	{
		$this->assertEquals($this->security->csrf_hash, $this->security->get_csrf_hash());
	}

	// --------------------------------------------------------------------

	public function test_get_csrf_token_name()
	{
		$this->assertEquals('ci_csrf_token', $this->security->get_csrf_token_name());
	}

	// --------------------------------------------------------------------

	public function test_xss_clean()
	{
		$harm_string = "Hello, i try to <script>alert('Hack');</script> your site";

		$harmless_string = $this->security->xss_clean($harm_string);

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_string);
	}
	
        // --------------------------------------------------------------------
	
	public function testJavaScriptCleaning()
	{
		// http://cpansearch.perl.org/src/KURIANJA/HTML-Defang-1.02/t/02_xss.t
		
		$testArray = array(
			"<img FSCommand=\"someFunction()\">",
			"<img onAbort=\"someFunction()\">",
			"<img onActivate=\"someFunction()\">",
			"<img onAfterPrint=\"someFunction()\">",
			"<img onAfterUpdate=\"someFunction()\">",
			"<img onBeforeActivate=\"someFunction()\">",
			"<img onBeforeCopy=\"someFunction()\">",
			"<img onBeforeCut=\"someFunction()\">",
			"<img onBeforeDeactivate=\"someFunction()\">",
			"<img onBeforeEditFocus=\"someFunction()\">",
			"<img onBeforePaste=\"someFunction()\">",
			"<img onBeforePrint=\"someFunction()\">",
			"<img onBeforeUnload=\"someFunction()\">",
			"<img onBegin=\"someFunction()\">",
			"<img onBlur=\"someFunction()\">",
			"<img onBounce=\"someFunction()\">",
			"<img onCellChange=\"someFunction()\">",
			"<img onChange=\"someFunction()\">",
			"<img onClick=\"someFunction()\">",
			"<img onContextMenu=\"someFunction()\">",
			"<img onControlSelect=\"someFunction()\">",
			"<img onCopy=\"someFunction()\">",
			"<img onCut=\"someFunction()\">",
			"<img onDataAvailable=\"someFunction()\">",
			"<img onDataSetChanged=\"someFunction()\">",
			"<img onDataSetComplete=\"someFunction()\">",
			"<img onDblClick=\"someFunction()\">",
			"<img onDeactivate=\"someFunction()\">",
			"<img onDrag=\"someFunction()\">",
			"<img onDragEnd=\"someFunction()\">",
			"<img onDragLeave=\"someFunction()\">",
			"<img onDragEnter=\"someFunction()\">",
			"<img onDragOver=\"someFunction()\">",
			"<img onDragDrop=\"someFunction()\">",
			"<img onDrop=\"someFunction()\">",
			"<img onEnd=\"someFunction()\">",
			"<img onError=\"someFunction()\">",
			"<img onErrorUpdate=\"someFunction()\">",
			"<img onFilterChange=\"someFunction()\">",
			"<img onFinish=\"someFunction()\">",
			"<img onFocus=\"someFunction()\">",
			"<img onFocusIn=\"someFunction()\">",
			"<img onFocusOut=\"someFunction()\">",
			"<img onHelp=\"someFunction()\">",
			"<img onKeyDown=\"someFunction()\">",
			"<img onKeyPress=\"someFunction()\">",
			"<img onKeyUp=\"someFunction()\">",
			"<img onLayoutComplete=\"someFunction()\">",
			"<img onLoad=\"someFunction()\">",
			"<img onLoseCapture=\"someFunction()\">",
			"<img onMediaComplete=\"someFunction()\">",
			"<img onMediaError=\"someFunction()\">",
			"<img onMouseDown=\"someFunction()\">",
			"<img onMouseEnter=\"someFunction()\">",
			"<img onMouseLeave=\"someFunction()\">",
			"<img onMouseMove=\"someFunction()\">",
			"<img onMouseOut=\"someFunction()\">",
			"<img onMouseOver=\"someFunction()\">",
			"<img onMouseUp=\"someFunction()\">",
			"<img onMouseWheel=\"someFunction()\">",
			"<img onMove=\"someFunction()\">",
			"<img onMoveEnd=\"someFunction()\">",
			"<img onMoveStart=\"someFunction()\">",
			"<img onOutOfSync=\"someFunction()\">",
			"<img onPaste=\"someFunction()\">",
			"<img onPause=\"someFunction()\">",
			"<img onProgress=\"someFunction()\">",
			"<img onPropertyChange=\"someFunction()\">",
			"<img onReadyStateChange=\"someFunction()\">",
			"<img onRepeat=\"someFunction()\">",
			"<img onReset=\"someFunction()\">",
			"<img onResize=\"someFunction()\">",
			"<img onResizeEnd=\"someFunction()\">",
			"<img onResizeStart=\"someFunction()\">",
			"<img onResume=\"someFunction()\">",
			"<img onReverse=\"someFunction()\">",
			"<img onRowsEnter=\"someFunction()\">",
			"<img onRowExit=\"someFunction()\">",
			"<img onRowDelete=\"someFunction()\">",
			"<img onRowInserted=\"someFunction()\">",
			"<img onScroll=\"someFunction()\">",
			"<img onSeek=\"someFunction()\">",
			"<img onSelect=\"someFunction()\">",
			"<img onSelectionChange=\"someFunction()\">",
			"<img onSelectStart=\"someFunction()\">",
			"<img onStart=\"someFunction()\">",
			"<img onStop=\"someFunction()\">",
			"<img onSyncRestored=\"someFunction()\">",
			"<img onSubmit=\"someFunction()\">",
			"<img onTimeError=\"someFunction()\">",
			"<img onTrackChange=\"someFunction()\">",
			"<img onUnload=\"someFunction()\">",
			"<img onURLFlip=\"someFunction()\">",
			"<img seekSegmentTime=\"someFunction()\">",
		);
	
		foreach ($testArray as $test) {
	      		$this->assertEquals("<img >", $this->security->xss_clean($test));
	    	}
	}

        // --------------------------------------------------------------------

	public function test_xss_clean_string_array()
	{
		$harm_strings = array(
			"Hello, i try to <script>alert('Hack');</script> your site",
			"Simple clean string",
			"Hello, i try to <script>alert('Hack');</script> your site"
		);

		$harmless_strings = $this->security->xss_clean($harm_strings);

		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_strings[0]);
		$this->assertEquals("Simple clean string", $harmless_strings[1]);
		$this->assertEquals("Hello, i try to [removed]alert&#40;'Hack'&#41;;[removed] your site", $harmless_strings[2]);
	}

	// --------------------------------------------------------------------
	
	public function testXssClean()
	{
		$testArray = array(
			"onAttribute=\"bar\"" => "\"bar\"",
			"<BGSOUND SRC=\"javascript:alert('XSS');\">" => "&lt;BGSOUND SRC=\"alert&#40;'XSS'&#41;;\"&gt;", // BGSOUND
			"<BR SIZE=\"&{alert('XSS')}\">" => "<BR SIZE=\"&{alert&#40;'XSS'&#41;}\">", // & JavaScript includes
			"<LINK REL=\"stylesheet\" HREF=\"javascript:alert('XSS');\">" => "&lt;LINK REL=\"stylesheet\" HREF=\"alert&#40;'XSS'&#41;;\"&gt;", // STYLE sheet
			"<STYLE>BODY{-moz-binding:url(\"http://ha.ckers.org/xssmoz.xml#xss\")}</STYLE>" => "&lt;STYLE&gt;BODY{:url(\"http://ha.ckers.org/xssmoz.xml#xss\")}&lt;/STYLE&gt;", // Remote style sheet
			"<STYLE>@im\\port'\\ja\vasc\ript:alert(\"XSS\")';</STYLE>" => "&lt;STYLE&gt;@im\port'\jaasc\ript:alert&#40;\"XSS\"&#41;';&lt;/STYLE&gt;", // STYLE tags with broken up JavaScript for XSS
			"<XSS STYLE=\"xss:expression_r(alert('XSS'))\">" => "", // Anonymous HTML with STYLE attribute
			"<XSS STYLE=\"behavior: url(xss.htc);\">" => "", // Local htc file
			"¼script¾alert(¢XSS¢)¼/script¾" => "¼script¾alert&#40;¢XSS¢&#41;¼/script¾", // US-ASCII encoding
			"<IMG defang_SRC=javascript:alert\(&quot;XSS&quot;\)>" => "<IMG >", // IMG
			"<IMG SRC=javascript:alert(&quot;XSS&quot;)>" => "<IMG >",
			"<IMG SRC=&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;>" => "<IMG >",
			"<IMG SRC=&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041>" => "<IMG >",
			"<IMG SRC=&#x6A&#x61&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29>" => "<IMG >",
			"<IMG SRC=\"jav	ascript:alert('XSS');\">" => "<IMG >",
			"<IMG SRC=\"jav&#x09;ascript:alert('XSS');\">" => "<IMG >",
			"<IMG SRC=\"jav&#x0A;ascript:alert('XSS');\">" => "<IMG >",
			"<IMG SRC\n=\n\"\nj\na\nv\n&#x0A;a\ns\nc\nr\ni\np\nt\n:\na\nl\ne\nr\nt\n(\n'\nX\nS\nS\n'\n)\n;\">" => "<IMG SRC\n=\n\"\n\nalert\n&#40;\n'\nX\nS\nS\n'\n&#41;\n;\">",
			"<IMG SRC=java�script:alert('XSS')>" => "<IMG >",
			"<DIV STYLE=\"background-image:\\0075\\0072\\006C\\0028'\\006a\\0061\\0076\\0061\\0073\\0063\\0072\\0069\\0070\\0074\\003a\\0061\\006c\\0065\\0072\\0074\\0028\\0027\\0058\\0053\\0053\\0027\\0029'\\0029\">" => "<DIV >",
			"<STYLE>.XSS{background-image:url(\"javascript:alert('XSS')\");}</STYLE><A CLASS=XSS></A>" => "&lt;STYLE&gt;.XSS{background-image:url(\"alert&#40;'XSS'&#41;\");}&lt;/STYLE&gt;&lt;A ></A>",
			"<META HTTP-EQUIV=\"refresh\" CONTENT=\"0;url=javascript:alert('XSS');\">" => "&lt;META HTTP-EQUIV=\"refresh\" CONTENT=\"0;url=alert&#40;'XSS'&#41;;\"&gt;", // META
			"<IFRAME SRC=\"javascript:alert('XSS');\"></IFRAME>" => "&lt;IFRAME SRC=\"alert&#40;'XSS'&#41;;\"&gt;&lt;/IFRAME>", // IFRAME
			"<applet code=A21 width=256 height=256 archive=\"toir.jar\"></applet>" => "&lt;applet code=A21 width=256 height=256 archive=\"toir.jar\"&gt;&lt;/applet>",
			"<script Language=\"JavaScript\" event=\"FSCommand (command, args)\" for=\"theMovie\">...</script>" => "...", // <script>
			"<SCRIPT>document.write(\"<SCRI\");</SCRIPT>PT SRC=\"http://ha.ckers.org/xss.js\"></SCRIPT>" => "(\"<SCRI\");PT SRC=\"http://ha.ckers.org/xss.js\">", // XSS using HTML quote encapsulation
			"<SCR�IPT>alert(\"XSS\")</SCR�IPT>" => "alert&#40;\"XSS\"&#41;",
			"Би шил идэй чадна,<STYLE>li {list-style-image: url(\"javascript:alert('XSS')\");}</STYLE><UL><LI>我能吞下玻璃而不傷身體</br>" => "Би шил идэй чадна,&lt;STYLE&gt;li {list-style-image: url(\"alert&#40;'XSS'&#41;\");}&lt;/STYLE&gt;&lt;UL><LI>我能吞下玻璃而不傷身體</br>",
			"';alert(String.fromCharCode(88,83,83))//';alert(String.fromCharCode(88,83,83))//\"\; alert(String.fromCharCode(88,83,83))//\"\;alert(String.fromCharCode(88,83,83))//--></SCRIPT>\">'><SCRIPT>alert(String.fromCharCode(88,83,83))</SCRIPT>" => "';alert&#40;String.fromCharCode(88,83,83&#41;)//';alert&#40;String.fromCharCode(88,83,83&#41;)//\"\\; alert&#40;String.fromCharCode(88,83,83&#41;)//\"\\;alert&#40;String.fromCharCode(88,83,83&#41;)//--&gt;\">'>alert&#40;String.fromCharCode(88,83,83&#41;)",
			"म काँच खान सक्छू र मलाई केहि नी हुन्‍न् <IMG SRC=javascript:alert(String.fromCharCode(88,83,83))>।" => "म काँच खान सक्छू र मलाई केहि नी हुन्‍न् <IMG >।",
		);
		
		foreach ($testArray as $before => $after) {
			$this->assertEquals($after, $this->security->xss_clean($before));
		}
	}

	public function test_xss_clean_image_valid()
	{
		$harm_string = '<img src="test.png">';

		$xss_clean_return = $this->security->xss_clean($harm_string, TRUE);

		$this->assertTrue($xss_clean_return);
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_image_invalid()
	{
		$harm_string = '<img src=javascript:alert(String.fromCharCode(88,83,83))>';

		$xss_clean_return = $this->security->xss_clean($harm_string, TRUE);

		$this->assertFalse($xss_clean_return);
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_entity_double_encoded()
	{
		$input = '<a href="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere</a>';
		$this->assertEquals('<a >Clickhere</a>', $this->security->xss_clean($input));
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_js_img_removal()
	{
		$input = '<img src="&#38&#35&#49&#48&#54&#38&#35&#57&#55&#38&#35&#49&#49&#56&#38&#35&#57&#55&#38&#35&#49&#49&#53&#38&#35&#57&#57&#38&#35&#49&#49&#52&#38&#35&#49&#48&#53&#38&#35&#49&#49&#50&#38&#35&#49&#49&#54&#38&#35&#53&#56&#38&#35&#57&#57&#38&#35&#49&#49&#49&#38&#35&#49&#49&#48&#38&#35&#49&#48&#50&#38&#35&#49&#48&#53&#38&#35&#49&#49&#52&#38&#35&#49&#48&#57&#38&#35&#52&#48&#38&#35&#52&#57&#38&#35&#52&#49">Clickhere';
		$this->assertEquals('<img >', $this->security->xss_clean($input));
	}

	// --------------------------------------------------------------------

	public function test_xss_clean_sanitize_naughty_html()
	{
		$input = '<blink>';
		$this->assertEquals('&lt;blink&gt;', $this->security->xss_clean($input));
	}

	// --------------------------------------------------------------------

	public function test_remove_evil_attributes()
	{
		$this->assertEquals('<foo [removed]>', $this->security->remove_evil_attributes('<foo onAttribute="bar">', false));
		$this->assertEquals('<foo [removed]>', $this->security->remove_evil_attributes('<foo onAttributeNoQuotes=bar>', false));
		$this->assertEquals('<foo [removed]>', $this->security->remove_evil_attributes('<foo onAttributeWithSpaces = bar>', false));
		$this->assertEquals('<foo prefixOnAttribute="bar">', $this->security->remove_evil_attributes('<foo prefixOnAttribute="bar">', false));
		$this->assertEquals('<foo>onOutsideOfTag=test</foo>', $this->security->remove_evil_attributes('<foo>onOutsideOfTag=test</foo>', false));
		$this->assertEquals('onNoTagAtAll = true', $this->security->remove_evil_attributes('onNoTagAtAll = true', false));
	}

	// --------------------------------------------------------------------

	public function test_xss_hash()
	{
		$this->assertEmpty($this->security->xss_hash);

		// Perform hash
		$this->security->xss_hash();

		$this->assertTrue(preg_match('#^[0-9a-f]{32}$#iS', $this->security->xss_hash) === 1);
	}

	// --------------------------------------------------------------------

	public function test_get_random_bytes()
	{
		$length = "invalid";
		$this->assertFalse($this->security->get_random_bytes($length));

		$length = 10;
		$this->assertNotEmpty($this->security->get_random_bytes($length));
	}

	// --------------------------------------------------------------------

	public function test_entity_decode()
	{
		$encoded = '&lt;div&gt;Hello &lt;b&gt;Booya&lt;/b&gt;&lt;/div&gt;';
		$decoded = $this->security->entity_decode($encoded);

		$this->assertEquals('<div>Hello <b>Booya</b></div>', $decoded);

		// Issue #3057 (https://github.com/bcit-ci/CodeIgniter/issues/3057)
		$this->assertEquals(
			'&foo should not include a semicolon',
			$this->security->entity_decode('&foo should not include a semicolon')
		);
	}

	// --------------------------------------------------------------------

	public function test_sanitize_filename()
	{
		$filename = './<!--foo-->';
		$safe_filename = $this->security->sanitize_filename($filename);

		$this->assertEquals('foo', $safe_filename);
	}

	// --------------------------------------------------------------------

	public function test_strip_image_tags()
	{
		$imgtags = Array(
			'<img src="smiley.gif" alt="Smiley face" height="42" width="42">',
			'<img alt="Smiley face" height="42" width="42" src="smiley.gif">',
			'<img src="http://www.w3schools.com/images/w3schools_green.jpg">',
			'<img src="/img/sunset.gif" height="100%" width="100%">',
			'<img src="mdn-logo-sm.png" alt="MD Logo" srcset="mdn-logo-HD.png 2x, mdn-logo-small.png 15w, mdn-banner-HD.png 100w 2x" />',
			'<img sqrc="/img/sunset.gif" height="100%" width="100%">',
			'<img srqc="/img/sunset.gif" height="100%" width="100%">',
			'<img srcq="/img/sunset.gif" height="100%" width="100%">'
		);

		$urls = Array(
			'smiley.gif',
			'smiley.gif',
			'http://www.w3schools.com/images/w3schools_green.jpg',
			'/img/sunset.gif',
			'mdn-logo-sm.png',
			'<img sqrc="/img/sunset.gif" height="100%" width="100%">',
			'<img srqc="/img/sunset.gif" height="100%" width="100%">',
			'<img srcq="/img/sunset.gif" height="100%" width="100%">'
		);

		for($i = 0; $i < count($imgtags); $i++) 
		{
			$this->assertEquals($urls[$i], $this->security->strip_image_tags($imgtags[$i]));
		}
	}

	// --------------------------------------------------------------------

	public function test_csrf_set_hash()
	{
		// Set cookie for security test
		$_COOKIE['ci_csrf_cookie'] = md5(uniqid(mt_rand(), TRUE));

		// Set config for Security class
		$this->ci_set_config('csrf_protection', TRUE);
		$this->ci_set_config('csrf_token_name', 'ci_csrf_token');

		// leave csrf_cookie_name as blank to test _csrf_set_hash function
		$this->ci_set_config('csrf_cookie_name', '');

		$this->security = new Mock_Core_Security();

		$this->assertNotEmpty($this->security->get_csrf_hash());
	}
}
