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

require_once dirname(__FILE__) . '/StoreProfileManager.php';
require_once dirname(__FILE__) . '/PromptBuilder.php';

class IiromanestiAiSeoProductGeneratorService
{
    private $context;
    private $module;

    public function __construct(Context $context, Module $module)
    {
        $this->context = $context;
        $this->module = $module;
    }

    public function buildPrompt($idProduct, array $selectedFields, array $auditReport = array(), $idLang = null)
    {
        $idLang = $idLang ? (int) $idLang : (int) $this->context->language->id;
        $shop = new Shop((int) $this->context->shop->id);
        $language = new Language((int) $idLang);
        $profileManager = new IiromanestiAiSeoStoreProfileManager();
        $profile = $profileManager->getActiveProfile($shop, $language);
        $builder = new IiromanestiAiSeoPromptBuilder();

        return $builder->buildProductPrompt(
            $profile,
            $this->buildProductContext($idProduct, $idLang),
            $selectedFields,
            $auditReport,
            $this->getPromptSettings()
        );
    }

    private function getPromptSettings()
    {
        return array(
            'meta_title_max' => 70,
            'meta_description_max' => 160,
        );
    }

    public function buildProductContext($idProduct, $idLang = null)
    {
        $idLang = $idLang ? (int) $idLang : (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;
        $product = new Product((int) $idProduct, false, $idLang, $idShop);

        if (!Validate::isLoadedObject($product)) {
            throw new PrestaShopException('Produsul nu a fost gasit.');
        }

        return array(
            'id_product' => (int) $product->id,
            'language' => $this->getLanguageData($idLang),
            'category' => $this->getCategoryData((int) $product->id_category_default, $idLang, $idShop),
            'manufacturer' => $this->getManufacturerData((int) $product->id_manufacturer),
            'features' => $this->getFeatures((int) $product->id, $idLang),
            'combinations' => $this->getCombinations((int) $product->id, $idLang),
            'images' => $this->getImages((int) $product->id, $idLang),
            'existing_description' => array(
                'name' => trim((string) $product->name),
                'description_short' => (string) $product->description_short,
                'description' => (string) $product->description,
            ),
            'existing_meta' => array(
                'meta_title' => trim((string) $product->meta_title),
                'meta_description' => trim((string) $product->meta_description),
                'meta_keywords' => trim((string) $product->meta_keywords),
                'link_rewrite' => trim((string) $product->link_rewrite),
            ),
            'tags' => $this->getTags((int) $product->id, $idLang),
            'url' => $this->context->link->getProductLink($product, null, null, null, $idLang, $idShop),
            'similar_products' => $this->getSimilarProducts($product, $idLang, $idShop),
        );
    }

    private function getLanguageData($idLang)
    {
        $language = new Language((int) $idLang);

        return array(
            'id_lang' => (int) $idLang,
            'iso_code' => Validate::isLoadedObject($language) ? (string) $language->iso_code : '',
            'name' => Validate::isLoadedObject($language) ? (string) $language->name : '',
        );
    }

    private function getCategoryData($idCategory, $idLang, $idShop)
    {
        $category = new Category((int) $idCategory, (int) $idLang, (int) $idShop);

        if (!Validate::isLoadedObject($category)) {
            return array('id_category' => 0, 'name' => '', 'link_rewrite' => '');
        }

        return array(
            'id_category' => (int) $category->id,
            'name' => trim((string) $category->name),
            'link_rewrite' => trim((string) $category->link_rewrite),
        );
    }

    private function getManufacturerData($idManufacturer)
    {
        $manufacturer = new Manufacturer((int) $idManufacturer);

        if (!Validate::isLoadedObject($manufacturer)) {
            return array('id_manufacturer' => 0, 'name' => '');
        }

        return array(
            'id_manufacturer' => (int) $manufacturer->id,
            'name' => trim((string) $manufacturer->name),
        );
    }

    private function getFeatures($idProduct, $idLang)
    {
        $features = Product::getFrontFeaturesStatic((int) $idLang, (int) $idProduct);
        return is_array($features) ? $features : array();
    }

    private function getCombinations($idProduct, $idLang)
    {
        $product = new Product((int) $idProduct, false, (int) $idLang, (int) $this->context->shop->id);
        $attributes = $product->getAttributeCombinations((int) $idLang);
        return is_array($attributes) ? $attributes : array();
    }

    private function getImages($idProduct, $idLang)
    {
        $images = Image::getImages((int) $idLang, (int) $idProduct);
        return is_array($images) ? $images : array();
    }

    private function getTags($idProduct, $idLang)
    {
        $tags = Tag::getProductTags((int) $idProduct);
        return isset($tags[(int) $idLang]) && is_array($tags[(int) $idLang]) ? $tags[(int) $idLang] : array();
    }

    private function getSimilarProducts(Product $product, $idLang, $idShop)
    {
        $products = Product::getProducts((int) $idLang, 0, 6, 'position', 'ASC', (int) $product->id_category_default, true, $this->context);
        $similar = array();

        if (!is_array($products)) {
            return $similar;
        }

        foreach ($products as $row) {
            if ((int) $row['id_product'] === (int) $product->id) {
                continue;
            }

            $similar[] = array(
                'id_product' => (int) $row['id_product'],
                'name' => isset($row['name']) ? trim((string) $row['name']) : '',
                'url' => $this->context->link->getProductLink((int) $row['id_product'], null, null, null, (int) $idLang, (int) $idShop),
            );
        }

        return $similar;
    }
}
