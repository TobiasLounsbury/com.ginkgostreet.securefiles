<table class="hiddenElement" id="securefiles-throwaway">
    <tbody>
    <tr class="securefiles-custom-field-enabled-section">
        <td class="label">{$form.use_securefiles.label} {help id="securefiles-field-help" file="CRM/Securefiles/Form/CustomField.hlp"}</td>
        <td class="html-adjust">{$form.use_securefiles.html}
            <span class="description">{ts}Store this file using Secure File Storage? (This functionality is provided by SecureFiles.){/ts}</span>
        </td>
    </tr>
    <tr class="securefiles-custom-field-settings">
        <td colspan="2">
            <div class="securefiles-custom-field-settings-container">
                {foreach from=$secureFileElements item=elementName}
                    <div class="crm-section {$elementName}-section">
                        <div class="label">{$form.$elementName.label}</div>
                        <div class="content">{$form.$elementName.html}</div>
                        <div class="clear"></div>
                    </div>
                {/foreach}
            </div>
        </td>
    </tr>
    </tbody>
</table>