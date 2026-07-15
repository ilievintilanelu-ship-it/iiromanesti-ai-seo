{*
* 2007-2026 PrestaShop
*
* @author    iiRomanesti
* @copyright 2026 iiRomanesti
* @license   Academic Free License (AFL 3.0)
*}

<div class="panel iiromanesti-ai-seo-profiles">
    <div class="panel-heading">
        <i class="icon-list"></i>
        {l s='Profiluri AI' mod='iiromanesti_ai_seo'}
    </div>

    <div class="alert alert-info">
        {l s='Profilul este predefinit si disponibil doar pentru vizualizare in aceasta etapa. Nu modifica produse si nu apeleaza AI.' mod='iiromanesti_ai_seo'}
    </div>

    <table class="table">
        <tbody>
            <tr><th>{l s='Profil activ' mod='iiromanesti_ai_seo'}</th><td>{$active_profile.name|escape:'html':'UTF-8'} ({$active_profile.code|escape:'html':'UTF-8'})</td></tr>
            <tr><th>{l s='Magazin asociat' mod='iiromanesti_ai_seo'}</th><td>{$active_profile.shop|escape:'html':'UTF-8'}</td></tr>
            <tr><th>{l s='Limba' mod='iiromanesti_ai_seo'}</th><td>{$active_profile.language|escape:'html':'UTF-8'}</td></tr>
            <tr><th>{l s='Versiune profil' mod='iiromanesti_ai_seo'}</th><td>{$active_profile.version|escape:'html':'UTF-8'}</td></tr>
            <tr><th>{l s='Ultima modificare' mod='iiromanesti_ai_seo'}</th><td>{$active_profile.last_modified_at|escape:'html':'UTF-8'}</td></tr>
        </tbody>
    </table>

    <h3>{l s='Reguli principale' mod='iiromanesti_ai_seo'}</h3>
    {foreach from=$active_profile.rules key=section item=rules}
        <h4>{$section|escape:'html':'UTF-8'}</h4>
        <ul>
            {foreach from=$rules item=rule}
                <li>{$rule|escape:'html':'UTF-8'}</li>
            {/foreach}
        </ul>
    {/foreach}

    <h3>{l s='Istoric versiuni' mod='iiromanesti_ai_seo'}</h3>
    <table class="table">
        <thead><tr><th>{l s='Versiune' mod='iiromanesti_ai_seo'}</th><th>{l s='Data' mod='iiromanesti_ai_seo'}</th><th>{l s='Modificari' mod='iiromanesti_ai_seo'}</th></tr></thead>
        <tbody>
            {foreach from=$active_profile.history item=item}
                <tr><td>{$item.version|escape:'html':'UTF-8'}</td><td>{$item.date|escape:'html':'UTF-8'}</td><td>{$item.changes|escape:'html':'UTF-8'}</td></tr>
            {/foreach}
        </tbody>
    </table>
</div>
