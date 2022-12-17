<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="store_slack == ''">{{ $t("Add Store") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Store") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name">{{ $t("Name") }}</label>
                        <input type="text" name="name" v-model="name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter store name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name') }">{{ errors.first('name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="store_code">{{ $t("Store Code") }}</label>
                        <input type="text" name="store_code" v-model="store_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" :placeholder="$t('Please enter store code')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('store_code') }">{{ errors.first('store_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_number">{{ $t("Tax Number or GST number") }}</label>
                        <input type="text" name="tax_number" v-model="tax_number" v-validate="'max:50'" class="form-control form-control-custom" :placeholder="$t('Please enter tax number or GST number')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('tax_number') }">{{ errors.first('tax_number') }}</span> 
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Contact Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="primary_contact">{{ $t("Primary Contact No.") }}</label>
                        <input type="text" name="primary_contact" v-model="primary_contact" v-validate="'min:10|max:15'" class="form-control form-control-custom" :placeholder="$t('Please enter primary contact number')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('primary_contact') }">{{ errors.first('primary_contact') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Secondary Contact No.") }}</label>
                        <input type="text" name="secondary_contact" v-model="secondary_contact" v-validate="'min:10|max:15'" class="form-control form-control-custom" :placeholder="$t('Please enter secondary contact number')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('secondary_contact') }">{{ errors.first('secondary_contact') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="primary_contact">{{ $t("Primary Email") }}</label>
                        <input type="text" name="primary_email" v-model="primary_email" v-validate="'email'" class="form-control form-control-custom" :placeholder="$t('Please enter primary email')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('primary_email') }">{{ errors.first('primary_email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Secondary Email") }}</label>
                        <input type="text" name="secondary_email" v-model="secondary_email" v-validate="'email'" class="form-control form-control-custom" :placeholder="$t('Please enter secondary email')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('secondary_email') }">{{ errors.first('secondary_email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address">{{ $t("Address") }}</label>
                        <textarea name="address" v-model="address" v-validate="'required|max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter store address')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('address') }">{{ errors.first('address') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="country">{{ $t("Country") }}</label>
                        <select name="country" v-model="country" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Country..</option>
                            <option v-for="(country_item, index) in country_list" v-bind:value="country_item.country_id" v-bind:key="index">
                                {{ country_item.code }} - {{ country_item.name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('country') }">{{ errors.first('country') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="pincode">{{ $t("Pincode") }}</label>
                        <input type="text" name="pincode" v-model="pincode" v-validate="'max:15'" class="form-control form-control-custom" :placeholder="$t('Enter Pincode')">
                        <span v-bind:class="{ 'error' : errors.has('pincode') }">{{ errors.first('pincode') }}</span>
                    </div>
                </div>
                <hr>
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Restaurant Mode") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="restaurant_mode">{{ $t("Enable Restaurant Mode") }}</label>
                        <select name="restaurant_mode" v-model="restaurant_mode" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Restaurant Mode..</option>
                            <option v-for="(restaurant_mode_option, index) in restaurant_mode_options" v-bind:value="index" v-bind:key="index">
                                {{ restaurant_mode_option}}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('restaurant_mode') }">{{ errors.first('restaurant_mode') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="restaurant_billing_type">{{ $t("Default Billing Type") }}</label>
                        <select name="restaurant_billing_type" v-model="restaurant_billing_type" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Default Billing Type..</option>
                            <option v-for="(billing_type_list_item, index) in billing_type_list" v-bind:value="billing_type_list_item.billing_type_constant" v-bind:key="index">
                                {{ billing_type_list_item.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('restaurant_billing_type') }">{{ errors.first('restaurant_billing_type') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="restaurant_waiter_role">{{ $t("Role for Waiter") }}</label>
                        <select name="restaurant_waiter_role" v-model="restaurant_waiter_role" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Role for Waiter..</option>
                            <option v-for="(waiter_role_item, index) in waiter_role" v-bind:value="waiter_role_item.slack" v-bind:key="index">
                                {{ waiter_role_item.role_code }} - {{ waiter_role_item.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('restaurant_waiter_role') }">{{ errors.first('restaurant_waiter_role') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="restaurant_chef_role">{{ $t("Role for Chef") }}</label>
                        <select name="restaurant_chef_role" v-model="restaurant_chef_role" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Role for Chef..</option>
                            <option v-for="(chef_role_item, index) in chef_role" v-bind:value="chef_role_item.slack" v-bind:key="index">
                                {{ chef_role_item.role_code }} - {{ chef_role_item.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('restaurant_chef_role') }">{{ errors.first('restaurant_chef_role') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="enable_digital_menu_otp_verification">{{ $t("Digital Menu OTP Verification") }}</label>
                        <select name="enable_digital_menu_otp_verification" v-model="enable_digital_menu_otp_verification" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Digital Menu OTP Verification..</option>
                            <option v-for="(restaurant_menu_otp_verification_options_item, index) in restaurant_menu_otp_verification_options" v-bind:value="index" v-bind:key="index">
                                {{ restaurant_menu_otp_verification_options_item }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('enable_digital_menu_otp_verification') }">{{ errors.first('enable_digital_menu_otp_verification') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="digital_menu_send_order_to_kitchen">{{ $t("Send Digital Menu Orders To Kitchen") }}</label>
                        <select name="digital_menu_send_order_to_kitchen" v-model="digital_menu_send_order_to_kitchen" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Send Digital Menu Orders To Kitchen..</option>
                            <option v-for="(digital_menu_send_order_to_kitchen_option_item, index) in digital_menu_send_order_to_kitchen_options" v-bind:value="index" v-bind:key="index">
                                {{ digital_menu_send_order_to_kitchen_option_item }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('digital_menu_send_order_to_kitchen') }">{{ errors.first('digital_menu_send_order_to_kitchen') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="menu_language">{{ $t("Digital Menu Language") }}</label>
                        <select name="menu_language" v-model="menu_language" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Digital Menu Language..</option>
                            <option v-for="(language, index) in languages" v-bind:value="language.language_constant" v-bind:key="index">
                                {{ language.language_code+' - '+language.language }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('menu_language') }">{{ errors.first('menu_language') }}</span> 
                    </div>
                </div>
                <hr>
                <div v-if="store_slack != ''">
                    <div class="d-flex flex-wrap mb-1">
                        <div class="mr-auto">
                            <span class="text-subhead">{{ $t("Store wise Tax & Discount Information") }}</span>
                        </div>
                        <div class="">
                            
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="form-group col-md-3">
                            <label for="tax_code">{{ $t("Tax Code") }} ({{ $t("Option to Choose Exclusive Tax") }})</label>
                            <select name="tax_code" v-model="tax_code" v-validate="''" class="form-control form-control-custom custom-select">
                                <option value="">Choose Tax Code..</option>
                                <option v-for="(tax_code, index) in tax_codes" v-bind:value="tax_code.slack" v-bind:key="index">
                                    {{ tax_code.tax_code+' - '+tax_code.label }}
                                </option>
                            </select>
                            <span v-bind:class="{ 'error' : errors.has('tax_code') }">{{ errors.first('tax_code') }}</span> 
                        </div>
                        <div class="form-group col-md-3">
                            <label for="discount_code">{{ $t("Discount Code") }}</label>
                            <select name="discount_code" v-model="discount_code" v-validate="''" class="form-control form-control-custom custom-select">
                                <option value="">Choose Discount Code..</option>
                                <option v-for="(discount_code, index) in discount_codes" v-bind:value="discount_code.slack" v-bind:key="index">
                                    {{ discount_code.discount_code+' - '+discount_code.label }}
                                </option>
                            </select>
                            <span v-bind:class="{ 'error' : errors.has('discount_code') }">{{ errors.first('discount_code') }}</span> 
                        </div>
                    </div>
                    <hr>
                </div>
                
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Status Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Status") }}</label>
                        <select name="status" v-model="status" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('status') }">{{ errors.first('status') }}</span> 
                    </div>
                </div>
                <hr>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("POS Screen Setting") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Enable Customer Detail Popup") }}</label>
                        <select name="enable_customer_popup" v-model="enable_customer_popup" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Customer Popup..</option>
                            <option v-for="(customer_popup_option, index) in customer_popup_options" v-bind:value="index" v-bind:key="index">
                                {{ customer_popup_option }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('enable_customer_popup') }">{{ errors.first('enable_customer_popup') }}</span>  
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Enable Variant Selection Popup") }}</label>
                        <select name="enable_variants_popup" v-model="enable_variants_popup" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Variant Selection Popup..</option>
                            <option v-for="(variant_popup_option, index) in variant_popup_options" v-bind:value="index" v-bind:key="index">
                                {{ variant_popup_option }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('enable_variants_popup') }">{{ errors.first('enable_variants_popup') }}</span>  
                    </div>
                </div>
                <hr>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Invoice Print & Currency Details") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="print_type">{{ $t("Invoice Print Type") }}</label>
                        <select name="print_type" v-model="print_type" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Invoice Print Type..</option>
                            <option v-for="(invoice_print_type, index) in invoice_print_types" v-bind:value="invoice_print_type.print_type_value" v-bind:key="index">
                                {{ invoice_print_type.print_type_label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('print_type') }">{{ errors.first('print_type') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="currency_code">{{ $t("Currency") }}</label>
                        <select name="currency_code" v-model="currency_code" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Currency..</option>
                            <option v-for="(currency_item, index) in currency_list" v-bind:value="currency_item.currency_code" v-bind:key="index">
                                {{ currency_item.currency_code }} - {{ currency_item.currency_name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('currency_code') }">{{ errors.first('currency_code') }}</span> 
                    </div>
                </div>
            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                Confirm
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">You are making the store inactive.</p>
                Are you sure you want to proceed?
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
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.store_data == null)?'/api/add_store':'/api/update_store/'+this.store_data.slack,

                restaurant_mode_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },

                customer_popup_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },

                restaurant_menu_otp_verification_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },

                digital_menu_send_order_to_kitchen_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },

                variant_popup_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },

                store_slack     : (this.store_data == null)?'':this.store_data.slack,
                name            : (this.store_data == null)?'':this.store_data.name,
                store_code      : (this.store_data == null)?'':this.store_data.store_code,
                tax_number      : (this.store_data == null)?'':this.store_data.tax_number,
                primary_contact : (this.store_data == null)?'':this.store_data.primary_contact,
                secondary_contact : (this.store_data == null)?'':this.store_data.secondary_contact,
                primary_email   : (this.store_data === null)?'':this.store_data.primary_email,
                secondary_email : (this.store_data == null)?'':this.store_data.secondary_email,
                address         : (this.store_data == null)?'':this.store_data.address,
                country         : (this.store_data == null)?'':(this.store_data.country == null)?'':this.store_data.country.id,
                pincode         : (this.store_data == null)?'':this.store_data.pincode,
                tax_code        : (this.store_data == null)?'':(this.store_data.tax_code == null)?'':this.store_data.tax_code.slack,
                discount_code   : (this.store_data == null)?'':(this.store_data.discount_code == null)?'':this.store_data.discount_code.slack,
                print_type      : (this.store_data == null)?'':(this.store_data.invoice_type == null)?'':this.store_data.invoice_type.print_type_value,
                currency_code   : (this.store_data == null)?'':(this.store_data.currency_code == null)?'':this.store_data.currency_code,
                restaurant_mode : (this.store_data == null)?0:this.store_data.restaurant_mode,
                restaurant_billing_type : (this.store_data == null)?'':(this.store_data.restaurant_billing_type == null)?'':this.store_data.restaurant_billing_type.billing_type_constant,
                restaurant_waiter_role : (this.store_data == null)?'':(this.store_data.restaurant_waiter_role == null)?'':this.store_data.restaurant_waiter_role.slack,
                restaurant_chef_role : (this.store_data == null)?'':(this.store_data.restaurant_chef_role == null)?'':this.store_data.restaurant_chef_role.slack,
                status          : (this.store_data == null)?'':this.store_data.status.value,
                enable_customer_popup : (this.store_data == null)?0:this.store_data.enable_customer_popup,
                enable_variants_popup : (this.store_data == null)?0:this.store_data.enable_variants_popup,
                enable_digital_menu_otp_verification : (this.store_data == null)?0:this.store_data.enable_digital_menu_otp_verification,
                digital_menu_send_order_to_kitchen : (this.store_data == null)?0:this.store_data.digital_menu_send_order_to_kitchen,
                menu_language: (this.store_data == null)?'':((this.store_data.menu_language == null)?'':this.store_data.menu_language.language_constant),
            }
        },
        props: {
            statuses: Array,
            tax_codes: Array,
            discount_codes: Array,
            invoice_print_types: Array,
            currency_list: Array,
            country_list: Array,
            accounts: Array,
            store_data: [Array, Object],
            billing_type_list: [Array, Object],
            waiter_role: [Array, Object],
            chef_role: [Array, Object],
            languages: [Array, Object],
        },
        mounted() {
            console.log('Add store page loaded');
        },
        methods: {
            submit_form(){

                this.$off("submit");
                this.$off("close");
                
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("name", (this.name == null)?'':this.name);
                            formData.append("store_code", (this.store_code == null)?'':this.store_code);
                            formData.append("tax_number", (this.tax_number == null)?'':this.tax_number);
                            formData.append("primary_contact", (this.primary_contact == null)?'':this.primary_contact);
                            formData.append("secondary_contact", (this.secondary_contact == null)?'':this.secondary_contact);
                            formData.append("primary_email", (this.primary_email == null)?'':this.primary_email);
                            formData.append("secondary_email", (this.secondary_email == null)?'':this.secondary_email);
                            formData.append("address", (this.address == null)?'':this.address);
                            formData.append("country", (this.country == null)?'':this.country);
                            formData.append("pincode", (this.pincode == null)?'':this.pincode);
                            formData.append("tax_code", (this.tax_code == null)?'':this.tax_code);
                            formData.append("discount_code", (this.discount_code == null)?'':this.discount_code);
                            formData.append("invoice_type", (this.print_type == null)?'':this.print_type);
                            formData.append("currency_code", (this.currency_code == null)?'':this.currency_code);
                            formData.append("restaurant_mode", (this.restaurant_mode == null)?'':this.restaurant_mode);
                            formData.append("restaurant_billing_type", (this.restaurant_billing_type == null)?'':this.restaurant_billing_type);
                            formData.append("restaurant_waiter_role", (this.restaurant_waiter_role == null)?'':this.restaurant_waiter_role);
                            formData.append("restaurant_chef_role", (this.restaurant_chef_role == null)?'':this.restaurant_chef_role);
                            formData.append("enable_customer_popup", (this.enable_customer_popup == null)?'':this.enable_customer_popup);
                            formData.append("enable_variants_popup", (this.enable_variants_popup == null)?'':this.enable_variants_popup);
                            formData.append("enable_digital_menu_otp_verification", (this.enable_digital_menu_otp_verification == null)?'':this.enable_digital_menu_otp_verification);
                            formData.append("digital_menu_send_order_to_kitchen", (this.digital_menu_send_order_to_kitchen == null)?'':this.digital_menu_send_order_to_kitchen);
                            formData.append("menu_language", (this.menu_language == null)?'':this.menu_language);
                            formData.append("status", (this.status == null)?'':this.status);

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
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
                    }
                });
            }
        }
    }
</script>