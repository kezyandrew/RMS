<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title">{{ $t("Kitchen View") }} <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{ $t("Orders") }}</span> <span v-if="processing == true"><i class='fa fa-circle-notch fa-spin'></i> Loading..</span></span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="d-flex">

                        <div class="custom-control custom-switch ml-3 mr-3 mt-1">
                            <input type="checkbox" class="custom-control-input" id="auto_load_switch" v-model="auto_refresh">
                            <label class="custom-control-label" for="auto_load_switch">{{ $t("Auto Refresh Every 1 Min") }}</label>
                        </div>
                        
                        <button class="btn btn-light" v-on:click="load_kot_list">{{ $t("Refresh") }}</button>
                    </div>
                </div>
            </div>
            
            <p v-if="server_errors" v-html="server_errors" v-bind:class="[error_class]"></p>
            
            <div class="d-flex flex-row mb-3">

                <div class="col-md-12">

                    <div class="d-flex justify-content-center mb-3" v-if="list_populated == true">
                        <input type="text" name="filter_order_no" v-model="filter_order_no" class="form-control form-control-lg col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12" :placeholder="$t('Search by Order No / Table')"  autocomplete="off">
                    </div>

                    <div class="row flex-nowrap kitchen-wrapper" v-bind:class="{ 'bg-light': list_populated }">

                        <div class="d-flex flex-column mb-4 mt-4 ml-4 mr-4 p-0 bg-white col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-8 kitchen-card" v-for="(kot_list_item, kot_list_key, index) in kot_list_filtered" v-bind:key="index">
                            <div class="p-0 border-bottom">
                                <div class="d-flex flex-wrap p-3">
                                    <span class="mr-auto text-subtitle text-black-50">
                                        Order # {{ kot_list_item.order_number }}
                                    </span>
                                    <span><span class="timer-dot mr-2"></span> {{ kot_list_item.duration }} Minute</span>
                                </div>
                            </div>
                            <div class="p-0 border-bottom">
                                <div class="d-flex justify-content-between p-3">
                                    <div class="" v-if="kot_list_item.order_type_data != null"><i v-show="kot_list_item.order_type_data != null" v-bind:class="kot_list_item.order_type_data.icon"></i> {{ kot_list_item.order_type }}</div><div v-else></div>
                                    
                                    <div v-if="change_kitchen_order_status == true">
                                        <div class="" v-show="kot_list_item.kitchen_status_processing == true"><i class='fa fa-circle-notch fa-spin'></i>&nbsp;Changing Status</div>
                                        <div class="dropdown" v-show="kot_list_item.kitchen_status_processing == null || kot_list_item.kitchen_status_processing == false">
                                            <button class="btn btn-link text-decoration-none dropdown-toggle text-bold p-0" id="user_menu_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span v-if="kot_list_item.kitchen_status != null">
                                                    <span v-bind:class="kot_list_item.kitchen_status.color">{{ kot_list_item.kitchen_status.label }}</span>
                                                </span>
                                                <span v-else>Update Status</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right mt-2" aria-labelledby="user_menu_dropdown" >
                                                <button class="dropdown-item" type="button" v-for="(kitchen_status, key, ks_index) in kitchen_statuses" v-bind:key="ks_index" v-on:click="update_kitchen_status(kot_list_item.slack, kitchen_status.value_constant, kot_list_key)">{{ kitchen_status.label }}</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="p-0 border-bottom" v-show="kot_list_item.table != ''">
                                <div class="d-flex justify-content-center p-3">
                                    <div class="">{{ kot_list_item.table }}</div>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="d-flex flex-wrap pl-3 pt-3 pr-3">
                                    <span class="mr-auto text-subtitle text-black-50">Items</span>
                                    <span class="text-subtitle text-black-50">Qty</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 p-3 border-bottom" v-bind:class="((item_list_value.parent_order_product == false)?'ml-3':'')" v-for="(item_list_value, key, item_index) in kot_list_item.products" v-bind:key="item_index">
                                    <span v-on:click="mark_prepared(item_list_value.slack, kot_list_key, key)">
                                        <span v-show="item_list_value.item_status_loading == true"><i class='fa fa-circle-notch fa-spin kitchen-item-checker '></i></span>
                                        <i v-show="item_list_value.item_status_loading == null || item_list_value.item_status_loading == false" class="kitchen-item-checker cursor"  v-bind:class="{ 'text-success fas fa-check-circle': item_list_value.is_ready_to_serve == 1, 'text-muted far fa-check-circle': item_list_value.is_ready_to_serve == 0}"></i>
                                    </span>
                                    <span class="text-break kitchen-item-title">
                                    <span v-if="item_list_value.parent_order_product == false" class="label blue-label addon-label">Add-on</span>
                                    <div>{{ item_list_value.name }}</div>
                                    </span>
                                    <span class="text-break">{{ item_list_value.quantity }}</span>
                                </div>
                            </div>

                            <div class="p-0" v-if="pos_order_edit == true">
                                <div class="d-flex justify-content-center p-2" v-if="kot_list_item.status.constant != 'CLOSED'" >
                                    <a v-bind:href="kot_list_item.edit_link" class="text-bold text-decoration-none" target="_blank"><i class="fas fa-cash-register mr-2"></i> {{ $t("Bill or Edit This Order") }}</a>
                                </div>
                                <div class="d-flex justify-content-center p-2" v-if="kot_list_item.kitchen_status != null && kot_list_item.kitchen_status.constant == 'ORDER_READY'">
                                    <span class="text-danger text-bold cursor" v-on:click="dismiss_order(kot_list_item.slack)"><i class="far fa-times-circle"></i> {{ $t("Dismiss This Order From Kitchen") }}</span>
                                </div>
                            </div>

                        </div>

                        <div v-if="kot_list.length == 0 && processing == false">
                            <span>Zero orders in queue!</span>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                Are you sure you want to dismiss this order from kitchen?
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>
    </div>
