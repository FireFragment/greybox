<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
use Drahak\Restful\Application\Routes\ResourceRoute;
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
    	$router[] = new ResourceRoute('api/v1/person/<id>/badge', 'Person:badge', ResourceRoute::PATCH);
    	$router[] = new ResourceRoute('api/v1/badge/check', 'Badge:check', ResourceRoute::PATCH);
    	//$router[] = new Route('api/v1/<presenter>[/id]/<action>', 'Sample:content');
    	//$router[] = new CrudRoute('api/v1/<presenter>[/<id>]', 'Sample');
    	$router[] = new CrudRoute('api/v1/<presenter>[/<id>[/<relation>[/<relationId>]]]', 'Sample');
		//$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
