<?php

namespace SonarApp;

class App
{
	protected $controller = "home";
	protected $controllerWithNamespace = "SonarApp\\home";
	protected $method = "index";
	protected $params = [];

	public function __construct( )
	{
		$url = $this->parseUrl( );

		if ( file_exists( "../app/controllers/" . $url[0] . ".php" ) )
		{
			$this->controller = $url[0];
			$this->controllerWithNamespace = "SonarApp\\".$url[0];
		}
        else if ( isset( $url[0] ) ) // checks if the a controller was specified
        {
            if ( \Sonar\Config::Get( "errors/isCodePagesEnabled" ) )
            {
                $this->controller = "errors";
                $this->controllerWithNamespace = "SonarApp\\errors";
            }
        }
        
        unset( $url[0] );

		require_once( "../app/controllers/" . $this->controller . ".php" );

		$this->controllerWithNamespace = new $this->controllerWithNamespace;

		if ( isset( $url[1] ) )
		{
			if ( method_exists( $this->controllerWithNamespace, $url[1] ) )
			{
				$this->method = $url[1];
			}
            else // if the page view/page does not exit show 404 error page
            {
                if ( \Sonar\Config::Get( "errors/isCodePagesEnabled" ) )
                {
                    $this->controller = "errors";
                    $this->controllerWithNamespace = "SonarApp\\errors";
                
                    require_once( "../app/controllers/" . $this->controller . ".php" );
                    $this->controllerWithNamespace = new $this->controllerWithNamespace;
                
                    $this->method = "code404";
                }
            }
            
            unset( $url[1] );
		}
        
        $this->params = $url ? array_values( $url ) : [];

		call_user_func_array( [$this->controllerWithNamespace, $this->method], $this->params );
	}

	public function parseUrl( )
	{
		if ( isset( $_GET['url'] ) )
		{
			return $url = explode( "/", filter_var( rtrim( $_GET["url"], "/" ), FILTER_SANITIZE_URL ) );
		}
	}
}