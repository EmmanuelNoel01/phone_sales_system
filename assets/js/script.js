document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation
    document.querySelectorAll('.needs-validation').forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Dynamic price calculation for swaps
    const calculateSwapDifference = () => {
        const returnSelect = document.querySelector('select[name="return_id"]');
        const newPhoneSelect = document.querySelector('select[name="new_phone_id"]');
        const topUpInput = document.querySelector('input[name="top_up_amount"]');
        const diffDisplay = document.getElementById('price-difference');
        
        if (returnSelect && newPhoneSelect && topUpInput && diffDisplay) {
            const returnOption = returnSelect.options[returnSelect.selectedIndex];
            const newPhoneOption = newPhoneSelect.options[newPhoneSelect.selectedIndex];
            
            if (returnOption.value && newPhoneOption.value) {
                const oldPrice = parseFloat(returnOption.dataset.price);
                const newPrice = parseFloat(newPhoneOption.dataset.price);
                const topUp = parseFloat(topUpInput.value) || 0;
                const difference = newPrice - oldPrice + topUp;
                
                diffDisplay.value = difference >= 0 
                    ? `Customer pays: $${difference.toFixed(2)}` 
                    : `Refund to customer: $${Math.abs(difference).toFixed(2)}`;
            }
        }
    };

    document.querySelectorAll('[data-price-calc]').forEach(el => {
        el.addEventListener('change', calculateSwapDifference);
    });
});