<?php

namespace AndyLabo\Provider;


// Skip these two lines if you're using Composer
define('FACEBOOK_SDK_V4_SRC_DIR', '/../../vendor/facebook/php-sdk-v4/src/Facebook/');
require __DIR__ . '/../../vendor/facebook/php-sdk-v4/autoload.php';

use Silex\Application;
use Silex\ServiceProviderInterface;


use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;


FacebookSession::setDefaultApplication('1593677974224055','4aa8892f1b99a81ca87bc93099b89fba');

$helper = $fb->getRedirectLoginHelper();

class FacebookServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['facebook'] = $app->share(function () use ($app) {
            return new \Facebook(array(
                'appId'  => $app['facebook.app_id'],
                'secret' => $app['facebook.secret'],
                'default_graph_version' => 'v2.3',
            ));
        });
        
        $helper = new FacebookRedirectLoginHelper('http://www.contents1.com/user/login');
        
        try{
        $session = $helper->getSessionFromRedirect();
        }catch(Exception $e){
            echo $e->getMessage();
        }
        
        if(isset($_SESSION['token'])){
            
            $session = new FacebookSession($_SESSION['token']);
            try{
            }catch(FacebookAuthorizationException $e){
                $session = '';
            }
            
        }
        
        
        if(isset($session)){
            $_SESSION['token'] = $session->getToken();
            echo "successful<br>";
            
            $request = new FacebookRequest($session ,'Get','/me');
            $response = $request->execute();
            $graph = $response->getGraphObject(GraphUser::className());
            
            echo "Hi ". $graph->getName();
            
            
            
        }else
        {
            echo "<a href = ". $helper->getLoginUrl() ."> Login with Facebook </a>";
        }
    }

    public function boot(Application $app)
    {
    }
}
