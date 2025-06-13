
function setupDynamicUpdate(selectId, targetInputId, dataAttribute) {
    const selectElement = document.getElementById(selectId);
    const targetInput = document.getElementById(targetInputId);

    if (selectElement && targetInput) {
        selectElement.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const value = selectedOption.getAttribute(`data-${dataAttribute}`);
            targetInput.value = value || '';
        });
    }
}

// Initialize the function after DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    setupDynamicUpdate('alizeti_id', 'al_kilogram', 'al_kilogram');
    //setupDynamicUpdate('alizeti_id', 'mafu_machafu', 'mafu_machafu');
    //setupDynamicUpdate('alizeti_id', 'mafu_masafi', 'mafu_masafi');
    setupDynamicUpdate('alizeti_id', 'data-price-litre', 'price_per_litre');
    setupDynamicUpdate('alizeti_id', 'data-price-20litre', 'price_per_20_litre');
    setupDynamicUpdate('alizeti_id', 'data-shudu', 'shudu');
    
});
