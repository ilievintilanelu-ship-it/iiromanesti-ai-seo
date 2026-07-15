<?php
/**
 * 2007-2026 PrestaShop
 *
 * @author    iiRomanesti
 * @copyright 2026 iiRomanesti
 * @license   Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/IiromanestiRoProfile.php';

class IiromanestiAiSeoStoreProfileManager
{
    private $profiles;

    public function __construct()
    {
        $this->profiles = array(new IiromanestiAiSeoIiromanestiRoProfile());
    }

    public function getActiveProfile(Shop $shop = null, Language $language = null)
    {
        foreach ($this->profiles as $profile) {
            if ($profile->supportsShop($shop) && $profile->supportsLanguage($language)) {
                return $profile;
            }
        }
        return $this->profiles[0];
    }

    public function getProfiles()
    {
        return $this->profiles;
    }
}
