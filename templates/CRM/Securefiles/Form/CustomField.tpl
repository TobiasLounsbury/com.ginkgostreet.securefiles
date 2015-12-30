<table class="hiddenElement" id="securefiles-throwaway">
    <tbody>
    <tr class="securefiles-custom-field-enabled-section">
        <td class="label">{$form.is_slider_widget.label} {help id="volunteer-slider" file="Slider/CRM/Custom/Form/Field.hlp"}</td>
        <td class="html-adjust">{$form.is_slider_widget.html}
            <span class="description">{ts}Display option set as a slider? (This functionality is provided by CiviVolunteer.){/ts}</span>
        </td>
    </tr>
    <tr class="securefiles-backend-service-settings-section">
        <td colspan="2">
            {foreach from=$secureFileElements item=elementName}
                <div class="crm-section {$elementName}-section">
                    <div class="label">{$form.$elementName.label}</div>
                    <div class="content">{$form.$elementName.html}</div>
                    <div class="clear"></div>
                </div>
            {/foreach}
        </td>
    </tr>
    </tbody>
</table>