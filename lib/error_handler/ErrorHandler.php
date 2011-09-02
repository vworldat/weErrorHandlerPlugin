<?php

define('E_FATAL', 16384);

class ErrorHandler
{
	public $dispatcher = null;
	
	public static $errorTypes = array(
		E_NOTICE 						=> 'notice',
		E_USER_NOTICE 			=> 'user notice',
		E_WARNING 					=> 'warning',
		E_USER_WARNING 			=> 'user warning',
		E_ERROR 						=> 'error',
		E_USER_ERROR 				=> 'user error',
		E_RECOVERABLE_ERROR => 'recoverable error',
		E_FATAL 						=> 'fatal error',
	);
	
	public function handleError($errNo, $errStr, $errFile, $errLine, $errContext = array())
	{
		$errorData = array(
			'errNo' 			=> $errNo,
			'errStr' 			=> $errStr,
			'errFile' 		=> $errFile,
			'errLine' 		=> $errLine,
			'errContext' 	=> $errContext,
		);
		
		// check if any listeners registered to error_handler.handle_error want to handle the error
		if (!$result = $this->dispatchError($errorData))
		{
			switch ($errNo)
			{
				case E_NOTICE:
					$this->showError('notice', $errorData);
					break;
				case E_USER_NOTICE:
					$this->showError('user notice', $errorData);
					break;
				case E_WARNING:
					$this->showError('warning', $errorData);
					break;
				case E_USER_WARNING:
					$this->showError('user warning', $errorData);
					break;
				case E_ERROR:
					$this->showError('error', $errorData);
					break;
				case E_USER_ERROR:
					$this->showError('user error', $errorData);
					break;
				case E_RECOVERABLE_ERROR:
					$this->showError('recoverable error', $errorData);
					die();
					break;
					
				case E_FATAL:
					die(sprintf('<b>Caught fatal error:</b> %s in %s on line %d',
						$errStr,
						$errFile,
						$errLine
					));
					
				default:
					break;
			}
		}
	}
	
	public function handleShutdown()
	{
		$error = error_get_last();
		if ($error['type'] == 1)
		{
			$this->handleError(E_FATAL, $error['message'], $error['file'], $error['line']);
		}
	}
	
	public function setDispatcher(sfEventDispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}
	
	public function dispatchError(array $errorData)
	{
		if (null !== $this->dispatcher)
		{
			return $this->dispatcher->notifyUntil(new sfEvent('error', 'error_handler.handle_error', $errorData))->isProcessed();
		}
		return false;
	}
	
	public function showError($type, $errorData)
	{
		echo sprintf('<br /><b>Caught %s:</b> %s in %s on line %d<br /><br />',
			$type,
			$errorData['errStr'],
			$errorData['errFile'],
			$errorData['errLine']
		);
	}
	
	/**
	 * Use this to raise a general ErrorHandlerException on any caught error.
	 *
	 * @param sfEvent $event
	 *
	 * @throws ErrorHandlerException
	 */
	public function handleAsException(sfEvent $event)
	{
		$errorData = $event->getParameters();
		
		$errorType = self::$errorTypes[$errorData['errNo']];
		
		// remove html tags from error text
		$errorData['errStr'] = preg_replace('/ \[\<.*\>\]/', '', $errorData['errStr']);
		
		// thx to sfInflector::camelize()
		$tmp = $errorType;
		$tmp = sfToolkit::pregtr($tmp, array('#/(.?)#e'		=> "'::'.strtoupper('\\1')",
																				 '/(^| |-)+(.)/e' => "strtoupper('\\2')"));
		
		$class = sprintf('ErrorHandler%sException', $tmp);
		
		throw new $class(sprintf('Error handler caught %s: %s in %s on line %d', $errType, $errorData['errStr'], $errorData['errFile'], $errorData['errLine']));
	}
}
