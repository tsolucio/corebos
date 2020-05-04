<section role="dialog" tabindex="-1" class="slds-modal slds-fade-in-open slds-modal_medium" aria-labelledby="modal-heading-01" aria-modal="true" aria-describedby="modal-content-id-1">
    <div class="slds-modal__container">
        <header class="slds-modal__header">
            <button class="slds-button slds-button_icon slds-modal__close slds-button_icon-inverse" title="Close" onclick="ModuleBuilder.closeModal()">
                <svg class="slds-button__icon slds-button__icon_large" aria-hidden="true">
                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#close"></use>
                </svg>
                <span class="slds-assistive-text">{$MOD.LBL_MB_CLOSE}</span>
            </button>
            <h2 id="modal-heading-01" class="slds-modal__title slds-hyphenate">{$MOD.LBL_MB_LISTMODULES}</h2>
        </header>
        <div class="slds-modal__content slds-p-around_medium" id="modal-content-id-1">
            <table class="slds-table slds-table_cell-buffer slds-table_bordered">
                <thead>
                    <tr class="slds-line-height_reset">
                        <th class="" scope="col">
                            <div class="slds-truncate">{$MOD.LBL_MB_MODULENAME}</div>
                        </th>
                        <th class="" scope="col">
                            <div class="slds-truncate">{$MOD.LBL_MB_DATECREATED}</div>
                        </th>
                        <th class="" scope="col">
                            <div class="slds-truncate">{$MOD.LBL_MB_STATUS}</div>
                        </th>
                        <th class="" scope="col">
                            <div class="slds-truncate">{$MOD.LBL_MB_EXPORT}</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$MODULELISTS item=i key=k}
                    <tr class="slds-hint-parent">
                        <td data-label="Module Name" style="width: 25%">
                            <div class="slds-truncate">
                                {$i['modulebuilder_name']}
                            </div>
                        </td>
                        <td data-label="Date created" style="width: 25%">
                            <div class="slds-truncate">
                                {$i['date']}
                            </div>
                        </td>
                        <td data-label="Completed" style="width: 25%">
                            <div class="slds-truncate">
                                {if is_numeric($i['completed'])}
                                <span class="slds-icon_container slds-icon-standard-quip" style="color: white">
                                  {$i['completed']}%
                                </span> 
                                {else}
                                <span class="slds-icon_container slds-icon-standard-task" style="color: white">
                                  <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                    <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#task"></use>
                                  </svg>
                                  {$MOD.LBL_MB_COMPLETED}
                                </span>
                                {/if}
                            </div>
                        </td>
                        <td data-label="Export">
                            <div class="slds-truncate">
                                {if is_numeric($i['completed'])}
                                <button class="slds-button slds-button_neutral slds-button_dual-stateful" aria-live="assertive">
                                    <span class="slds-text-not-pressed">
                                    <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#edit"></use>
                                    </svg>{$MOD.LBL_MB_STARTEDITING}</span>
                                </button>
                                {else}
                                <button class="slds-button slds-button_brand" aria-live="assertive">
                                    <span class="slds-text-not-pressed">
                                    <svg class="slds-button__icon slds-button__icon_small slds-button__icon_left" aria-hidden="true">
                                      <use xlink:href="include/LD/assets/icons/utility-sprite/svg/symbols.svg#download"></use>
                                    </svg>{$MOD.LBL_MB_EXPORT}</span>
                                </button>
                                {/if}
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</section>
<div class="slds-backdrop slds-backdrop_open"></div>