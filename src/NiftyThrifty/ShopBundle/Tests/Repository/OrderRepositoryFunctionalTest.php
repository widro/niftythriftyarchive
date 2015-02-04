<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\OrderData;

class UserOrderRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * Find an unpaid order.
     */
    public function testFindUnpaidByBasket()
    {
        $this->addFixture(new OrderData);
        $this->executeFixtures();
        $order = $this->em
                      ->getRepository('NiftyThriftyShopBundle:Order')
                      ->findUnpaidByBasket(2);
        $this->assertEquals($order->getOrderId(), 2);
        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\Order', $order);
   }

    /**
     * Test exception case
     */
    public function testFindUnpaidByBasketNotFound()
    {
        $this->addFixture(new OrderData);
        $this->executeFixtures();
        $order = $this->em
                      ->getRepository('NiftyThriftyShopBundle:Order')
                      ->findUnpaidByBasket(3);
        $this->assertNull($order);
    }
}
