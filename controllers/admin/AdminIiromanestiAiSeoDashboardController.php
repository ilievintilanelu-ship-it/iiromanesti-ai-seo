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

class AdminIiromanestiAiSeoDashboardController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'dashboard_cards' => $this->getDashboardCards(),
        ));

        $this->setTemplate('dashboard.tpl');
    }

    private function getDashboardCards()
    {
        return array(
            array(
                'label' => $this->l('Produse totale'),
                'value' => $this->countTotalProducts(),
                'icon' => 'icon-cubes',
            ),
            array(
                'label' => $this->l('Categorii totale'),
                'value' => $this->countTotalCategories(),
                'icon' => 'icon-folder-open',
            ),
            array(
                'label' => $this->l('Produse active'),
                'value' => $this->countProductsByActiveStatus(1),
                'icon' => 'icon-check',
            ),
            array(
                'label' => $this->l('Produse inactive'),
                'value' => $this->countProductsByActiveStatus(0),
                'icon' => 'icon-remove',
            ),
            array(
                'label' => $this->l('Produse fara imagine'),
                'value' => $this->countProductsWithoutImage(),
                'icon' => 'icon-picture',
            ),
            array(
                'label' => $this->l('Produse fara descriere lunga'),
                'value' => $this->countProductsWithoutLongDescription(),
                'icon' => 'icon-align-left',
            ),
            array(
                'label' => $this->l('Produse fara meta title'),
                'value' => $this->countProductsWithoutMetaTitle(),
                'icon' => 'icon-header',
            ),
            array(
                'label' => $this->l('Produse fara meta description'),
                'value' => $this->countProductsWithoutMetaDescription(),
                'icon' => 'icon-list-alt',
            ),
            array(
                'label' => $this->l('Produse fara taguri'),
                'value' => $this->countProductsWithoutTags(),
                'icon' => 'icon-tags',
            ),
        );
    }

    private function countTotalProducts()
    {
        return (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'product`');
    }

    private function countTotalCategories()
    {
        if (Shop::isFeatureActive()) {
            return (int) Db::getInstance()->getValue(
                'SELECT COUNT(DISTINCT `id_category`) FROM `' . _DB_PREFIX_ . 'category_shop` WHERE `id_shop` = ' . (int) $this->context->shop->id
            );
        }

        return (int) Db::getInstance()->getValue('SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'category`');
    }

    private function countProductsByActiveStatus($active)
    {
        if (Shop::isFeatureActive()) {
            return (int) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_shop` = ' . (int) $this->context->shop->id . ' AND `active` = ' . (int) $active
            );
        }

        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'product` WHERE `active` = ' . (int) $active
        );
    }

    private function countProductsWithoutImage()
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON i.`id_product` = p.`id_product`
            WHERE i.`id_image` IS NULL'
        );
    }

    private function countProductsWithoutLongDescription()
    {
        return $this->countProductsWithEmptyProductLangField('description');
    }

    private function countProductsWithoutMetaTitle()
    {
        return $this->countProductsWithEmptyProductLangField('meta_title');
    }

    private function countProductsWithoutMetaDescription()
    {
        return $this->countProductsWithEmptyProductLangField('meta_description');
    }

    private function countProductsWithEmptyProductLangField($field)
    {
        $allowedFields = array('description', 'meta_title', 'meta_description');
        if (!in_array($field, $allowedFields, true)) {
            return 0;
        }

        $shopCondition = '';
        if (Shop::isFeatureActive()) {
            $shopCondition = ' AND pl.`id_shop` = ' . (int) $this->context->shop->id;
        }

        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.`id_product` = p.`id_product` AND pl.`id_lang` = ' . (int) $this->context->language->id . $shopCondition . '
            WHERE pl.`id_product` IS NULL OR TRIM(pl.`' . bqSQL($field) . '`) = ""'
        );
    }

    private function countProductsWithoutTags()
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*)
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_tag` pt ON pt.`id_product` = p.`id_product`
            WHERE pt.`id_product` IS NULL'
        );
    }
}
