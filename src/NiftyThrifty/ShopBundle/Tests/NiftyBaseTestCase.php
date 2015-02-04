<?php

namespace NiftyThrifty\ShopBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\ClassLoader;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;

abstract class NiftyBaseTestCase extends WebTestCase 
{
    protected $adminClient;
    protected $userClient;
    protected $anonClient;
    protected $em;
    protected $loader;
    protected $purger;
    protected $executor;
    protected $container;

    public function __construct($loadUsers=false)
    {
        if ($loadUsers) {
            $this->loadUsers();
        }
    }

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->loader = new Loader();
        $this->purger = new ORMPurger();
        $this->purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $this->executor = new ORMExecutor($this->em, $this->purger);
    }

    public function getLoggedInTestClient($username="ut_user", $password="ut_userpass")
    {
        $client = static::createClient();
        //$client->setServerParameter('HTTP_REFERER', '/');
        $crawler = $client->request('GET', '/login');

        $loginForm = $crawler->selectButton('login')
                             ->form(array('userEmail'    => $username,
                                          'userPassword' => $password),
                                    'POST');
        $client->submit($loginForm);
        $security = $client->getProfile()->getCollector('security');
        //$client->followRedirect();

        $this->assertTrue(is_string($security->getUser()) && strlen($security->getUser()) > 0);
        $this->assertTrue($security->isAuthenticated(), 'Logged in user is not authenticated.');

        return $client;
    }

    public function getLoggedInAdminTestClient()
    {
        return $this->getLoggedInTestClient('ut_admin', 'ut_adminpass');
    }

    public function addFixture($fixture)
    {
        $this->loader->addFixture($fixture);
    }

    public function executeFixtures()
    {
        $this->executor->execute($this->loader->getFixtures());
    }

    /**
     * Generate a remember me cookie so we can test full authentication requirement.
     */
    public function getRememberMeCookie()
    {
        $client = static::createClient();

        // Get the crawler to the form page.
        $crawler = $client->request('GET', '/login');
        $loginForm = $crawler->selectButton('login')
                             ->form(array('userEmail'   => 'ut_user',
                                          'userPassword'=> 'ut_userpass',
                                          '_remember_me'=> 1),
                                    'POST');
        $client->setServerParameter('HTTP_REFERRER', '/');

        $crawler = $client->submit($loginForm);
        $rememberMeCookie = $client->getCookieJar()->get('REMEMBERME');

        return $rememberMeCookie;
    }
    
    /**
     * This is just a shortcut to loading the user fixture since almost all the
     * controller tests require a logged in user.
     */
    protected function loadUsers()
    {
        $this->addFixture(new UserData);
        $this->executeFixtures();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->container->get('doctrine')->getConnection()->close();
        parent::tearDown();
        $this->em->close();
    }
}
