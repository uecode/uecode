Underground Elephant Library
======

1. To Install
	
	```sh
	composer require uecode/uecode dev/master
	```

2. To Use, after autoloading

	```php
	// Saves the dump of the $variable var, going down four layers, into $save
	$save = \Uecode::dump( $variable, 4, false, true );

	// Dumps the $variable var, going down three layers, and then dies
	\Uecode::dump( $variable, 3, true );
	```

Contributing
=====

If you want to contribute, just fork your own repo, and make your changes, then submit a pull request.


Looking for a job?
=====
Email Aaron Scherer at aaron@undergroundelephant.com


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/uecode/uecode/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

