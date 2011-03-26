<?php
/**
 * Application Bootstrap
 *
 * @author          Eddie Jaoude
 * @package       Default Module
 *
 */
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Doctype
     *
     * @author          Eddie Jaoude
     * @param           void
     * @return          void
     *
     */
    protected function _initDoctype() {
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype('XHTML1_STRICT');
    }

    /**
     * Configuration
     *
     * @author          Eddie Jaoude
     * @param           void
     * @return          void
     *
     */
    protected function _initConfig() {
        # get config
        $this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);

        # get registery
        $this->_registry = Zend_Registry::getInstance();

        # save new database adapter to registry
        $this->_registry->auth->_hash = $this->_config->auth->hash;
    }
    
    /**
     * Initializes and returns Doctrine ORM entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     * @todo Resource configurator like http://framework.zend.com/wiki/x/0IAbAQ
     */
    protected function _initDoctrine()
    {
        # doctrine loader
        require_once APPLICATION_PATH . '/../library/Doctrine/Common/ClassLoader.php';
        $doctrineAutoloader = new \Doctrine\Common\ClassLoader('Doctrine', APPLICATION_PATH . '/../library');
        $doctrineAutoloader->register();
        
        # configure doctrine
        $cache = new Doctrine\Common\Cache\ArrayCache;
        $config = new Configuration;
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver( APPLICATION_PATH . '/auth/models/entities' );
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir( APPLICATION_PATH . '/auth/models/proxies' );
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(true);

        # database connection
        $this->_registry->doctrine->_em = EntityManager::create($this->_config->doctrine->connection->toArray(), $config);
    }

}

