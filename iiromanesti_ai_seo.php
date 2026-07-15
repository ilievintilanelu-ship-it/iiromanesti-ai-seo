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

require_once __DIR__ . '/classes/Logger.php';
require_once __DIR__ . '/classes/ApiClient.php';

class Iiromanesti_Ai_Seo extends Module
{
    const CONFIG_PREFIX = 'IIRO_AI_SEO_';
    const CONFIG_ENABLED = 'IIRO_AI_SEO_ENABLED';
    const CONFIG_PROVIDER = 'IIRO_AI_SEO_PROVIDER';
    const CONFIG_MODEL = 'IIRO_AI_SEO_MODEL';
    const CONFIG_API_KEY = 'IIRO_AI_SEO_API_KEY';
    const CONFIG_TIMEOUT = 'IIRO_AI_SEO_TIMEOUT';
    const CONFIG_LOG_LEVEL = 'IIRO_AI_SEO_LOG_LEVEL';
    const CONFIG_LOG_RETENTION = 'IIRO_AI_SEO_LOG_RETENTION';
    const CONFIG_HISTORY_MAX = 'IIRO_AI_SEO_HISTORY_MAX';

    const TAB_CLASS_MAIN = 'AdminIiromanestiAiSeo';
    const TAB_CLASS_DASHBOARD = 'AdminIiromanestiAiSeoDashboard';

    const DEFAULT_PROVIDER = 'openai';
    const DEFAULT_MODEL = 'gpt-4o-mini';
    const DEFAULT_TIMEOUT = 30;
    const DEFAULT_LOG_LEVEL = 'info';
    const DEFAULT_LOG_RETENTION = 30;
    const DEFAULT_HISTORY_MAX = 10;

    /** @var IiromanestiAiSeoLogger */
    private $moduleLogger;

    public function __construct()
    {
        $this->name = 'iiromanesti_ai_seo';
        $this->tab = 'seo';
        $this->version = '0.1.0';
        $this->author = 'iiRomanesti';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.8.11', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('iiRomanesti AI SEO');
        $this->description = $this->l('Fundatie tehnica pentru generarea si administrarea SEO asistata de AI.');
        $this->confirmUninstall = $this->l('Sigur doriti sa dezinstalati modulul iiRomanesti AI SEO?');

        $this->moduleLogger = new IiromanestiAiSeoLogger($this);
    }

    public function install()
    {
        $installed = parent::install()
            && $this->installConfiguration()
            && $this->installTabs();

        if ($installed) {
            $this->moduleLogger->info('Instalare modul finalizata.');
        } else {
            $this->moduleLogger->error('Instalarea modulului a esuat.');
        }

        return (bool) $installed;
    }

    public function uninstall()
    {
        $this->moduleLogger->info('Dezinstalare modul initiata.');

        $uninstalled = $this->uninstallTabs()
            && $this->uninstallConfiguration()
            && parent::uninstall();

        if (!$uninstalled) {
            $this->moduleLogger->error('Dezinstalarea modulului a esuat.');
        }

        return (bool) $uninstalled;
    }

    public function getContent()
    {
        if (!$this->canConfigure()) {
            return $this->displayError($this->l('Nu aveti permisiunea de a configura acest modul.'));
        }

        $output = '';

        if (Tools::isSubmit('submitIiromanestiAiSeoConfig')) {
            $output .= $this->processConfigurationForm();
        }

        if (Tools::isSubmit('submitIiromanestiAiSeoConnectionTest')) {
            $output .= $this->processConnectionTest();
        }

        return $output . $this->renderConfigurationForm();
    }

    private function installTabs()
    {
        $mainTabId = $this->installTab(
            self::TAB_CLASS_MAIN,
            $this->l('iiRomanesti AI'),
            0,
            'icon-lightbulb-o'
        );

        if (!$mainTabId) {
            return false;
        }

        return (bool) $this->installTab(
            self::TAB_CLASS_DASHBOARD,
            $this->l('Dashboard'),
            (int) $mainTabId,
            'icon-dashboard'
        );
    }

