<?php

/**
 * Exception thrown by ErrorHandler class.
 *
 * @author david
 */
class ErrorHandlerException extends Exception
{
	
}

class ErrorHandlerNoticeException extends ErrorHandlerException {}
class ErrorHandlerUserNoticeException extends ErrorHandlerException {}
class ErrorHandlerWarningException extends ErrorHandlerException {}
class ErrorHandlerUserWarningException extends ErrorHandlerException {}
class ErrorHandlerErrorException extends ErrorHandlerException {}
class ErrorHandlerUserErrorException extends ErrorHandlerException {}
class ErrorHandlerRecoverableErrorException extends ErrorHandlerException {}
class ErrorHandlerFatalException extends ErrorHandlerException {}
