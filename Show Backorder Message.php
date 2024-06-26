add_action('wp_footer', 'custom_backorder_script');


// show back order pop-up
function custom_backorder_script() {
    if (is_product()) {
        ?>
        <script>
            jQuery(document).ready(function($) {
                $('.single_add_to_cart_button').on('click', function(event) {
                    var isBackorder = $(this).data('backorder');
                    var outOfStockItems = $(this).data('out-of-stock-items');
                    
                    if (isBackorder) {
                        event.preventDefault(); // Prevent the default action
                        
                        // Show the modal
                        $('body').append('<div id="backorder-modal" class="modal"><div class="modal-content"><span class="close">&times;</span><p>The following items are out of stock: ' + outOfStockItems + '. You may proceed with placing your order, but please anticipate a delay in shipping. We appreciate your patience and understanding.</p></div></div>');

                        // Style the modal
                        $('.modal').css({
                            display: 'block',
                            position: 'fixed',
                            zIndex: '999',
                            paddingTop: '60px',
                            left: '0',
                            top: '0',
                            width: '100%',
                            height: '100%',
                            overflow: 'auto',
                            backgroundColor: 'rgb(0,0,0)',
                            backgroundColor: 'rgba(0,0,0,0.4)'
                        });
                        $('.modal-content').css({
                            backgroundColor: '#fefefe',
                            margin: '5% auto',
                            padding: '20px',
                            border: '1px solid #888',
							borderRadius: '6px',
                            width: '420px'
                        });
                        $('.close').css({
                            color: '#aaa',
                            float: 'right',
                            fontSize: '28px',
                            fontWeight: 'bold'
                        });

                        // Close the modal
                        $('.close').on('click', function() {
                            $('#backorder-modal').remove();
                        });
                        $(window).on('click', function(event) {
                            if ($(event.target).is('#backorder-modal')) {
                                $('#backorder-modal').remove();
                            }
                        });
                    }
                });
            });
        </script>
        <?php
    }
}

