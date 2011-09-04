<?php

$this->dispatcher->connect('context.load_factories', function(sfEvent $event)
{
	$errorHandler = new ErrorHandler();
	$dispatcher = $event->getSubject()->getEventDispatcher();
	
	$errorHandler->setDispatcher($dispatcher);
	set_error_handler(array($errorHandler, 'handleError'), E_ALL | E_STRICT);
	register_shutdown_function(array($errorHandler, 'handleShutdown'));
	
	if (sfConfig::get('global_error_handler_force_exceptions', false)) 
	{
		$dispatcher->connect('error_handler.handle_error', array($errorHandler, 'handleAsException'));
	} 
	else 
	{
		$dispatcher->connect('config.global_configuration_loaded', function(sfEvent $event) use ($dispatcher, $errorHandler)
		{
			if (sfConfig::get('global_error_handler_force_exceptions', false))
			{
				$dispatcher->connect('error_handler.handle_error', array($errorHandler, 'handleAsException'));
			}
		});
	}
});
