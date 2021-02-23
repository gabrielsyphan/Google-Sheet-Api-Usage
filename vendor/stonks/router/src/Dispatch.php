<?php

namespace Stonks\Router;

/**
 * Class Dispatch
 *
 * @package Stonks\Router
 */
class Dispatch
{

	use RouterTrait;

	/**
	 * @var array|null
	 */
	private $route;

	/**
	 * @var string
	 */
	private $projectUrl;

	/**
	 * @var string
	 */
	private $separator;

	/**
	 * @var string|null
	 */
	private $namespace;

	/**
	 * @var string|null
	 */
	private $group;

	/**
	 * @var array|null
	 */
	private $data;

	/**
	 * @var int
	 */
	private $error;

	/**
	 * @const int
	 */
	public const BAD_REQUEST = 400;

	/**
	 * @const int
	 */
	public const NOT_FOUND = 404;

	/**
	 * @const int
	 */
	public const METHOD_NOT_ALLOWED = 405;

	/**
	 * @const int
	 */
	public const NOT_IMPLEMENTED = 501;

	/**
	 * Dispatch constructor.
	 *
	 * @param string $projectUrl
	 * @param string $separator
	 */
	public function __construct(string $projectUrl, string $separator = ':')
	{
		$this->projectUrl = (substr($projectUrl, -1) === '/' ? substr($projectUrl, 0, '-1') : $projectUrl);
		$this->separator = ($separator ?? ':');
		$this->httpMethod = $_SERVER['REQUEST_METHOD'];
		$this->patch = (filter_input(INPUT_GET, 'route', FILTER_DEFAULT) ?? '/');
	}

	/**
	 * @return array|null
	 */
	public function __debugInfo(): ?array
	{
		return $this->routes;
	}

	/**
	 * @param string|null $namespace
	 * @return $this
	 */
	public function namespace(?string $namespace): Dispatch
	{
		$this->namespace = ($namespace ? ucwords($namespace) : null);
		return $this;
	}

	/**
	 * @param string|null $group
	 * @return $this
	 */
	public function group(?string $group): Dispatch
	{
		$this->group = ($group ? str_replace('/', '', $group) : null);
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function data(): ?array
	{
		return $this->data;
	}

	/**
	 * @return int|null
	 */
	public function error(): ?int
	{
		return $this->error;
	}

	/**
	 * @return bool
	 */
	public function dispatch(): bool
	{
		if (empty($this->routes) || empty($this->routes[$this->httpMethod])) {
			$this->error = self::NOT_IMPLEMENTED;
			return false;
		}

		$this->route = null;

		foreach ($this->routes[$this->httpMethod] as $key => $route) {
			if (preg_match("~^{$key}$~", $this->patch, $found)) {
				$this->route = $route;
			}
		}

		return $this->execute();
	}

	/**
	 * @return bool
	 */
	private function execute(): bool
	{
		if ($this->route) {
			if (is_callable($this->route['handler'])) {
				call_user_func($this->route['handler'], ($this->route['data'] ?? []));
				return true;
			}

			$controller = $this->route['handler'];

			if (class_exists($controller)) {
				$method = $this->route['action'];
				$newController = new $controller($this);

				if (method_exists($controller, $method)) {
					$newController->$method(($this->route['data'] ?? []));
					return true;
				}

				$this->error = self::METHOD_NOT_ALLOWED;
				return false;
			}

			$this->error = self::BAD_REQUEST;
			return false;
		}

		$this->error = self::NOT_FOUND;
		return false;
	}

	/**
	 * @return void
	 */
	private function formSpoofing(): void
	{
		$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

		if (!empty($post['_method']) && in_array($post['_method'], ['PUT', 'PATCH', 'DELETE'])) {
			$this->httpMethod = $post['_method'];
			$this->data = $post;

			unset($this->data['_method']);
			return;
		}

		if ($this->httpMethod === 'POST') {
			$this->data = $post;

			unset($this->data['_method']);
			return;
		}

		if (in_array($this->httpMethod, ['PUT', 'PATCH', 'DELETE']) && !empty($_SERVER['CONTENT_LENGTH'])) {
			parse_str(file_get_contents('php://input', false, null, 0, $_SERVER['CONTENT_LENGTH']), $this->data);

			unset($this->data['_method']);
			return;
		}

		$this->data = [];
	}
}
