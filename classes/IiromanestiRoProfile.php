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

require_once dirname(__FILE__) . '/StoreProfileInterface.php';

class IiromanestiAiSeoIiromanestiRoProfile implements IiromanestiAiSeoStoreProfileInterface
{
    const CODE = 'iiromanesti_ro';
    const DOMAIN = 'iiromanesti.ro';
    const VERSION = '1.0.0';
    const LAST_MODIFIED_AT = '2026-07-15';

    public function getCode() { return self::CODE; }
    public function getName() { return 'iiromanesti.ro'; }
    public function getAssociatedDomain() { return self::DOMAIN; }
    public function getLanguageLabel() { return 'Romana fara diacritice'; }
    public function getVersion() { return self::VERSION; }
    public function getLastModifiedAt() { return self::LAST_MODIFIED_AT; }

    public function supportsShop(Shop $shop = null)
    {
        if (!$shop || !Validate::isLoadedObject($shop)) {
            return true;
        }
        $domain = strtolower((string) $shop->domain);
        $domainSsl = strtolower((string) $shop->domain_ssl);
        return $domain === self::DOMAIN || $domainSsl === self::DOMAIN;
    }

    public function supportsLanguage(Language $language = null)
    {
        if (!$language || !Validate::isLoadedObject($language)) {
            return true;
        }
        return strtolower((string) $language->iso_code) === 'ro';
    }

    public function getRules()
    {
        return array(
            'Identitate' => array(
                'Magazin romanesc de ii, camasi si articole traditionale.',
                'Accent pe port popular romanesc, traditie, autenticitate si prezentare comerciala clara.',
                'Continut orientat spre Google Romania si cresterea conversiilor.',
                'Scrie in limba romana fara diacritice.',
            ),
            'Denumire produs' => array(
                'Titlu clar, natural si comercial.',
                'Include tip produs, public, material, culoare si broderie numai daca informatiile sunt confirmate.',
                'Evita formularile artificiale, repetarile inutile si afirmatiile inventate.',
            ),
            'Descriere scurta' => array(
                'HTML curat, orientat spre cumparator.',
                'Explica rapid produsul, materialul confirmat, broderia, beneficiile si ocaziile de purtare.',
                'Fara keyword stuffing.',
            ),
            'Descriere lunga' => array(
                'HTML compatibil PrestaShop, cu H2 si H3, fara H1 suplimentar.',
                'Include prezentare, beneficii reale, ocazii de purtare si continutul setului cand produsul este set.',
                'Materialul, broderia, marimile, intretinerea, FAQ si linkurile interne se folosesc numai din date confirmate/reale.',
            ),
            'Meta si etichete' => array(
                'Meta Title natural, relevant, cu expresia principala si lungime validata prin setarile modulului.',
                'Meta Description descrie produsul si beneficiul principal fara promisiuni exagerate.',
                'Etichete relevante, separate prin virgula, fara Meta Keywords, repetari sau variante aproape identice.',
            ),
            'Imagini' => array(
                'ALT diferit pentru fiecare imagine si descriere exacta, fara keyword stuffing.',
                'Title imagine separat.',
                'Nu presupune ca o persoana este Julia sau Reece fara informatie explicita in context.',
            ),
            'Caracteristici' => array(
                'Foloseste caracteristicile reale existente in PrestaShop.',
                'Recomanda valori pentru Tip produs, Gen, Material, Culoare, Tip broderie, Lungime maneca, Tip guler, Stil, Proprietati si Ocazie.',
                'Nu crea automat caracteristici sau valori fara confirmare si nu inventa materialul, originea sau metoda de fabricatie.',
            ),
            'Conversie' => array(
                'Evidentiaza confortul, aspectul, utilitatea si ocaziile reale de purtare.',
                'Foloseste indemnuri naturale, fara presiune sau afirmatii false.',
                'Raspunde intrebarilor care pot bloca achizitia si nu promite livrare, stoc, origine sau calitate neconfirmata.',
            ),
        );
    }

    public function getVersionHistory()
    {
        return array(array('version' => self::VERSION, 'date' => self::LAST_MODIFIED_AT, 'changes' => 'Versiune initiala predefinita pentru magazinul iiromanesti.ro.'));
    }

    public function getPromptSection()
    {
        $lines = array('PROFIL ACTIV: ' . $this->getName(), 'Versiune profil: ' . self::VERSION, 'Limba: ' . $this->getLanguageLabel());
        foreach ($this->getRules() as $section => $rules) {
            $lines[] = $section . ':';
            foreach ($rules as $rule) {
                $lines[] = '- ' . $rule;
            }
        }
        return implode("\n", $lines);
    }
}
