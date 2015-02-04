<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\CollectionData;

class CollectionRepositoryFunctionalTest extends NiftyBaseTestCase
{

    private function _getRepository()
    {
        return $this->em->getRepository('NiftyThriftyShopBundle:Collection');
    }

    /**
     * Finds all active collections with active set to 'yes' and an ending date before now.
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findAllActive
     */
    public function testFindAllActiveDefault()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findAllActive();

        $this->assertCount(8, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 3);
        $this->assertEquals($collections[1]->getCollectionId(), 2);
        $this->assertEquals($collections[2]->getCollectionId(), 1);
        $this->assertEquals($collections[3]->getCollectionId(), 4);
        $this->assertEquals($collections[4]->getCollectionId(), 5);
        $this->assertEquals($collections[5]->getCollectionId(), 6);
        $this->assertEquals($collections[6]->getCollectionId(), 12);
        $this->assertEquals($collections[7]->getCollectionId(), 13);
    }

    /**
     * Find all active sort by timestamp descending
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findAllActive
     */
    public function testFindAllActiveSortAscending()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findAllActive(3, 'collectionDateEnd', 'asc');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 5);
        $this->assertEquals($collections[1]->getCollectionId(), 4);
        $this->assertEquals($collections[2]->getCollectionId(), 6);
    }

    /**
     * Find all active, sorting by something else.
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findAllActive
     */
    public function testFindAllActiveSortByOtherAsc() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findAllActive(3, 'collectionId', 'asc');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 1);
        $this->assertEquals($collections[1]->getCollectionId(), 2);
        $this->assertEquals($collections[2]->getCollectionId(), 3);
    }

    /**
     * Find all active, sorting by something else desc
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findAllActive
     */
    public function testFindAllActiveSortByOtherDesc() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findAllActive(3, 'collectionId');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 13);
        $this->assertEquals($collections[1]->getCollectionId(), 12);
        $this->assertEquals($collections[2]->getCollectionId(), 6);
    }

    /**
     * Find upcoming sales, which are both 'active' with a start date that hasn't yet occurred
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findUpcoming
     */
    public function testFindUpcoming() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findUpcoming();

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 8);
        $this->assertEquals($collections[1]->getCollectionId(), 7);
        $this->assertEquals($collections[2]->getCollectionId(), 9);
    }

    /**
     * Find sales that are ending soon, ending soon defined as within 24 hours.
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findAllEndingSoon
     */
    public function testFindEndingSoonDefault() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findEndingSoon();

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 5);
        $this->assertEquals($collections[1]->getCollectionId(), 4);
        $this->assertEquals($collections[2]->getCollectionId(), 6);
    }

    /**
     * Find all ending soon sort by timestamp descending
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findEndingSoon
     */
    public function testFindEndingSoonSortDescending()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findEndingSoon('collectionDateEnd', 'desc');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 6);
        $this->assertEquals($collections[1]->getCollectionId(), 4);
        $this->assertEquals($collections[2]->getCollectionId(), 5);

    }

    /**
     * Find all ending soon, sorting by something else.
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findEndingSoon
     */
    public function testFindEndingSoonSortByOtherAsc() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findEndingSoon('collectionId');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 4);
        $this->assertEquals($collections[1]->getCollectionId(), 5);
        $this->assertEquals($collections[2]->getCollectionId(), 6);
    }

    /**
     * Find all ending soon, sorting by something else desc
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findEndingSoon
     */
    public function testFindEndingSoonSortByOtherDesc() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findEndingSoon('collectionId', 'desc');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 6);
        $this->assertEquals($collections[1]->getCollectionId(), 5);
        $this->assertEquals($collections[2]->getCollectionId(), 4);
    }

    /**
     * Find active sales that are not ending within 24 hours.
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findAllActiveNotEndingSoon
     */
    public function testFindActiveNotEndingSoonDefault() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findActiveNotEndingSoon();

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 1);
        $this->assertEquals($collections[1]->getCollectionId(), 3);
        $this->assertEquals($collections[2]->getCollectionId(), 2);
    }

    /**
     * Find all not ending soon sort by timestamp descending
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findActiveNotEndingSoon
     */
    public function testFindActiveNotEndingSoonSortDescending()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findActiveNotEndingSoon('collectionDateEnd', 'desc');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 2);
        $this->assertEquals($collections[1]->getCollectionId(), 3);
        $this->assertEquals($collections[2]->getCollectionId(), 1);
    }

    /**
     * Find all not ending soon, sorting by something else.
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findActiveNotEndingSoon
     */
    public function testFindActiveNotEndingSoonSortByOtherAsc() 
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findActiveNotEndingSoon('collectionId');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 1);
        $this->assertEquals($collections[1]->getCollectionId(), 2);
        $this->assertEquals($collections[2]->getCollectionId(), 3);
    }

    /**
     * Find all not ending soon, sorting by something else desc
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository::findActiveNotEndingSoon
     */
    public function testFindActiveNotEndingSoonSortByOtherDesc()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findActiveNotEndingSoon('collectionId', 'desc');

        $this->assertCount(3, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 3);
        $this->assertEquals($collections[1]->getCollectionId(), 2);
        $this->assertEquals($collections[2]->getCollectionId(), 1);
    }
    
    /**
     * Test getting the collections that are shops for the navigation bar
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository:findShopsForNavigation
     */
    public function testFindShopsForNavigation()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findShopsForNavigation();

        $this->assertCount(2, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 13);
        $this->assertEquals($collections[1]->getCollectionId(), 12);
    }

    /**
     * Test getting shops
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository:findAllShops
     */
    public function testFindAllShops()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->findShopsForNavigation();

        $this->assertCount(2, $collections);
        $this->assertEquals($collections[0]->getCollectionId(), 13);
        $this->assertEquals($collections[1]->getCollectionId(), 12);
    }
    
    /**
     * Test getting filter collections
     *
     * @Group Repository
     * @Group Collection
     * @covers CollectionRepository:collectionsForFilter
     */
    public function testCollectionsForFilter()
    {
        $this->addFixture(new CollectionData);
        $this->executeFixtures();
        $collections = $this->_getRepository()->collectionsForFilter();

        $this->assertCount(9, $collections);
        $this->assertEquals($collections[0]['collectionId'], 3);
        $this->assertEquals($collections[1]['collectionId'], 2);
        $this->assertEquals($collections[2]['collectionId'], 1);
        $this->assertEquals($collections[3]['collectionId'], 4);
        $this->assertEquals($collections[4]['collectionId'], 5);
        $this->assertEquals($collections[5]['collectionId'], 6);
        $this->assertEquals($collections[6]['collectionId'], 10);
        $this->assertEquals($collections[7]['collectionId'], 12);
        $this->assertEquals($collections[8]['collectionId'], 13);
    }
}
