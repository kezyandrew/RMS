<template>
    <div class="">
        <div class="col-md-12 pl-0 pr-0">

            <div class="d-flex flex-wrap p-4 border-bottom">
                <div class="d-flex">
                    <div class="mr-4">
                        <span class="text-title"> {{ $t("Register") }} {{ register_data_basic.billing_counter['billing_counter_code'] }} - {{ register_data_basic.billing_counter['counter_name'] }}</span>
                    </div>
                </div>
                <div class="ml-auto">
                    <a :href="new_order_link" class="btn btn-lg btn-primary ml-3" v-if="new_order_access == true">+ {{ $t("New Order") }}</a>
                </div>
            </div>
            
            <div class="d-flex mb-4">
                <iframe class="pdf_iframe" title="Register Summary Print PDF" :src="pdf_print"></iframe>
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
                
                slack           : this.register_data.slack,
                register_data_basic : this.register_data,
            }
        },
        props: {
            register_data: [Array, Object],
            pdf_print: String,
            new_order_link: String,
            new_order_access: Boolean,
        },
        mounted() {
            console.log('Register summary page loaded');
        },
        methods: {
           
        }
    }
</script>