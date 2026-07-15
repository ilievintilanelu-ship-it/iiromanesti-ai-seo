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

require_once _PS_MODULE_DIR_ . 'iiromanesti_ai_seo/classes/ProductAudit.php';

class AdminIiromanestiAiSeoProductAuditController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $idProduct = (int) Tools::getValue('id_product');
        if ($idProduct <= 0) {
            $this->errors[] = $this->l('Produsul nu a fost specificat.');
            $this->setTemplate('product_audit.tpl');
            return;
        }

        try {
            $auditor = new IiromanestiAiSeoProductAudit($this->context, $this->module);
            $report = $auditor->analyze($idProduct);
            $this->context->smarty->assign(array(
                'audit_report' => $report,
                'back_url' => $this->context->link->getAdminLink('AdminProducts') . '&id_product=' . (int) $idProduct . '&updateproduct',
            ));
        } catch (Exception $exception) {
            $this->errors[] = $this->l('Auditul SEO nu a putut fi generat: ') . $exception->getMessage();
        }

        $this->setTemplate('product_audit.tpl');
    }
}
