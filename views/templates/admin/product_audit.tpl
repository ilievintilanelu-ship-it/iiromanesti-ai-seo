{*
* 2007-2026 PrestaShop
*}

<div class="panel iiromanesti-ai-seo-product-audit">
    <div class="panel-heading">
        <i class="icon-search"></i>
        {l s='Audit SEO produs' mod='iiromanesti_ai_seo'}
    </div>

    <div class="alert alert-info">
        {l s='Raportul este doar informativ. Produsul nu este modificat, nu se apeleaza OpenAI si nu se genereaza continut AI.' mod='iiromanesti_ai_seo'}
    </div>

    {if isset($audit_report)}
        <h3>{$audit_report.product_name|escape:'html':'UTF-8'} (#{$audit_report.id_product|intval})</h3>

        <div class="row">
            <div class="col-md-4"><div class="panel"><div class="panel-body text-center"><strong>{l s='Scor Google' mod='iiromanesti_ai_seo'}</strong><div class="score">{$audit_report.scores.google|intval}/100</div></div></div></div>
            <div class="col-md-4"><div class="panel"><div class="panel-body text-center"><strong>{l s='Scor Conversie' mod='iiromanesti_ai_seo'}</strong><div class="score">{$audit_report.scores.conversion|intval}/100</div></div></div></div>
            <div class="col-md-4"><div class="panel"><div class="panel-body text-center"><strong>{l s='Scor Completitudine' mod='iiromanesti_ai_seo'}</strong><div class="score">{$audit_report.scores.completeness|intval}/100</div></div></div></div>
        </div>

        <h4>{l s='Indicatori analizati' mod='iiromanesti_ai_seo'}</h4>
        <table class="table">
            <tbody>
                <tr><th>{l s='Titlu' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.title_length|intval} {l s='caractere' mod='iiromanesti_ai_seo'}</td></tr>
                <tr><th>{l s='Descriere scurta' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.short_description_length|intval} {l s='caractere' mod='iiromanesti_ai_seo'}</td></tr>
                <tr><th>{l s='Descriere lunga' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.long_description_length|intval} {l s='caractere' mod='iiromanesti_ai_seo'}</td></tr>
                <tr><th>{l s='Meta title' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.meta_title_length|intval} {l s='caractere' mod='iiromanesti_ai_seo'}</td></tr>
                <tr><th>{l s='Meta description' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.meta_description_length|intval} {l s='caractere' mod='iiromanesti_ai_seo'}</td></tr>
                <tr><th>{l s='Imagini / fara ALT' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.image_count|intval} / {$audit_report.stats.images_without_alt|intval}</td></tr>
                <tr><th>{l s='Taguri' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.tag_count|intval}</td></tr>
                <tr><th>{l s='URL' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.url|escape:'html':'UTF-8'}</td></tr>
                <tr><th>{l s='Categoria' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.category|escape:'html':'UTF-8'}</td></tr>
                <tr><th>{l s='Lungime continut' mod='iiromanesti_ai_seo'}</th><td>{$audit_report.stats.content_words|intval} {l s='cuvinte' mod='iiromanesti_ai_seo'}</td></tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-6">
                <h4>{l s='Probleme detectate' mod='iiromanesti_ai_seo'}</h4>
                {if $audit_report.issues|count}<ul>{foreach from=$audit_report.issues item=issue}<li>{$issue|escape:'html':'UTF-8'}</li>{/foreach}</ul>{else}<p class="text-success">{l s='Nu au fost detectate probleme majore.' mod='iiromanesti_ai_seo'}</p>{/if}
            </div>
            <div class="col-md-6">
                <h4>{l s='Recomandari' mod='iiromanesti_ai_seo'}</h4>
                {if $audit_report.recommendations|count}<ul>{foreach from=$audit_report.recommendations item=recommendation}<li>{$recommendation|escape:'html':'UTF-8'}</li>{/foreach}</ul>{else}<p class="text-success">{l s='Produsul acopera criteriile de baza analizate.' mod='iiromanesti_ai_seo'}</p>{/if}
            </div>
        </div>

        <a class="btn btn-default" href="{$back_url|escape:'html':'UTF-8'}"><i class="icon-arrow-left"></i> {l s='Inapoi la produs' mod='iiromanesti_ai_seo'}</a>
    {/if}
</div>

<style>.iiromanesti-ai-seo-product-audit .score{font-size:34px;font-weight:600;color:#25b9d7;margin-top:10px;}</style>
