<?php

namespace Rain\Tpl\Plugin;
require_once __DIR__ . '/../Plugin.php';

class PathReplace extends \Rain\Tpl\Plugin
{
	protected $hooks = array('beforeParse');
	private $tags = array('a', 'img', 'link', 'script', 'input');

	/**
	 * replace the path of image src, link href and a href.
	 * url => template_dir/url
	 * url# => url
	 * http://url => http://url
	 *
	 * @param \ArrayAccess $context
	 */
	public function beforeParse(\ArrayAccess $context){

		// set variables
		$html = $context->code;
		$template_basedir = $context->template_basedir;
		$tags = $this->tags;
		$basecode = "<?php echo static::\$conf['base_url']; ?>";


		// get the template base directory
		$template_directory = $basecode . $context->conf['tpl_dir'] . $context->template_basedir;

		// reduce the path
                $path = str_replace( "://", "@not_replace@", $template_directory );
                $path = preg_replace( "#(/+)#", "/", $path );
                $path = preg_replace( "#(/\./+)#", "/", $path );
                $path = str_replace( "@not_replace@", "://", $path );

                while( preg_match( '#\.\./#', $path ) ){
                    $path = preg_replace('#\w+/\.\./#', '', $path );
                }
                
                

		$exp = $sub = array();

		if( in_array( "img", $tags ) ){
			$exp = array( '/<img(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<img(.*?)src=(?:")([^"]+?)#(?:")/i', '/<img(.*?)src="(.*?)"/', '/<img(.*?)src=(?:\@)([^"]+?)(?:\@)/i' );
			$sub = array( '<img$1src=@$2://$3@', '<img$1src=@$2@', '<img$1src="' . $path . '$2"', '<img$1src="$2"' );
		}

		if( in_array( "script", $tags ) ){
			$exp = array_merge( $exp , array( '/<script(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<script(.*?)src=(?:")([^"]+?)#(?:")/i', '/<script(.*?)src="(.*?)"/', '/<script(.*?)src=(?:\@)([^"]+?)(?:\@)/i' ) );
			$sub = array_merge( $sub , array( '<script$1src=@$2://$3@', '<script$1src=@$2@', '<script$1src="' . $path . '$2"', '<script$1src="$2"' ) );
		}

		if( in_array( "link", $tags ) ){
			$exp = array_merge( $exp , array( '/<link(.*?)href=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<link(.*?)href=(?:")([^"]+?)#(?:")/i', '/<link(.*?)href="(.*?)"/', '/<link(.*?)href=(?:\@)([^"]+?)(?:\@)/i' ) );
			$sub = array_merge( $sub , array( '<link$1href=@$2://$3@', '<link$1href=@$2@' , '<link$1href="' . $path . '$2"', '<link$1href="$2"' ) );
		}

		if( in_array( "a", $tags ) ){
                        $exp = array_merge( $exp , array( '/<a(.*?)href=(?:")(http:\/\/|https:\/\/|javascript:|mailto:|\/|{)([^"]+?)(?:")/i','/<a(.*?)href="(.*?)"/', '/<a(.*?)href=(?:\@)([^"]+?)(?:\@)/i'));
			$sub = array_merge( $sub , array( '<a$1href=@$2$3@', '<a$1href="' . $basecode . '$2"', '<a$1href="$2"' ) );
		}

		if( in_array( "input", $tags ) ){
			$exp = array_merge( $exp , array( '/<input(.*?)src=(?:")(http|https)\:\/\/([^"]+?)(?:")/i', '/<input(.*?)src=(?:")([^"]+?)#(?:")/i', '/<input(.*?)src="(.*?)"/', '/<input(.*?)src=(?:\@)([^"]+?)(?:\@)/i' ) );
			$sub = array_merge( $sub , array( '<input$1src=@$2://$3@', '<input$1src=@$2@', '<input$1src="' . $path . '$2"', '<input$1src="$2"' ) );
		}

		$context->code = preg_replace( $exp, $sub, $html );
	}



	public function setTags($tags) {
		$this->tags = (array) $tags;
		return $this;
	}

}
