<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

<div class="crm-section securefiles_backend_service-section">
    <div class="label">{$form.securefiles_backend_service.label}</div>
    <div class="content">{$form.securefiles_backend_service.html}</div>
    <div class="clear"></div>
</div>
<div class="securefiles-backend-service-settings-section">
    {foreach from=$elementNames item=elementName}
      <div class="crm-section {$elementName}-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
    {/foreach}
</div>
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
