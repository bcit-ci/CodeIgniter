{extends "page.tpl"}

{block "title"}
Welcome to Dwoo-ed CodeIgniter
{/block}

{block "content"}
<h1>Welcome to Dwoo-ed CodeIgniter!</h1>

<p>The page you are looking at is being generated dynamically by <b>CodeIgniter</b> in combination with the 'Smarty-killer' <b>Dwoo</b> template engine.
The page is rendered at {$itshowlate} by the Dwoo_compiler.</p>

<p>If you would like to edit this page you'll find it located at:</p>
<code>application/views/dwoowelcome.tpl</code>

<p>The corresponding controller for this page is found at:</p>
<code>application/controllers/dwoowelcome.php</code>

<p>The library for Dwoo integration can be found at:</p>
<code>application/libraries/Dwootemplate.php</code>

<p>If you are exploring Dwoo for the very first time, you should start by reading the {anchor uri='http://dwoo.org/' title='Dwoo website'}.</p>
<p>If you are exploring CodeIgniter for the very first time, you should start by reading the {anchor uri='http://codeigniter.com/user_guide/' title='User Guide'}.</p>

<pre>
<b>Usage</b>:
$this->load->library('Dwootemplate');
$this->dwootemplate->assign('test', 'test');
$this->dwootemplate->display('dwoowelcome.tpl');
</pre>
{/block}