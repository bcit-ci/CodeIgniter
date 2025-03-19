Custom Authentication
=====================
Custom authentication handlers are designed to be extremely simple to write.
In order to write a handler, you'll need to implement the `Requests_Auth`
interface.

An instance of this handler is then passed in by the user via the `auth`
option, just like for normal authentication.

Let's say we have a HTTP endpoint that checks for the `Hotdog` header and
authenticates you if said header is set to `Yummy`. (I don't know of any
services that do this; perhaps this is a market waiting to be tapped?)

```php
class MySoftware_Auth_Hotdog implements Requests_Auth {
	protected $password;

	public function __construct($password) {
		$this->password = $password;
	}

	public function register(Requests_Hooks &$hooks) {
		$hooks->register('requests.before_request', array(&$this, 'before_request'));
	}

	public function before_request(&$url, &$headers, &$data, &$type, &$options) {
		$headers['Hotdog'] = $this->password;
	}
}
```

We then use this in our request calls:

```
$options = array(
	'auth' => new MySoftware_Auth_Hotdog('yummy')
);
$response = Requests::get('http://hotdogbin.org/admin', array(), $options);
```

(For more information on how to register and use hooks, see the [hooking
system documentation][hooks])

[hooks]: hooks.md