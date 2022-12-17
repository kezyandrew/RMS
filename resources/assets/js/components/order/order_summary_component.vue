<template>
    <div class="">
        <div class="col-md-12 pl-0 pr-0">

            <div class="d-flex flex-wrap p-4 border-bottom">
                <div class="d-flex">
                    <div class="mr-4 pt-1 d-none d-sm-block">
                        <span v-bind:class="order_basic.status.color"  class="mr-2">{{ order_basic.status.label }}</span>
                        <span v-if="order_basic.restaurant_mode == 1 && order_basic.kitchen_status != null" v-bind:class="order_basic.kitchen_status.color">{{ order_basic.kitchen_status.label }}</span>
                    </div>
                    <div class="mr-4">
                        <span class="text-title"> {{ $t("Order") }} #{{ order_basic.order_number }} </span>
                    </div>
                    <div class="mr-3">
                        <span class="text-title text-primary">{{ order_basic.currency_code }} {{ order_basic.total_order_amount }}</span>
                    </div>
                </div>
                <div class="ml-auto">
                    <a :href="edit_order_link" class="btn btn-outline-primary" v-if="edit_order_access == true && order_status != 'CLOSED'">{{ $t("Edit") }}</a>

                    <a :href="order_detail_link" class="btn btn-outline-primary ml-3" v-if="order_detail_access == true">{{ $t("Order Details") }}</a>

                    <a :href="print_order_link" class="btn btn-outline-primary ml-3" v-if="order_detail_access == true">{{ $t("PDF") }}</a>

                    <a :href="print_kot_link" class="btn btn-outline-primary ml-3" v-if="order_detail_access == true && print_kot_link != ''">{{ $t("KOT") }}</a>

                    <a :href="new_order_link" class="btn btn-lg btn-primary ml-3" v-if="new_order_access == true">+ {{ $t("New Order") }}</a>
                </div>
            </div>
            
            <div class="d-flex mb-4">
                <iframe class="pdf_iframe" title="Order Summary Print PDF" :src="pdf_print"></iframe>
            </div>
        </div>
    </div>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                order_processing: false,
                processing      : false,
                
                slack           : this.order_data.slack,
                order_basic     : this.order_data,

                order_status    : this.order_data.status.constant
            }
        },
        props: {
            order_data: [Array, Object],
            pdf_print: String,
            new_order_link: String,
            order_detail_link: String,
            order_detail_access: Boolean,
            new_order_access: Boolean,
            print_order_link: String,
            print_kot_link: String,
            edit_order_access: Boolean,
            edit_order_link: String
        },
        mounted() {
            console.log('Order summary page loaded');
        },
        methods: {
           
        }
    }
</script>