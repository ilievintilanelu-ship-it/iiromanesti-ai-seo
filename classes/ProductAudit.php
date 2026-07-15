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

class IiromanestiAiSeoProductAudit
{
    private $context;
    private $module;

    public function __construct(Context $context, Module $module)
    {
        $this->context = $context;
        $this->module = $module;
    }

    public function analyze($idProduct)
    {
        $product = new Product((int) $idProduct, false, (int) $this->context->language->id, (int) $this->context->shop->id);
        if (!Validate::isLoadedObject($product)) {
            throw new PrestaShopException('Produsul nu a fost gasit.');
        }

        $issues = array();
        $recommendations = array();
        $stats = $this->getProductStats($product);

        $googleScore = 100;
        $conversionScore = 100;
        $completenessScore = 100;

        $this->checkTitle($stats, $issues, $recommendations, $googleScore, $conversionScore, $completenessScore);
        $this->checkDescriptions($stats, $issues, $recommendations, $googleScore, $conversionScore, $completenessScore);
        $this->checkMeta($stats, $issues, $recommendations, $googleScore, $completenessScore);
        $this->checkImages($stats, $issues, $recommendations, $googleScore, $conversionScore, $completenessScore);
        $this->checkTagsUrlAndCategory($stats, $issues, $recommendations, $googleScore, $conversionScore, $completenessScore);

        return array(
            'product_name' => $stats['title'],
            'id_product' => (int) $product->id,
            'scores' => array(
                'google' => max(0, (int) $googleScore),
                'conversion' => max(0, (int) $conversionScore),
                'completeness' => max(0, (int) $completenessScore),
            ),
            'stats' => $stats,
            'issues' => array_values(array_unique($issues)),
            'recommendations' => array_values(array_unique($recommendations)),
        );
    }

    private function getProductStats(Product $product)
    {
        $idLang = (int) $this->context->language->id;
        $idShop = (int) $this->context->shop->id;
        $images = Image::getImages($idLang, (int) $product->id);
        $tags = Tag::getProductTags((int) $product->id);
        $localizedTags = isset($tags[$idLang]) ? $tags[$idLang] : array();
        $descriptionShort = $this->plainText($product->description_short);
        $description = $this->plainText($product->description);
        $category = new Category((int) $product->id_category_default, $idLang, $idShop);

        return array(
            'title' => trim((string) $product->name),
            'title_length' => Tools::strlen(trim((string) $product->name)),
            'short_description_length' => Tools::strlen($descriptionShort),
            'long_description_length' => Tools::strlen($description),
            'content_words' => str_word_count($descriptionShort . ' ' . $description),
            'meta_title' => trim((string) $product->meta_title),
            'meta_title_length' => Tools::strlen(trim((string) $product->meta_title)),
            'meta_description' => trim((string) $product->meta_description),
            'meta_description_length' => Tools::strlen(trim((string) $product->meta_description)),
            'image_count' => count($images),
            'images_without_alt' => $this->countImagesWithoutAlt($images),
            'tag_count' => is_array($localizedTags) ? count($localizedTags) : 0,
            'url' => trim((string) $product->link_rewrite),
            'category' => Validate::isLoadedObject($category) ? trim((string) $category->name) : '',
        );
    }

    private function countImagesWithoutAlt(array $images)
    {
        $count = 0;
        foreach ($images as $image) {
            if (!isset($image['legend']) || trim((string) $image['legend']) === '') {
                $count++;
            }
        }

        return $count;
    }

