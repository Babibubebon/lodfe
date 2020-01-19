<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


function joinHostPort($components)
{
    return $components['host'] . (isset($components['port']) ? ':' . $components['port'] : '');
}

/** @var Laravel\Lumen\Routing\Router $router */

foreach (config('datasets') as $name => $dataset) {
    // route for resource URI
    $urlComponents = parse_url($dataset['resource_uri']);
    $hostPort = $dataset['host_name'] ?: joinHostPort($urlComponents);
    if ($hostPort === $_SERVER['HTTP_HOST']) {
        $router->addRoute('GET', $urlComponents['path'], [
            'uses' => 'ResourceController@resource',
            'as' => 'resource.' . $name,
            'middleware' => 'content_negotiation'
        ]);
    }

    // route for html URI
    $urlComponents = parse_url($dataset['html_uri']);
    $hostPort = $dataset['host_name'] ?: joinHostPort($urlComponents);
    if ($hostPort === $_SERVER['HTTP_HOST']) {
        $router->addRoute('GET', $urlComponents['path'], [
            'uses' => 'ResourceController@html',
            'as' => 'html.' . $name,
        ]);
    }

    // route for data URI
    $urlComponents = parse_url($dataset['data_uri']);
    $hostPort = $dataset['host_name'] ?: joinHostPort($urlComponents);
    if ($hostPort === $_SERVER['HTTP_HOST']) {
        $router->addRoute('GET', $urlComponents['path'].'{ext:\.[A-Za-z]+}', [
            'uses' => 'ResourceController@data',
            'as' => 'data.' . $name,
        ]);
    }
}
