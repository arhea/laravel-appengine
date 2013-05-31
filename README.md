## Laravel App Engine
This package modifys and adds functionality to Laravel to get Laravel working on Google App Engine.

## Getting Started

* Install the package
* Comment out the `mcrypt` extension check on line 27-32 of `vendor/laravel/framework/src/Illuminate/Foundation/start.php`
* Comment out the default laravel Encryption service provider in `app/config/app.php` and replace it with `LaravelAppEngine\Encryption\EncryptionServiceProvider'`

## Encryption Service Provider
This is an extension to the Encryption class that changes the implementation to use Open SSL becuase Mcrypt is not yet support by App Engine. I have filed a [bug report](https://code.google.com/p/googleappengine/issues/detail?id=9332&q=language%3DPHP&colspec=ID%20Type%20Component%20Status%20Stars%20Summary%20Language%20Priority%20Owner%20Log) which at the time of writing this has been started and well requested. So please star that issue as I do not like changing core functionality of Laravel.


### License
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

### Author

Alex Rhea
alex.rhea@gmail.com
