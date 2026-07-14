<?php
/**
 * Logger pentru modulul iiRomanesti AI SEO.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class IiromanestiAiSeoLogger
{
    /** @var Module */
    private $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    public function info($message)
    {
        $this->log($message, 1);
    }

    public function error($message)
    {
        $this->log($message, 3);
    }

    public function debug($message)
    {
        if (Configuration::get(Iiromanesti_Ai_Seo::CONFIG_LOG_LEVEL) === 'debug') {
            $this->log($message, 1);
        }
    }

    private function log($message, $severity)
    {
        $safeMessage = preg_replace('/(sk-[A-Za-z0-9_\-]{8,})/', '[cheie-api-ascunsa]', (string) $message);
        PrestaShopLogger::addLog(
            '[' . $this->module->displayName . '] ' . $safeMessage,
            (int) $severity,
            null,
            'Module',
            (int) $this->module->id,
            true
        );
    }
}
