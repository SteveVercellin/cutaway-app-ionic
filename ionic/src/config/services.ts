export const ServicesConfig = {
    check_nonce_before_request: false,
    authenticate: {
        wp: {
            login: 'wp-json/jwt-auth/v1/token'
        },
        social: {
            get_nonce: 'wp-json/social-authenticate-rest/v1/get-nonce-action',
            authenticate: 'wp-json/social-authenticate-rest/v1/authenticate',
            log_authenticate_error: 'wp-json/social-authenticate-rest/v1/log-authenticate-fail'
        }
    },
    functions: {
        wp: {
            get_nonce: 'wp-json/wordpress-rest-functions/v1/get-nonce-action',
            register: 'wp-json/wordpress-rest-functions/v1/register',
            send_reset_pass_email: 'wp-json/wordpress-rest-functions/v1/send-reset-pass',
            get_user_information: 'wp-json/wordpress-rest-functions/v1/get-user-information',
            update_user_information: 'wp-json/wordpress-rest-functions/v1/update-user-information',
            change_user_password: 'wp-json/wordpress-rest-functions/v1/change-user-password'
        },
        bookly: {
            get_list_services: 'wp-json/cutaway/v1/services/getAllServices',
            search_staffs: 'wp-json/cutaway/v1/shop/searchStaffMember',
            load_staff_detail: 'wp-json/cutaway/v1/shop/getStaffDetail',
            get_staff_book_summary: 'wp-json/cutaway/v1/shop/getStaffBookSummary',
            get_customer_orders: 'wp-json/cutaway/v1/shop/getCustomerOrders',
            get_customer_order: 'wp-json/cutaway/v1/shop/getCustomerOrder',
            get_customer_histories: 'wp-json/cutaway/v1/shop/getCustomerHistories',
            get_staffs_customer_booked: 'wp-json/cutaway/v1/shop/getListStaffsCustomerBooked',
            make_staff_favourite: 'wp-json/cutaway/v1/customers/favorite',
            make_staff_unfavourite: 'wp-json/cutaway/v1/customers/unfavorite',
            get_list_locations: 'wp-json/cutaway/v1/locations/getLocations',
            load_staff_dashboard_data: 'wp-json/cutaway/v1/staffs/getStaffDashboardData',
            update_staff_book_available: 'wp-json/cutaway/v1/staffs/updateStaffBookAvailable',
            load_staff_config_available_time_slots: 'wp-json/cutaway/v1/shop/getStaffConfigAvailableTimeSlots',
            update_staff_config_available_time_slots: 'wp-json/cutaway/v1/shop/setStaffConfigAvailableTimeSlots',
            get_customer_orders_review: 'wp-json/cutaway/v1/shop/getCustomerOrdersReview',
            set_customer_order_review: 'wp-json/cutaway/v1/shop/updateCustomerOrderReview',
            delete_customer_order_review: 'wp-json/cutaway/v1/shop/deleteCustomerOrderReview',
            get_orders_review: 'wp-json/cutaway/v1/shop/getOrdersReview',
            get_staff_orders: 'wp-json/cutaway/v1/staffs/getStaffOrders',
            get_staff_order: 'wp-json/cutaway/v1/staffs/getStaffOrder',
            get_staff_orders_review: 'wp-json/cutaway/v1/staffs/getStaffOrdersRating'
        }
    },
    payments: {
        get_nonce: 'wp-json/rest-payments/v1/get-nonce-action',
        create_pending_booking: 'wp-json/rest-payments/v1/create-pending-booking',
        stripe: {
            pay: 'wp-json/rest-payments/v1/stripe-payment',
            refund: 'wp-json/rest-payments/v1/refund-stripe-payment'
        },
        paypal: {
            complete_booking: 'wp-json/rest-payments/v1/paypal-payment'
        }
    }
}