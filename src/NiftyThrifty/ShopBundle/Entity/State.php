<?php

namespace NiftyThrifty\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NiftyThrifty\ShopBundle\Entity\State
 *
 * @ORM\Table(name="state")
 * @ORM\Entity
 */
class State
{
    /**
     * @var integer $stateId
     *
     * @ORM\Column(name="state_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $stateId;

    /**
     * @var string $stateName
     *
     * @ORM\Column(name="state_name", type="string", length=64, nullable=false)
     */
    private $stateName;

    /**
     * @var string $stateCode
     *
     * @ORM\Column(name="state_code", type="string", length=5, nullable=false)
     */
    private $stateCode;

    /**
     * Returns an array of valid two-letter state codes.  Includes DC.
     *
     * @return Array
     */
    public static function getValidStateCodes()
    {
        $states = array('ak', 'al', 'ar', 'az', 'ca', 'co', 'ct', 'dc', 'de', 'fl', 
                        'ga', 'hi', 'ia', 'id', 'il', 'in', 'ks', 'ky', 'la', 'ma', 
                        'md', 'me', 'mi', 'mn', 'mo', 'ms', 'mt', 'nb', 'nc', 'nd', 
                        'nh', 'nj', 'nm', 'nv', 'ny', 'oh', 'ok', 'or', 'pa', 'ri', 
                        'sc', 'sd', 'tn', 'tx', 'ut', 'va', 'vt', 'wa', 'wi', 'wv', 'wy');
        return array_map('strtoupper', $states);
    }

    /**
     * Return an array of states for select choices.
     *
     * @return Array
     */
    public static function getStateChoices()
    {

        return array('AL'=>"Alabama", 'AK'=>"Alaska", 'AZ'=>"Arizona", 'AR'=>"Arkansas", 'CA'=>"California", 
                     'CO'=>"Colorado", 'CT'=>"Connecticut", 'DE'=>"Delaware", 'DC'=>"District Of Columbia", 'FL'=>"Florida", 
                     'GA'=>"Georgia", 'HI'=>"Hawaii", 'ID'=>"Idaho", 'IL'=>"Illinois", 'IN'=>"Indiana", 
                     'IA'=>"Iowa", 'KS'=>"Kansas", 'KY'=>"Kentucky", 'LA'=>"Louisiana", 'ME'=>"Maine", 
                     'MD'=>"Maryland", 'MA'=>"Massachusetts", 'MI'=>"Michigan", 'MN'=>"Minnesota", 'MS'=>"Mississippi", 
                     'MO'=>"Missouri", 'MT'=>"Montana", 'NE'=>"Nebraska", 'NV'=>"Nevada", 'NH'=>"New Hampshire", 
                     'NJ'=>"New Jersey", 'NM'=>"New Mexico", 'NY'=>"New York", 'NC'=>"North Carolina", 'ND'=>"North Dakota", 
                     'OH'=>"Ohio", 'OK'=>"Oklahoma", 'OR'=>"Oregon", 'PA'=>"Pennsylvania", 'RI'=>"Rhode Island", 
                     'SC'=>"South Carolina", 'SD'=>"South Dakota", 'TN'=>"Tennessee", 'TX'=>"Texas", 'UT'=>"Utah", 
                     'VT'=>"Vermont", 'VA'=>"Virginia", 'WA'=>"Washington", 'WV'=>"West Virginia", 'WI'=>"Wisconsin", 'WY'=>"Wyoming");
    }

    /**
     * Get stateId
     *
     * @return integer 
     */
    public function getStateId()
    {
        return $this->stateId;
    }

    /**
     * Set stateName
     *
     * @param string $stateName
     * @return State
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;
    
        return $this;
    }

    /**
     * Get stateName
     *
     * @return string 
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set stateCode
     *
     * @param string $stateCode
     * @return State
     */
    public function setStateCode($stateCode)
    {
        $this->stateCode = $stateCode;
    
        return $this;
    }

    /**
     * Get stateCode
     *
     * @return string 
     */
    public function getStateCode()
    {
        return $this->stateCode;
    }
}
