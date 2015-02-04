<?php

namespace NiftyThrifty\ShopBundle\Tests\Repository;

use NiftyThrifty\ShopBundle\Tests\NiftyBaseTestCase;
use NiftyThrifty\ShopBundle\Tests\Fixture\UserInvitationData;

class UserInvitationRepositoryFunctionalTest extends NiftyBaseTestCase
{
    /**
     * A user invitation that is found.
     *
     * @group Repository
     */
    public function testFindAcceptedByUserId()
    {
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();
        $invitation = $this->em
            ->getRepository('NiftyThriftyShopBundle:UserInvitation')
            ->findAcceptedByUserId(3);

        $this->assertInstanceOf('\NiftyThrifty\ShopBundle\Entity\UserInvitation', $invitation);
        $this->assertEquals($invitation->getUserInvitationId(), 4);
        $this->assertEquals($invitation->getUserId(),           1);
    }
    
    /**
     * Test that a user id has no accepted invitation.
     *
     * @group Repository
     */
    public function testFindAcceptedByUserIdNoResult()
    {
        $this->addFixture(new UserInvitationData);
        $this->executeFixtures();
        $invitation = $this->em
            ->getRepository('NiftyThriftyShopBundle:UserInvitation')
            ->findAcceptedByUserId(1);
            
        $this->assertNull($invitation);
    }
}
