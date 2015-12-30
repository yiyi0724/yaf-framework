<?php
use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;

class RoutePlugin extends Plugin_Abstract
{
     public function routerShutdown(Request_Abstract $request, Response_Abstract $response)
     {
         $controller = $request->getMOduleName();
		echo $controller;exit;
     }
}