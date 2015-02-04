<?php

namespace NiftyThrifty\ShopBundle\Tests;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\CollectionData;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserData;
use NiftyThrifty\ShopBundle\Tests\Fixture\BannerData;

/**
 * The content controller only delivers templates, so these tests do not require the database and
 * are relatively straight forward.
 */
class ContentControllerTest extends NiftyBaseTestCase
{
    public function testPageNotFound()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/aboutxxx');
        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function testAboutUs()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/about-us');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#aboutUs')->count() == 1);
    }

    public function testAboutUsNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/about-us');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#aboutUs')->count() == 1);
    }

    public function testCareers()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/careers');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#careers')->count() == 1);
    }

    public function testCareersNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/careers');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#careers')->count() == 1);
    }

    public function testContactUs()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/contact-us');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#contactUs')->count() == 1);
    }

    public function testContactUsNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/contact-us');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#contactUs')->count() == 1);
    }

    public function testFaq()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/faq');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#faq')->count() == 1);
    }

    public function testFaqNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/faq');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#faq')->count() == 1);
    }

    public function testHome()
    {
        $this->addFixture(new CollectionData);
        $this->addFixture(new UserData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();
        $client = $this->getLoggedInTestClient();

        $crawler = $client->request('GET', '/content/home');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#homeIndex')->count() == 1);
        $this->assertCount(4, $crawler->filter('div#homeCollections > div'));

        $children = $crawler->filter('div#homeCollections')->children();
        $this->assertEquals($children->eq(0)->attr('id'), 'homeCollectionId_3');
        $this->assertEquals($children->eq(1)->attr('id'), 'homeCollectionId_2');
        $this->assertEquals($children->eq(2)->attr('id'), 'homeCollectionId_1');
        $this->assertEquals($children->eq(3)->attr('class'), 'clear');
    }

    public function testHomeNotLoggedIn()
    {
        $this->addFixture(new CollectionData);
        $this->addFixture(new BannerData);
        $this->executeFixtures();

        $client = static::createClient();
        $crawler = $client->request('GET', '/content/home');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#homeIndex')->count() == 1);
        $this->assertCount(4, $crawler->filter('div#homeCollections > div'));

        $children = $crawler->filter('div#homeCollections')->children();
        $this->assertEquals($children->eq(0)->attr('id'), 'homeCollectionId_3');
        $this->assertEquals($children->eq(1)->attr('id'), 'homeCollectionId_2');
        $this->assertEquals($children->eq(2)->attr('id'), 'homeCollectionId_1');
        $this->assertEquals($children->eq(3)->attr('class'), 'clear');
    }

    public function testPress()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/press');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#press')->count() == 1);
    }

    public function testPressNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/press');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#press')->count() == 1);
    }

    public function testShippingAndReturns()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/shipping-and-returns');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#shippingAndReturns')->count() == 1);
    }

    public function testShippingAndReturnsNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/shipping-and-returns');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#shippingAndReturns')->count() == 1);
    }

    public function testTermsOfUse()
    {
        parent::__construct(true);
        $client = $this->getLoggedInTestClient();
        $crawler = $client->request('GET', '/content/terms-of-use');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#termsOfUse')->count() == 1);
    }

    public function testTermsOfUseNotLoggedIn()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/content/terms-of-use');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
        $this->assertTrue($crawler->filter('div#termsOfUse')->count() == 1);
    }

    public function testContactUsSendMessageSuccess()
    {
        $this->markTestIncomplete('More e-mail profiler stuff.');
    }

    public function testContactUsSendMessageFail()
    {
        $this->markTestIncomplete();
    }

}
