<?php

namespace mako\http\routing;

use \Closure;
use \mako\http\Request;
use \mako\http\Response;
use \mako\http\routing\Route;
use \RuntimeException;

/**
 * Route.
 * 
 * @author     Frederic G. Østby
 * @copyright  (c) 2008-2013 Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

class Dispatcher
{
	//---------------------------------------------
	// Class properties
	//---------------------------------------------

	/**
	 * Request.
	 * 
	 * @var \mako\http\Request
	 */

	protected $request;

	/**
	 * Route.
	 * 
	 * @var \mako\http\routing\Route
	 */

	protected $route;

	//---------------------------------------------
	// Class constructor, destructor etc ...
	//---------------------------------------------

	/**
	 * Constructor.
	 * 
	 * @access  public
	 * @param   \mako\http\Reqeust        $request  Request
	 * @param   \mako\http\routing\Route  $route    Route
	 */

	public function __construct(Request $request, Route $route)
	{
		$this->request = $request;
		$this->route   = $route;
	}

	//---------------------------------------------
	// Class methods
	//---------------------------------------------

	/**
	 * Executes before filters.
	 * 
	 * @access  protected
	 */

	protected function beforeFilters()
	{

	}

	/**
	 * Executes after filters.
	 * 
	 * @access  protected
	 */

	protected function afterFilters()
	{

	}

	/**
	 * Dispatch a closure controller action.
	 * 
	 * @access  protected
	 * @param   \mako\http\Response  $response  Response
	 * @param   \Closure             $closure   Closure  
	 */

	protected function dispatchClosure(Response $response, Closure $closure)
	{
		$response->body(call_user_func_array($closure, array_merge(array($this->request, $response), $this->route->getParameters())));
	}

	/**
	 * Dispatch a controller action.
	 * 
	 * @access  protected
	 * @param   \mako\http\Response  $response    Response
	 * @param   string               $controller  Controller
	 */

	protected function dispatchController(Response $response, $controller)
	{
		list($controller, $method) = explode('::', $controller, 2);

		$controller = new $controller($this->request, $response);

		// Check that the controller extends the base controller

		if(!($controller instanceof \mako\http\routing\Controller))
		{
			throw new RuntimeException(vsprintf("%s(): All controllers must extend mako\http\routing\Controller.", array(__METHOD__)));
		}

		// Execute the before filter, the controller action and finally the after filter

		$controller->beforeFilter();

		$response->body(call_user_func_array(array($controller, $method), $this->route->getParameters()));

		$controller->afterFilter();
	}

	/**
	 * Dispatches the route and returns the response.
	 * 
	 * @access  public
	 * @return  \mako\http\Response
	 */

	public function dispatch()
	{
		$response = new Response();

		// Execute before filters

		$this->beforeFilters();

		// Execute the route action

		$action = $this->route->getAction();

		if($action instanceof Closure)
		{
			$this->dispatchClosure($response, $action);
		}
		else
		{
			$this->dispatchController($response, $action);
		}

		// Execute after filters

		$this->afterFilters();

		// Return the response

		return $response;
	}
}

/** -------------------- End of file -------------------- **/