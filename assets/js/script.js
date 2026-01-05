document.addEventListener('DOMContentLoaded', function() {

    const qtyInput = document.querySelector('.qty-input');
    const btnBuy = document.querySelector('.btn-buy');
    
    if(qtyInput && btnBuy) {
        const priceElement = document.querySelector('.ticket-price');
        
        const basePrice = parseInt(priceElement.textContent.replace(/[^0-9]/g, ''));
        
        qtyInput.addEventListener('input', function() {
            let qty = parseInt(this.value);
            
            if(isNaN(qty) || qty < 1) { 
                qty = 1; 
            }
            
            let total = basePrice * qty;
            
            let formattedTotal = "Rp " + total.toLocaleString('id-ID');
            
            btnBuy.innerHTML = `Beli Tiket (${formattedTotal})`;
        });
    }
});