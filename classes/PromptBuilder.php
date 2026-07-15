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

class IiromanestiAiSeoPromptBuilder
{
    public function buildProductPrompt(IiromanestiAiSeoStoreProfileInterface $profile, array $productContext, array $selectedFields, array $auditReport = array(), array $settings = array())
    {
        return implode("\n\n", array(
            $this->getSafetyRules($settings),
            $profile->getPromptSection(),
            'DATE REALE PRODUS (nu inventa informatii lipsa):\n' . json_encode($productContext),
            'CAMPURI SELECTATE:\n' . json_encode(array_values($selectedFields)),
            'REZULTAT AUDIT PRODUS:\n' . json_encode($auditReport),
            'SCHEMA JSON RASPUNS:\n' . json_encode($this->getResponseSchema()),
        ));
    }

    private function getSafetyRules(array $settings)
    {
        $metaTitleMax = isset($settings['meta_title_max']) ? (int) $settings['meta_title_max'] : 70;
        $metaDescriptionMax = isset($settings['meta_description_max']) ? (int) $settings['meta_description_max'] : 160;
        return 'REGULI GENERALE DE SIGURANTA:\n- Foloseste numai date confirmate din context.\n- Daca o informatie lipseste, marcheaza explicit ca lipseste in notes si nu o inventa.\n- Nu modifica produsul si nu returna instructiuni de salvare automata.\n- Respecta lungimea configurata: meta_title max ' . $metaTitleMax . ' caractere, meta_description max ' . $metaDescriptionMax . ' caractere.';
    }

    private function getResponseSchema()
    {
        return array(
            'product_name' => 'string|null',
            'short_description' => 'html|null',
            'long_description_html' => 'html|null',
            'meta_title' => 'string|null',
            'meta_description' => 'string|null',
            'tags' => 'comma separated string|null',
            'images' => array(array('id_image' => 'int', 'alt' => 'string', 'title' => 'string')),
            'features' => array(array('name' => 'string', 'recommended_value' => 'string', 'confidence' => 'confirmed|missing')),
            'faq' => array(array('question' => 'string', 'answer' => 'string')),
            'notes' => array('string'),
        );
    }
}