    private function plainText($html)
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags((string) $html)));
    }

    private function checkTitle(array $stats, array &$issues, array &$recommendations, &$googleScore, &$conversionScore, &$completenessScore)
    {
        if ($stats['title'] === '') {
            $issues[] = $this->module->l('Titlul produsului lipseste.');
            $recommendations[] = $this->module->l('Adauga un titlu clar, cu termenul principal cautat de clienti.');
            $googleScore -= 20;
            $conversionScore -= 20;
            $completenessScore -= 15;
        } elseif ($stats['title_length'] < 20 || $stats['title_length'] > 70) {
            $issues[] = $this->module->l('Lungimea titlului nu este optima.');
            $recommendations[] = $this->module->l('Pastreaza titlul intre 20 si 70 de caractere, descriptiv si usor de citit.');
            $googleScore -= 8;
            $conversionScore -= 6;
        }
    }

    private function checkDescriptions(array $stats, array &$issues, array &$recommendations, &$googleScore, &$conversionScore, &$completenessScore)
    {
        if ($stats['short_description_length'] < 80) {
            $issues[] = $this->module->l('Descrierea scurta lipseste sau este prea scurta.');
            $recommendations[] = $this->module->l('Completeaza descrierea scurta cu beneficii, utilizare si diferentiatori principali.');
            $conversionScore -= 15;
            $completenessScore -= 10;
        }
        if ($stats['long_description_length'] < 300) {
            $issues[] = $this->module->l('Descrierea lunga are continut insuficient.');
            $recommendations[] = $this->module->l('Extinde descrierea lunga cu detalii tehnice, materiale, dimensiuni si context de utilizare.');
            $googleScore -= 18;
            $conversionScore -= 12;
            $completenessScore -= 15;
        }
        if ($stats['content_words'] < 120) {
            $issues[] = $this->module->l('Lungimea continutului este sub pragul recomandat.');
            $recommendations[] = $this->module->l('Tinteste minimum 120 de cuvinte relevante in descrierile produsului.');
            $googleScore -= 10;
            $completenessScore -= 8;
        }
    }

    private function checkMeta(array $stats, array &$issues, array &$recommendations, &$googleScore, &$completenessScore)
    {
        if ($stats['meta_title'] === '' || $stats['meta_title_length'] < 30 || $stats['meta_title_length'] > 70) {
            $issues[] = $this->module->l('Meta title lipseste sau are lungime nepotrivita.');
            $recommendations[] = $this->module->l('Adauga un meta title unic, de 30-70 caractere.');
            $googleScore -= 15;
            $completenessScore -= 8;
        }
        if ($stats['meta_description'] === '' || $stats['meta_description_length'] < 120 || $stats['meta_description_length'] > 160) {
            $issues[] = $this->module->l('Meta description lipseste sau nu are lungimea recomandata.');
            $recommendations[] = $this->module->l('Adauga o meta description unica, de 120-160 caractere, cu motiv de click.');
            $googleScore -= 15;
            $completenessScore -= 8;
        }
    }

    private function checkImages(array $stats, array &$issues, array &$recommendations, &$googleScore, &$conversionScore, &$completenessScore)
    {
        if ($stats['image_count'] < 1) {
            $issues[] = $this->module->l('Produsul nu are imagini.');
            $recommendations[] = $this->module->l('Adauga imagini clare ale produsului, inclusiv detalii si contexte de utilizare.');
            $googleScore -= 12;
            $conversionScore -= 25;
            $completenessScore -= 15;
        } elseif ($stats['images_without_alt'] > 0) {
            $issues[] = $this->module->l('Exista imagini fara ALT/legenda.');
            $recommendations[] = $this->module->l('Completeaza ALT-ul imaginilor cu descrieri naturale si relevante.');
            $googleScore -= 10;
            $completenessScore -= 6;
        }
    }

    private function checkTagsUrlAndCategory(array $stats, array &$issues, array &$recommendations, &$googleScore, &$conversionScore, &$completenessScore)
    {
        if ($stats['tag_count'] < 1) {
            $issues[] = $this->module->l('Produsul nu are taguri.');
            $recommendations[] = $this->module->l('Adauga taguri relevante pentru tema, material, stil sau utilizare.');
            $completenessScore -= 8;
        }
        if ($stats['url'] === '' || !Validate::isLinkRewrite($stats['url'])) {
            $issues[] = $this->module->l('URL-ul produsului lipseste sau nu este valid SEO.');
            $recommendations[] = $this->module->l('Foloseste un URL scurt, lizibil, cu termenul principal al produsului.');
            $googleScore -= 10;
            $completenessScore -= 6;
        }
        if ($stats['category'] === '') {
            $issues[] = $this->module->l('Categoria implicita lipseste sau nu poate fi citita.');
            $recommendations[] = $this->module->l('Asociaza produsul cu o categorie relevanta pentru navigare si context SEO.');
            $googleScore -= 8;
            $conversionScore -= 8;
            $completenessScore -= 8;
        }
    }
}