    private function installTab($className, $name, $parentId, $icon = '')
    {
        $existingTabId = (int) Tab::getIdFromClassName($className);
        if ($existingTabId) {
            return $existingTabId;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->id_parent = (int) $parentId;
        $tab->module = $this->name;
        if (property_exists($tab, 'icon')) {
            $tab->icon = $icon;
        }

        foreach (Language::getLanguages(false) as $language) {
            $tab->name[(int) $language['id_lang']] = $name;
        }

        if (!$tab->add()) {
            return false;
        }

        return (int) $tab->id;
    }

    private function uninstallTabs()
    {
        $result = true;
        $tabClasses = array(
            self::TAB_CLASS_DASHBOARD,
            self::TAB_CLASS_MAIN,
        );

        foreach ($tabClasses as $className) {
            $tabId = (int) Tab::getIdFromClassName($className);
            if (!$tabId) {
                continue;
            }

            $tab = new Tab($tabId);
            if (Validate::isLoadedObject($tab)) {
                $result = (bool) $tab->delete() && $result;
            }
        }

        return $result;
    }

    private function installConfiguration()
    {
        return Configuration::updateValue(self::CONFIG_ENABLED, 0)
            && Configuration::updateValue(self::CONFIG_PROVIDER, self::DEFAULT_PROVIDER)
            && Configuration::updateValue(self::CONFIG_MODEL, self::DEFAULT_MODEL)
            && Configuration::updateValue(self::CONFIG_API_KEY, '')
            && Configuration::updateValue(self::CONFIG_TIMEOUT, self::DEFAULT_TIMEOUT)
            && Configuration::updateValue(self::CONFIG_LOG_LEVEL, self::DEFAULT_LOG_LEVEL)
            && Configuration::updateValue(self::CONFIG_LOG_RETENTION, self::DEFAULT_LOG_RETENTION)
            && Configuration::updateValue(self::CONFIG_HISTORY_MAX, self::DEFAULT_HISTORY_MAX);
    }

    private function uninstallConfiguration()
    {
        $keys = array(
            self::CONFIG_ENABLED,
            self::CONFIG_PROVIDER,
            self::CONFIG_MODEL,
            self::CONFIG_API_KEY,
            self::CONFIG_TIMEOUT,
            self::CONFIG_LOG_LEVEL,
            self::CONFIG_LOG_RETENTION,
            self::CONFIG_HISTORY_MAX,
        );

        $result = true;
        foreach ($keys as $key) {
            $result = Configuration::deleteByName($key) && $result;
        }

        return $result;
    }

    private function renderConfigurationForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitIiromanestiAiSeoConfig';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigurationValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigurationForm()));
    }

