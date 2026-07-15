{*
* 2007-2026 PrestaShop
*
* @author    iiRomanesti
* @copyright 2026 iiRomanesti
* @license   Academic Free License (AFL 3.0)
*}

<div class="panel iiromanesti-ai-seo-dashboard">
    <div class="panel-heading">
        <i class="icon-dashboard"></i>
        {l s='Dashboard iiRomanesti AI SEO' mod='iiromanesti_ai_seo'}
    </div>

    <div class="alert alert-info">
        {l s='Acest dashboard citeste statisticile direct din baza de date PrestaShop. Nu genereaza continut AI si nu modifica produse.' mod='iiromanesti_ai_seo'}
    </div>

    <div class="row">
        {foreach from=$dashboard_cards item=card}
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="panel iiromanesti-ai-seo-card">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-3 text-center">
                                <i class="{$card.icon|escape:'html':'UTF-8'} iiromanesti-ai-seo-card-icon"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="iiromanesti-ai-seo-card-value">{$card.value|intval}</div>
                                <div class="iiromanesti-ai-seo-card-label">{$card.label|escape:'html':'UTF-8'}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
</div>

<style>
    .iiromanesti-ai-seo-card .panel-body {
        min-height: 105px;
    }

    .iiromanesti-ai-seo-card-icon {
        color: #25b9d7;
        font-size: 34px;
        line-height: 64px;
    }

    .iiromanesti-ai-seo-card-value {
        color: #363a41;
        font-size: 32px;
        font-weight: 600;
        line-height: 38px;
    }

    .iiromanesti-ai-seo-card-label {
        color: #6c868e;
        font-size: 14px;
        margin-top: 6px;
    }
</style>
