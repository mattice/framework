<?php

namespace mako\core\errorhandler\handlers;

use \Exception;
use \mako\view\View;
use \mako\http\Response;

/**
 * RequestException handler.
 *
 * @author     Frederic G. Østby
 * @copyright  (c) 2008-2013 Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

class RequestExceptionHandler
{
	//---------------------------------------------
	// Class properties
	//---------------------------------------------

	/**
	 * Exception.
	 * 
	 * @var Exception
	 */

	protected $exception;

	//---------------------------------------------
	// Class constructor, destructor etc ...
	//---------------------------------------------

	/**
	 * Constructor.
	 * 
	 * @access  public
	 * @param   Exception  $exception  Exception to handle
	 */

	public function __construct(Exception $exception)
	{
		$this->exception = $exception;
	}

	//---------------------------------------------
	// Class methods
	//---------------------------------------------

	/**
	 * Handles the exception.
	 * 
	 * @access  public
	 */

	public function handle()
	{
		Response::factory(new View('_mako_.errors.' . $this->exception->getCode()))->send($this->exception->getCode());
	}
}

/** -------------------- End of file -------------------- **/