    private function getConfigurationForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configurare iiRomanesti AI SEO'),
                    'icon' => 'icon-cogs',
                ),
                'description' => $this->l('Configureaza conexiunea AI. Testul de conexiune nu genereaza continut si nu modifica produse.'),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Activare modul'),
                        'name' => self::CONFIG_ENABLED,
                        'is_bool' => true,
                        'values' => array(
                            array('id' => 'active_on', 'value' => 1, 'label' => $this->l('Da')),
                            array('id' => 'active_off', 'value' => 0, 'label' => $this->l('Nu')),
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Furnizor AI'),
                        'name' => self::CONFIG_PROVIDER,
                        'options' => array(
                            'query' => array(
                                array('id' => 'openai', 'name' => 'OpenAI'),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Model AI'),
                        'name' => self::CONFIG_MODEL,
                        'required' => true,
                        'desc' => $this->l('Exemplu: gpt-4o-mini. Prompturile functionale vor fi adaugate intr-o etapa ulterioara.'),
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->l('Cheia API'),
                        'name' => self::CONFIG_API_KEY,
                        'desc' => $this->l('Cheia este salvata in configurarea PrestaShop si nu este scrisa in jurnale.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Timeout API (secunde)'),
                        'name' => self::CONFIG_TIMEOUT,
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Nivel jurnal'),
                        'name' => self::CONFIG_LOG_LEVEL,
                        'options' => array(
                            'query' => array(
                                array('id' => 'error', 'name' => $this->l('Doar erori')),
                                array('id' => 'info', 'name' => $this->l('Informatii')),
                                array('id' => 'debug', 'name' => $this->l('Depanare')),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Perioada pastrare jurnale (zile)'),
                        'name' => self::CONFIG_LOG_RETENTION,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Numar maxim versiuni istoric'),
                        'name' => self::CONFIG_HISTORY_MAX,
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Salveaza configurarea'),
                    'class' => 'btn btn-default pull-right',
                ),
                'buttons' => array(
                    array(
                        'type' => 'submit',
                        'name' => 'submitIiromanestiAiSeoConnectionTest',
                        'title' => $this->l('Testeaza conexiunea API'),
                        'icon' => 'process-icon-refresh',
                        'class' => 'pull-left',
                    ),
                ),
            ),
        );
    }

    private function getConfigurationValues()
    {
        return array(
            self::CONFIG_ENABLED => (int) Configuration::get(self::CONFIG_ENABLED),
            self::CONFIG_PROVIDER => Configuration::get(self::CONFIG_PROVIDER),
            self::CONFIG_MODEL => Configuration::get(self::CONFIG_MODEL),
            self::CONFIG_API_KEY => '',
            self::CONFIG_TIMEOUT => (int) Configuration::get(self::CONFIG_TIMEOUT),
            self::CONFIG_LOG_LEVEL => Configuration::get(self::CONFIG_LOG_LEVEL),
            self::CONFIG_LOG_RETENTION => (int) Configuration::get(self::CONFIG_LOG_RETENTION),
            self::CONFIG_HISTORY_MAX => (int) Configuration::get(self::CONFIG_HISTORY_MAX),
        );
    }

    private function processConfigurationForm()
    {
        $errors = $this->validateConfigurationInput();
        if (!empty($errors)) {
            $this->moduleLogger->error('Salvarea configurarii a esuat: date invalide.');
            return $this->displayError(implode('<br>', $errors));
        }

        Configuration::updateValue(self::CONFIG_ENABLED, (int) Tools::getValue(self::CONFIG_ENABLED));
        Configuration::updateValue(self::CONFIG_PROVIDER, Tools::getValue(self::CONFIG_PROVIDER));
        Configuration::updateValue(self::CONFIG_MODEL, trim((string) Tools::getValue(self::CONFIG_MODEL)));

        $apiKey = trim((string) Tools::getValue(self::CONFIG_API_KEY));
        if ($apiKey !== '') {
            Configuration::updateValue(self::CONFIG_API_KEY, $apiKey);
        }

        Configuration::updateValue(self::CONFIG_TIMEOUT, (int) Tools::getValue(self::CONFIG_TIMEOUT));
        Configuration::updateValue(self::CONFIG_LOG_LEVEL, Tools::getValue(self::CONFIG_LOG_LEVEL));
        Configuration::updateValue(self::CONFIG_LOG_RETENTION, (int) Tools::getValue(self::CONFIG_LOG_RETENTION));
        Configuration::updateValue(self::CONFIG_HISTORY_MAX, (int) Tools::getValue(self::CONFIG_HISTORY_MAX));

        $this->moduleLogger->info('Configurarea modulului a fost salvata.');

        return $this->displayConfirmation($this->l('Configurarea a fost salvata cu succes.'));
    }

    private function processConnectionTest()
    {
        $errors = $this->validateConfigurationInput(false);
        if (!empty($errors)) {
            $this->moduleLogger->error('Testul de conexiune a fost oprit: configurare invalida.');
            return $this->displayError(implode('<br>', $errors));
        }

        $client = new IiromanestiAiSeoApiClient(
            Tools::getValue(self::CONFIG_PROVIDER, Configuration::get(self::CONFIG_PROVIDER)),
            trim((string) Tools::getValue(self::CONFIG_API_KEY, Configuration::get(self::CONFIG_API_KEY))),
            trim((string) Tools::getValue(self::CONFIG_MODEL, Configuration::get(self::CONFIG_MODEL))),
            (int) Tools::getValue(self::CONFIG_TIMEOUT, Configuration::get(self::CONFIG_TIMEOUT))
        );

        try {
            $client->testConnection();
            $this->moduleLogger->info('Test conexiune API reusit.');
            return $this->displayConfirmation($this->l('Conexiunea API a fost testata cu succes.'));
        } catch (Exception $exception) {
            $this->moduleLogger->error('Test conexiune API esuat: ' . $exception->getMessage());
            return $this->displayError($this->l('Testul conexiunii API a esuat. Verificati furnizorul, cheia API, modelul si timeout-ul.'));
        }
    }

    private function validateConfigurationInput($apiKeyRequired = false)
    {
        $errors = array();
        $enabled = Tools::getValue(self::CONFIG_ENABLED);
        $provider = Tools::getValue(self::CONFIG_PROVIDER, Configuration::get(self::CONFIG_PROVIDER));
        $model = trim((string) Tools::getValue(self::CONFIG_MODEL, Configuration::get(self::CONFIG_MODEL)));
        $apiKey = trim((string) Tools::getValue(self::CONFIG_API_KEY, Configuration::get(self::CONFIG_API_KEY)));
        $timeout = (int) Tools::getValue(self::CONFIG_TIMEOUT, Configuration::get(self::CONFIG_TIMEOUT));
        $logLevel = Tools::getValue(self::CONFIG_LOG_LEVEL, Configuration::get(self::CONFIG_LOG_LEVEL));
        $logRetention = (int) Tools::getValue(self::CONFIG_LOG_RETENTION, Configuration::get(self::CONFIG_LOG_RETENTION));
        $historyMax = (int) Tools::getValue(self::CONFIG_HISTORY_MAX, Configuration::get(self::CONFIG_HISTORY_MAX));

        if (!in_array((int) $enabled, array(0, 1), true)) {
            $errors[] = $this->l('Valoarea pentru activarea modulului este invalida.');
        }
        if (!in_array($provider, array('openai'), true)) {
            $errors[] = $this->l('Furnizorul AI selectat este invalid.');
        }
        if ($model === '' || !Validate::isGenericName($model) || Tools::strlen($model) > 128) {
            $errors[] = $this->l('Modelul AI este obligatoriu si trebuie sa aiba cel mult 128 de caractere valide.');
        }
        if ($apiKeyRequired && $apiKey === '') {
            $errors[] = $this->l('Cheia API este obligatorie pentru testul de conexiune.');
        }
        if ($apiKey !== '' && Tools::strlen($apiKey) > 512) {
            $errors[] = $this->l('Cheia API trebuie sa aiba cel mult 512 caractere.');
        }
        if ($timeout < 5 || $timeout > 120) {
            $errors[] = $this->l('Timeout-ul trebuie sa fie intre 5 si 120 de secunde.');
        }
        if (!in_array($logLevel, array('error', 'info', 'debug'), true)) {
            $errors[] = $this->l('Nivelul jurnalului este invalid.');
        }
        if ($logRetention < 1 || $logRetention > 365) {
            $errors[] = $this->l('Perioada de pastrare a jurnalelor trebuie sa fie intre 1 si 365 de zile.');
        }
        if ($historyMax < 1 || $historyMax > 100) {
            $errors[] = $this->l('Numarul maxim de versiuni istoric trebuie sa fie intre 1 si 100.');
        }

        return $errors;
    }

    private function canConfigure()
    {
        if (!isset($this->context->employee) || !$this->context->employee->id) {
            return false;
        }

        if (method_exists($this->context->employee, 'can')) {
            return (bool) $this->context->employee->can('edit', 'AdminModules');
        }

        if (method_exists('Tab', 'checkTabRights')) {
            return (bool) Tab::checkTabRights('AdminModules');
        }

        return true;
    }
}