</template>  

<script>
    'use strict';
    
    import moment from "moment";

    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,

                kot_list        : [],

                total_orders     : 0,
                list_populated : false,

                filter_order_no  : '',

                auto_refresh     : true,

                addon_label     : '+ ',

                show_modal      : false,

                dismiss_order_api_link: '/api/toggle_order_dismissed_from_screen_status'
            }
        },
        props: {
            kitchen_statuses: [Array, Object],
            change_kitchen_order_status: Boolean,
            pos_order_edit: Boolean,
            store_slack: String
        },
        computed: {
            kot_list_filtered(){
                if(this.filter_order_no){
                    return this.kot_list.filter((kot_list_item)=>{
                        return this.filter_order_no.toLowerCase().split(' ').every(v => kot_list_item.order_number.toLowerCase().includes(v) || kot_list_item.customer_phone.toLowerCase().includes(v) || kot_list_item.customer_email.toLowerCase().includes(v) || kot_list_item.table.toLowerCase().includes(v))
                    })
                }else{
                    return this.kot_list;
                }
            }
        },
        mounted() {
            console.log('Kitchen order ticket page loaded');
            this.tick_update_duration_for_products();
            this.tick_update_kot_list();
        },
        created(){
            this.load_kot_list();
            console.log(`new-order.${this.store_slack}`);
        },
        methods: {
            load_kot_list(){
                this.processing = true;

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);

                axios.post('/api/get_in_kitchen_order_list', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.kot_list = response.data.data;
                        this.update_duration_for_products();
                        this.processing = false;
                        this.list_populated = (this.kot_list.length > 0)?true:false;
                        this.total_orders = this.kot_list.length;
                    }else{
                        this.processing = false;
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    };
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            update_kitchen_status(order_slack, kitchen_status, item_key){
            
                this.$set(this.kot_list[item_key], 'kitchen_status_processing', true);

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("order_slack", order_slack);
                formData.append("kitchen_status", kitchen_status);

                axios.post('/api/update_kitchen_order_status', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.$set(this.kot_list[item_key], 'kitchen_status_processing', false);
                        this.$set(this.kot_list, item_key, response.data.data.order_data);
                    }else{
                        this.$set(this.kot_list[item_key], 'kitchen_status_processing', false);
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    };
                })
                .catch((error) => {
                    console.log(error);
                });

            },

            calculate_duration(created_date){
                var moment = require('moment-timezone');
                var tz = moment.tz.guess();

                var today = moment();
                var date_obj = new Date(created_date);
                var moment_obj = moment.unix(date_obj).tz(tz);

                var duration = moment.duration(today.diff(moment_obj));
                var minutes = Math.abs(Math.round(duration.as('minutes')));
                minutes = (isNaN(minutes))?'-':minutes;
                return minutes;
            },

            update_duration_for_products(){
                for(var i = 0; i < this.kot_list.length; i++){   
                    var duration = this.calculate_duration(this.kot_list[i].create_at_utc);
                    this.$set(this.kot_list[i], 'duration', duration);
                }
            },

            mark_prepared(slack, index, item_index){

                this.$set(this.kot_list[index]['products'][item_index], 'item_status_loading', true);

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("item_slack", slack);
                axios.post('/api/update_kitchen_item_status', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.$set(this.kot_list, index, response.data.data.order_data);
                        this.$set(this.kot_list[index]['products'][item_index], 'item_status_loading', false);
                    }else{
                        this.$set(this.kot_list[index]['products'][item_index], 'item_status_loading', false);
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    };
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            dismiss_order(slack){

                this.$off("submit");
                this.$off("close");

                this.show_modal = true;
                this.$on("submit",function () {
                    
                    this.processing = true;
                    var formData = new FormData();

                    formData.append("access_token", window.settings.access_token);
                    formData.append("order_slack", slack);
                    formData.append("screen", 'KITCHEN_SCREEN');

                    axios.post(this.dismiss_order_api_link, formData).then((response) => {
                        if(response.data.status_code == 200) {
                            this.show_response_message(response.data.msg, 'SUCCESS');
                        
                            this.load_kot_list();

                            this.show_modal = false;
                            this.processing = false;
                        }else{
                            this.show_modal = false;
                            this.processing = false;
                            try{
                                var error_json = JSON.parse(response.data.msg);
                                this.loop_api_errors(error_json);
                            }catch(err){
                                this.server_errors = response.data.msg;
                            }
                            this.error_class = 'error';
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });
                
                this.$on("close",function () {
                    this.show_modal = false;
                });
            },

            tick_update_duration_for_products(){
                setInterval(() => {
                    this.update_duration_for_products();
                }, 1000);
            },

            tick_update_kot_list(){
                setInterval(() => {
                    if(this.auto_refresh == true){
                        this.load_kot_list();
                    }
                }, 60000);
            },
        }
    }
</script>