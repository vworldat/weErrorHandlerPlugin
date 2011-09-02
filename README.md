weErrorHandlerPlugin
====================

weErrorHandlerPlugin provides a custom error handler that provides symfony events for all PHP errors, warnings and notices.

This can be used to listen for particular errors or warnings and transform them to Exceptions or just drop them. This library has been heavily used
to fix the completely f***ed up warnings and notices in the php_ldap extension.

When the config value `global_error_handler_force_exceptions` is set to true (you can use vworldat/weGlobalConfigPlugin for this), all errors, 
warnings and notices will throw Exceptions (e.g. `ErrorHandlerNoticeException`). This is extremely useful to debug nasty notices and warnings using the symfony stack trace.


Requirements
------------

- symfony 1.3 oder 1.4 (could work with previous versions too, untested)

Installation
------------

 * Install plugin in `/plugins/weErrorHandlerPlugin` using GIT, SVN or whatever you like
 * Enable plugin in `/config/ProjectConfiguration.class.php`

``` php
<?php

class ProjectConfiguration extends sfProjectConfiguration
{
	public function setup()
	{
		...
		$this->enablePlugins('weErrorHandlerPlugin');
		...
	}
}
```

Usage
-----

You can listen for errors using the symfony event dispatcher like this:

``` php
$dispatcher->connect('error_handler.handle_error', {your callable goes here});
```

The event is created using notifyUntil(), so this is an event chain. If any of your callables handles the event
successfully, return true to stop event propagation.

Making everything an exception
------------------------------

It's most PHP developer's dream: no more warnings and notices, just exceptions! Can you handle them? Yes you can!

``` yml
[global.yml]
all:
  error_handler:
    force_exceptions: true
```

If you don't want to use vworldat/weGlobalConfigPlugin, you can set the value in your project configuration:

``` php
<?php

class ProjectConfiguration extends sfProjectConfiguration
{
	public function setup()
	{
		...
		$this->enablePlugins('weGlobalConfigPlugin');
		sfConfig::set('global_error_handler_force_exceptions', true);
		...
	}
}
```

*You shouldn't use this feature in production if you're not absolutely sure your application is notice free!*
