<?php

namespace NiftyThrifty\ShopBundle\Twig;

use Twig_Extension, Twig_SimpleFilter;

/**
 * File holds custom Twig helpers.
 */

class NiftyExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array (
            'slugify' => new \Twig_Filter_Method($this, 'slugifyText'),
        );
    }

    public function getFunctions()
    {
        return array (
            'get_webroot' => new \Twig_Function_Method($this, 'getWebRoot'),
        );
    }

    public function slugifyText($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Return the correct domain based on environment
     */
    public function getWebRoot()
    {
        if ($_SERVER['SERVER_NAME'] == 'staging.niftythrifty.com') {
            return 'http://staging.niftythrifty.com/app_dev.php';
        }
	//print_r($_SERVER);
    }

    public function getName()
    {
        return 'nifty_extension';
    }
}
