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

require_once _PS_MODULE_DIR_ . 'iiromanesti_ai_seo/classes/StoreProfileManager.php';

class AdminIiromanestiAiSeoProfilesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $manager = new IiromanestiAiSeoStoreProfileManager();
        $shop = new Shop((int) $this->context->shop->id);
        $language = new Language((int) $this->context->language->id);
        $profile = $manager->getActiveProfile($shop, $language);

        $this->context->smarty->assign(array(
            'active_profile' => array(
                'name' => $profile->getName(),
                'code' => $profile->getCode(),
                'shop' => Validate::isLoadedObject($shop) ? $shop->name . ' (' . $profile->getAssociatedDomain() . ')' : $profile->getAssociatedDomain(),
                'language' => $profile->getLanguageLabel(),
                'rules' => $profile->getRules(),
                'version' => $profile->getVersion(),
                'last_modified_at' => $profile->getLastModifiedAt(),
                'history' => $profile->getVersionHistory(),
            ),
        ));

        $this->setTemplate('profiles.tpl');
    }
}
