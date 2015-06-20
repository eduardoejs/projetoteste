<?php
require_once "vendor/autoload.php";

$app = new Silex\Application();

$app['debug'] = true;

/*Doctrine config*/
use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\EventManager as EventManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Cache\ArrayCache as Cache,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\ClassLoader;

use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\DoctrineServiceProvider;

$cache = new Doctrine\Common\Cache\ArrayCache;
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;

$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
    $annotationReader, // use reader
    $cache // and a cache driver
);

$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    $cachedAnnotationReader, // our cached annotation reader
    array(__DIR__ . DIRECTORY_SEPARATOR . 'src')
);

$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();
$driverChain->addDriver($annotationDriver,'Code');

$config = new Doctrine\ORM\Configuration;
$config->setProxyDir('/tmp');
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

AnnotationRegistry::registerFile(__DIR__. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM' . DIRECTORY_SEPARATOR . 'Mapping' . DIRECTORY_SEPARATOR . 'Driver' . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php');

$evm = new Doctrine\Common\EventManager();
$em = EntityManager::create(
    array(
        'driver'  => 'pdo_mysql',
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'user'    => 'root',
        'password'  => 'root',
        'dbname'  => 'code_doctrine',
    ),
    $config,
    $evm
);

$app['user_repository'] = $app->share(function($app) use($em){    
    $user = new Code\Sistema\Entity\User;    
    $repo = $em->getRepository('Code\Sistema\Entity\User');
    $repo->setPasswordEncoder($app['security.encoder_factory']->getEncoder($user));
    
    return $repo;
});

$app['user_provider'] = $app->share(function() use($app){
    $repo = new Code\Sistema\Entity\UserProvider($app['db']);    
    return $repo;
});

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/src/Code/Sistema/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app->register(new SessionServiceProvider());

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbhost' => 'localhost',
        'dbname' => 'code_doctrine',
        'user' => 'root',
        'password' => 'root',
    ),
));

$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'anonymous' => true,
            'pattern' => '^/',
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'users' => $app->share(function() use($app){
                return new Code\Sistema\Entity\UserProvider($app['db']);
            }),
            'logout' => array('logout_path' => '/admin/logout')
        )
    )
));

/*$app['security.encoder.digest'] = $app->share(function ($app) {
    // use the sha1 algorithm
    // don't base64 encode the password
    // use only 1 iteration
    return new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('sha1', false, 1);
});*/

$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN'),
    array('^/produtos', 'ROLE_USER'),
    array('^/tags', 'ROLE_TESTE')
    
);

$app['security.role_hierarchy'] = array(
    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH', 'ROLE_TESTE'),
    'ROLE_USER' => array('ROLE_USER'),
    'ROLE_TESTE' => array('ROLE_TESTE')
);

return $app;