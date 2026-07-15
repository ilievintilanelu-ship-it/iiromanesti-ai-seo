{*
* 2007-2026 PrestaShop
*}

<div class="panel iiromanesti-ai-seo-product-audit-box">
    <div class="panel-heading">
        <i class="icon-search"></i>
        {l s='Audit SEO produs' mod='iiromanesti_ai_seo'}
    </div>
    <p class="help-block">
        {l s='Analiza este doar informativa. Modulul nu modifica produsul si nu genereaza continut AI.' mod='iiromanesti_ai_seo'}
    </p>
    <a class="btn btn-primary" href="{$iiro_audit_url|escape:'html':'UTF-8'}">
        <i class="icon-line-chart"></i>
        {l s='Analizeaza produs' mod='iiromanesti_ai_seo'}
    </a>
</div>

<div class="panel iiromanesti-ai-seo-generator-box">
    <div class="panel-heading">
        <i class="icon-magic"></i>
        {l s='AI Generator' mod='iiromanesti_ai_seo'}
    </div>

    <div class="alert alert-warning">
        {l s='Infrastructura este pregatita doar pentru previzualizare. Produsul nu este modificat, nu se salveaza nimic in baza de date si nu se apeleaza OpenAI.' mod='iiromanesti_ai_seo'}
    </div>

    <h4>{l s='Campuri disponibile pentru generare' mod='iiromanesti_ai_seo'}</h4>
    <div class="row">
        {foreach from=$iiro_ai_generator_fields key=field_key item=field_label}
            <div class="col-md-4 col-sm-6">
                <div class="checkbox">
                    <label for="iiro_ai_field_{$field_key|escape:'html':'UTF-8'}">
                        <input type="checkbox" id="iiro_ai_field_{$field_key|escape:'html':'UTF-8'}" name="iiro_ai_generator_fields[]" value="{$field_key|escape:'html':'UTF-8'}" />
                        {$field_label|escape:'html':'UTF-8'}
                    </label>
                </div>
            </div>
        {/foreach}
    </div>

    <button type="button" class="btn btn-primary" id="iiro-ai-generator-button">
        <i class="icon-magic"></i>
        {l s='Genereaza continut AI' mod='iiromanesti_ai_seo'}
    </button>

    <div class="alert alert-info iiro-ai-generator-message" id="iiro-ai-generator-message" style="display:none; margin-top:15px;">
        {$iiro_ai_ready_message|escape:'html':'UTF-8'}
    </div>

    <hr />

    <h4>{l s='Preview Panel' mod='iiromanesti_ai_seo'}</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>{l s='Titlu actual' mod='iiromanesti_ai_seo'}</th>
                    <td>{if isset($iiro_ai_product_context.existing_description.name)}{$iiro_ai_product_context.existing_description.name|escape:'html':'UTF-8'}{/if}</td>
                </tr>
                <tr>
                    <th>{l s='Titlu propus' mod='iiromanesti_ai_seo'}</th>
                    <td><em>{$iiro_ai_pending_text|escape:'html':'UTF-8'}</em></td>
                </tr>
                <tr>
                    <th>{l s='Descriere actuala' mod='iiromanesti_ai_seo'}</th>
                    <td>{if isset($iiro_ai_product_context.existing_description.description_short)}{$iiro_ai_product_context.existing_description.description_short|strip_tags|truncate:300:'...'|escape:'html':'UTF-8'}{/if}</td>
                </tr>
                <tr>
                    <th>{l s='Descriere propusa' mod='iiromanesti_ai_seo'}</th>
                    <td><em>{$iiro_ai_pending_text|escape:'html':'UTF-8'}</em></td>
                </tr>
                <tr>
                    <th>{l s='Meta actual' mod='iiromanesti_ai_seo'}</th>
                    <td>
                        <strong>{l s='Meta Title' mod='iiromanesti_ai_seo'}:</strong>
                        {if isset($iiro_ai_product_context.existing_meta.meta_title)}{$iiro_ai_product_context.existing_meta.meta_title|escape:'html':'UTF-8'}{/if}<br />
                        <strong>{l s='Meta Description' mod='iiromanesti_ai_seo'}:</strong>
                        {if isset($iiro_ai_product_context.existing_meta.meta_description)}{$iiro_ai_product_context.existing_meta.meta_description|escape:'html':'UTF-8'}{/if}
                    </td>
                </tr>
                <tr>
                    <th>{l s='Meta propus' mod='iiromanesti_ai_seo'}</th>
                    <td><em>{$iiro_ai_pending_text|escape:'html':'UTF-8'}</em></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    (function () {
        var button = document.getElementById('iiro-ai-generator-button');
        var message = document.getElementById('iiro-ai-generator-message');

        if (button && message) {
            button.onclick = function () {
                message.style.display = 'block';
            };
        }
    }());
</script>
