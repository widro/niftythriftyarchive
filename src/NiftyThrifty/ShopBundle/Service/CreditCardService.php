<?php

namespace NiftyThrifty\ShopBundle\Service;
 
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

class CreditCardService
{
    public $cardName;
    public $cardNumber;
    public $expireMonth;
    public $expireYear;
    public $cvv;
    public $formattedDate;
    
    public function set($opts=array())
    {
        $this->cardName     = $opts['cardName'];
        $this->cardNumber   = $opts['cardNumber'];
        $this->expireMonth  = $opts['expireMonth'];
        $this->expireYear   = $opts['expireYear'];
        $this->cvv          = $opts['cvv'];
    }

    /**
     * Object validation.
     *
     * IMPORTANT NOTE: The fields here match the credit card input fields in Form/Type/OrderFormType.  This
     * allows the violations to be placed with the correct thing.
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('cardName',    new Assert\NotBlank(array('message' => 'Credit card name can not be blank.')));
        
        // Card number is not blank and passes both algorithm checks for valid numbers.
        $metadata->addPropertyConstraint('cardNumber',  new Assert\NotBlank(array('message' => 'Credit card number can not be blank.')));
        $metadata->addPropertyConstraint('cardNumber', 
                                            new Assert\CardScheme(
                                                array('schemes' => array('VISA','MASTERCARD','AMEX','DISCOVER'),
                                                      'message' => 'Credit card number is invalid.')
                                            )
                                        );
        $metadata->addPropertyConstraint('cardNumber', 
                                            new Assert\Luhn(
                                                array('message' => 'Please check your credit card number.')
                                            )
                                        );

        // Expiration date and CVV are valid formats.
        $metadata->addConstraint(new Assert\Callback(array('methods' => array('validateExpirationDate',
                                                                              'validateCVV'))));
    }
    
    /**
     * Return the proper Authorize.net formatted date string.
     */
    public function getFormattedDate()
    {
        return sprintf("%04d-%02d", $this->expireYear, $this->expireMonth);
    }
    
    /**
     * Return the last four digits of a card, which we can save.
     */
    public function getSavedDigits()
    {
        return substr($this->cardNumber, -4);
    }
    
    /**
     * Given an input string (usually from a payment profile), return whether it's a match to the 
     * current card number.
     *
     * @param   $numberString       A string of some length.  We match the last four digits
     *                                  with the last four digits of the card's number
     * @return  boolean
     */
    public function isMatch($numberString)
    {
        return (substr($numberString, -4) == $this->getSavedDigits());
    }
    
    /**
     * Check date.  Dates are submitted as free form select boxes since symfony does not support
     * date time selectors without days.
     */
    public function validateExpirationDate(ExecutionContextInterface $context)
    {
        // Month must be one or two digits.
        if (!preg_match('/^\d{1,2}$/', $this->expireMonth)) {
            $context->addViolationAt('expirationDateMonth', 'Month must be selected.');
        }
        
        // Year must be 4 digits
        if (!preg_match('/^\d{4}$/', $this->expireYear)) {
            $context->addViolationAt('expirationDateYear', 'Year must be selected.');
        }
        
        // If either of these are already wrong, we can bail.
        if ($context->getViolations()->count() > 0) return;
        
        // Year is less than current year, card is expired.
        if ($this->expireYear < date("Y")) {
            $context->addViolationAt('expirationDateYear', "This card's year is expired.");
        }
        
        // Month is less than current month, year is this year, card is expired.
        if ($this->expireMonth < date("m") && $this->expireYear == date("Y")) {
            $context->addViolationAt('expirationDateMonth', "This card's month is expired.");
        }
    }
    
    /**
     * Check the CVV is the right length
     */
    public function validateCVV(ExecutionContextInterface $context)
    {
        // Get the first number of the credit card so we know how many digits to look for
        $firstNumber = (int) substr($this->cardNumber, 0, 1);

        // Card is an Amex, CVV should be 4 digits.
        if ($firstNumber === 3) {
            if (!preg_match("/^\d{4}$/", $this->cvv)) {
                $context->addViolationAt('securityCode', 'The security code should be a 4 digit number.');
            }
        } else if (!preg_match("/^\d{3}$/", $this->cvv)) {
            $context->addViolationAt('securityCode', 'The security code should be a 3 digit number.');
        }
    }
}
