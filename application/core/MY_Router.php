<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends CI_Router {
    /**
     * This version of _parse_routes acts the same as the older version
     *     except that it now allows for callbacks to be used as an alternative
     *     to the original route styles. Backreferences are set to the
     *     parameters of the callback. Note: Remember to give default values to
     *     the parameters that can be empty.
     */
    function _parse_routes()
    {
        // Turn the segment array into a URI string
        $uri = implode('/', $this->uri->segments);

        // Is there a literal match?  If so we're done
        if (isset($this->routes[$uri]))
        {
            return $this->_set_request(explode('/', $this->routes[$uri]));
        }

        // Loop through the route array looking for wild-cards
        foreach ($this->routes as $key => $val)
        {
            // Convert wild-cards to RegEx
            $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

            // Does the RegEx match?
            if (preg_match('#^'.$key.'$#', $uri))
            {
                // Are we using a callback?
                $callable = is_callable($val);

                // Determine the appropriate preg_replace to use.
                $preg_replace_type = $callable? 'preg_replace_callback': 'preg_replace';

                // Are we using callbacks to process the matches?
                if($callable){
                    $val = function($matches)use($val){
                        // Remove the string we are matching against from the matches array.
                        array_shift($matches);

                        // Distribute the matches to the arguments of the user's callback.
                        return call_user_func_array($val, $matches);
                    };
                }

                // Do we have a back-reference?
                if ($callable OR (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE))
                {
                    $val = call_user_func($preg_replace_type, '#^'.$key.'$#', $val, $uri);
                }

                return $this->_set_request(explode('/', $val));
            }
        }

        // If we got this far it means we didn't encounter a
        // matching route so we'll set the site default route
        $this->_set_request($this->uri->segments);
    }
}