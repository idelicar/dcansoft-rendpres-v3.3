jQuery(document).ready(function($) {
    $('#import-btn').on('click', function() {
        const url = $('#product-url').val();
        if(!url) return;
        
        $('.progress-fill').css('width', '30%');
        
        $.ajax({
            url: dcansoft.ajax_url,
            type: 'POST',
            data: {
                action: 'dcansoft_import',
                security: dcansoft.nonce,
                action_type: 'import_product',
                product_url: url
            },
            success: function(response) {
                $('.progress-fill').css('width', '100%');
                
                if(response.success) {
                    $('.preview-container').html(response.data.preview);
                    $('.log-container').prepend(
                        `<div class="log-item success">` +
                        `${new Date().toLocaleString()} - Ürün eklendi (ID: ${response.data.product_id})` +
                        `</div>`
                    );
                    
                    // 2 saniye sonra progress barı sıfırla
                    setTimeout(() => {
                        $('.progress-fill').css('width', '0%');
                    }, 2000);
                }
            }
        });
    });
});