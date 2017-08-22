<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Drahak\Restful\Application\Routes\CrudRoute;


class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
    	$router[] = new CrudRoute('api/v1/<presenter>[/<id>[/<relation>[/<relationId>]]]', 'Sample');
		//$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
