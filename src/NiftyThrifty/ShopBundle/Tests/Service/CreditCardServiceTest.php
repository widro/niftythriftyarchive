<?php

namespace NiftyThrifty\ShopBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
#use Doctrine\Common\ClassLoader;

/**
 * Tests for the validator methods.
 */
class CreditCardServiceTest extends WebTestCase
{
    public $testCardArray;
    public $validator;
    public $cardService;
    
    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->validator    = $kernel->getContainer()->get('validator');
        $this->cardService  = $kernel->getContainer()->get('credit_card_validator');
        $this->testCardArray = array('cardName'     => 'Test Card Guy',
                                     'cardNumber'   => '4111111111111111',
                                     'expireMonth'  => '01',
                                     'expireYear'   => '2020',
                                     'cvv'          => '555');
    }

    public function testSet()
    {
        $this->cardService->set($this->testCardArray);
        
        $this->assertEquals($this->testCardArray['cardName'],   $this->cardService->cardName);
        $this->assertEquals($this->testCardArray['cardNumber'], $this->cardService->cardNumber);
        $this->assertEquals($this->testCardArray['expireMonth'],$this->cardService->expireMonth);
        $this->assertEquals($this->testCardArray['expireYear'], $this->cardService->expireYear);
        $this->assertEquals($this->testCardArray['cvv'],        $this->cardService->cvv);
    }

    public function testValidCard()
    {
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(0, $violationList->count());
    }

    public function testCardNameBlank()
    {
        $this->testCardArray['cardName'] = null;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Credit card name can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'cardName');
    }

    public function testCardNumberBlank()
    {
        $this->testCardArray['cardNumber'] = null;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Credit card number can not be blank.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'cardNumber');
    }
    
    public function testCardNumberMastercardPass()
    {
        $this->testCardArray['cardNumber'] = '5424000000000015';
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testCardNumberDiscoverPass()
    {
        $this->testCardArray['cardNumber'] = '6011000000000012';
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testCardNumberAmexPass()
    {
        $this->testCardArray['cardNumber']  = '370000000000002';
        $this->testCardArray['cvv']         = 4444;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(0, $violationList->count());
    }
    
    public function testCardNumberJCBFails()
    {
        $this->testCardArray['cardNumber'] = '3088000000000017';
        $this->testCardArray['cvv']        = 4444;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Credit card number is invalid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'cardNumber');
    }

    public function testCardNumberDinerFails()
    {
        $this->testCardArray['cardNumber'] = '38000000000006';
        $this->testCardArray['cvv']        = 4444;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Credit card number is invalid.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'cardNumber');
    }
    
    public function testCardNumberLuhnFails()
    {
        $this->testCardArray['cardNumber'] = '4252686795982564';
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Please check your credit card number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'cardNumber');
    }
    
    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testExpireMonthTooLongFails()
    {
        $this->testCardArray['expireMonth'] = 123;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Month must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'expirationDateMonth');
    }
    
    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testExpireMonthTooShortFails()
    {
        $this->testCardArray['expireMonth'] = null;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Month must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'expirationDateMonth');
    }

    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testExpireMonthCardExpiredFails()
    {
        $this->testCardArray['expireMonth'] = date("m") - 1;
        $this->testCardArray['expireYear']  = date("Y");
        if (!$this->testCardArray['expireMonth']) {
            $this->testCardArray['expireMonth'] = 12;
            $this->testCardArray['expireYear'] = date("Y")-1;
        }
        
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        "This card's month is expired.");
        $this->assertEquals($violationList[0]->getPropertyPath(),   'expirationDateMonth');
    }

    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testCardNumberSingleDigitMonthPass()
    {
        $this->testCardArray['expireMonth'] = 3;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(0, $violationList->count());
    }
    
    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testExpireYearTooShortFails()
    {
        $this->testCardArray['expireYear'] = 123;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Year must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'expirationDateYear');
    }
    
    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testExpireYearTooLongFails()
    {
        $this->testCardArray['expireYear'] = 12345;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'Year must be selected.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'expirationDateYear');
    }

    /**
     * @covers CreditCardService::validateExpirationDate
     */
    public function testExpireYearCardExpiredFails()
    {
        $this->testCardArray['expireMonth'] = date("m");
        $this->testCardArray['expireYear']  = date("Y")-1;
        
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        "This card's year is expired.");
        $this->assertEquals($violationList[0]->getPropertyPath(),   'expirationDateYear');
    }
    
    /**
     * @covers CreditCardService::validateCVV
     */
    public function testCVVTooLongFails()
    {
        $this->testCardArray['cvv'] = 4444;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The security code should be a 3 digit number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'securityCode');
    }
    
    /**
     * @covers CreditCardService::validateCVV
     */
    public function testCVVTooShortFails()
    {
        $this->testCardArray['cvv'] = 44;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The security code should be a 3 digit number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'securityCode');
    }
    
    /**
     * @covers CreditCardService::validateCVV
     */
    public function testAmexCVVPasses()
    {
        $this->testCardArray['cardNumber']  = '370000000000002';
        $this->testCardArray['cvv']         = 1234;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(0, $violationList->count());
    }
    
    /**
     * @covers CreditCardService::validateCVV
     */
    public function testAmexCVVTooLongFails()
    {
        $this->testCardArray['cardNumber']  = '370000000000002';
        $this->testCardArray['cvv']         = 44444;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The security code should be a 4 digit number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'securityCode');
    }
    
    /**
     * @covers CreditCardService::validateCVV
     */
    public function testAmexCVVTooShortFails()
    {
        $this->testCardArray['cardNumber']  = '370000000000002';
        $this->testCardArray['cvv']         = 444;
        $this->cardService->set($this->testCardArray);
        $violationList = $this->validator->validate($this->cardService);
        $this->assertEquals(1, $violationList->count());
        $this->assertEquals($violationList[0]->getMessage(),        'The security code should be a 4 digit number.');
        $this->assertEquals($violationList[0]->getPropertyPath(),   'securityCode');
    }

    public function testGetFormattedDate()
    {
        $this->cardService->set($this->testCardArray);
        $this->assertEquals($this->cardService->getFormattedDate(), '2020-01');
    }

    public function testGetFormattedDateOneDigitMonth()
    {
        $this->testCardArray['expireMonth'] = 2;
        $this->cardService->set($this->testCardArray);
        $this->assertEquals($this->cardService->getFormattedDate(), '2020-02');
    }
    
    public function testGetSavedDigits()
    {
        $this->testCardArray['cardNumber'] = '5424000000000015';
        $this->cardService->set($this->testCardArray);
        $this->assertEquals($this->cardService->getSavedDigits(), '0015');
    }
    
    public function testIsMatchPass()
    {
        $this->testCardArray['cardNumber'] = '5424000000000015';
        $this->cardService->set($this->testCardArray);
        $this->assertTrue($this->cardService->isMatch('XXXX0015'));
    }

    public function testIsMatchFail()
    {
        $this->testCardArray['cardNumber'] = '5424000000000015';
        $this->cardService->set($this->testCardArray);
        $this->assertFalse($this->cardService->isMatch('XXXX0016'));
    }
}