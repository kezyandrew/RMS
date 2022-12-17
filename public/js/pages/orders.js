class Orders{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/orders',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'orders.order_number' },
                { name: 'orders.customer_phone' },
                { name: 'orders.customer_email' },
                { name: 'orders.total_order_amount' },
                { name: 'master_status.label' },
                { name: 'orders.created_at' },
                { name: 'orders.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 6, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [8] },
                {
                    "targets": [3],
                    "className": 'text-right'
                }
            ],
        });
    }
}