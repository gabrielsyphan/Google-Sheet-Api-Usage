<?php

namespace Stonks\Router;

/**
 * Class Router
 *
 * @package Stonks\Router
 */
class Router extends Dispatch
{

	/**
	 * Router constructor.
	 *
	 * @param string $projectUrl
	 * @param string $separator
	 */
	public function __construct(string $projectUrl, string $separator = ':')
	{
		parent::__construct($projectUrl, $separator);
	}

	/**
	 * @param string $route
	 * @param $handler
	 * @param string|null $name
	 * @return void
	 */
	public function get(string $route, $handler, string $name = null): void
	{
		$this->addRoute('GET', $route, $handler, $name);
	}

	/**
	 * @param string $route
	 * @param $handler
	 * @param string|null $name
	 * @return void
	 */
	public function post(string $route, $handler, string $name = null): void
	{
		$this->addRoute('POST', $route, $handler, $name);
	}

	/**
	 * @param string $route
	 * @param $handler
	 * @param string|null $name
	 * @return void
	 */
	public function put(string $route, $handler, string $name = null): void
	{
		$this->addRoute('PUT', $route, $handler, $name);
	}

	/**
	 * @param string $route
	 * @param $handler
	 * @param string|null $name
	 * @return void
	 */
	public function patch(string $route, $handler, string $name = null): void
	{
		$this->addRoute('PATCH', $route, $handler, $name);
	}

	/**
	 * @param string $route
	 * @param $handler
	 * @param string|null $name
	 * @return void
	 */
	public function delete(string $route, $handler, string $name = null): void
	{
		$this->addRoute('DELETE', $route, $handler, $name);
	}
}
