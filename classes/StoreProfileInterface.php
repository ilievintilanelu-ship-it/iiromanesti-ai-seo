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

interface IiromanestiAiSeoStoreProfileInterface
{
    public function getCode();
    public function getName();
    public function getAssociatedDomain();
    public function supportsShop(Shop $shop = null);
    public function supportsLanguage(Language $language = null);
    public function getLanguageLabel();
    public function getVersion();
    public function getLastModifiedAt();
    public function getRules();
    public function getVersionHistory();
    public function getPromptSection();
}
