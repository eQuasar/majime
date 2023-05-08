import axios from './axios.js'

export default {

    getOrderDetail(data) {
        return axios.post('getOrderdetail', data)
    },

    getOrderDetails(data) {
        return axios.post('getOrderDetails', data)
    },
    orderSearch(data) {
        return axios.post('order_Search',data)
    },
    orderSearchdata(data) {
        return axios.post('order_Searchdata',data)
    },

    getOrderProfile(oid, vid) {
        return axios.get('order_Profile/' + oid+'?vid='+vid)
    },
    getOrderItems(oid, vid) {
        return axios.get('order_items/' + oid+'?vid='+vid)
    },
    changeStatus(data) {
        return axios.post('changeStatus', data)
    },
    
    getOrderOnStatus(vid,status) {
        return axios.get('getOrderOnStatus/'+vid+'/'+status)
    },
    getCompleteOrdersStatus(vid,statrto,statdto,statcomp,clos) {
        return axios.get('getComplete_OrdersStatus/'+vid+'/'+statrto+'/'+statdto+'/'+statcomp+'/'+clos)
    },
    assignAWB(data){
        return axios.post('assignAWB', data)
    },
    assignAWBOrder(data){
        return axios.post('assignAWBOrder', data)
    },
    return_order(){
        return axios.get('return_order')
    },
    printSlip(data){
        return axios.post('printSlip', data)
    },
    printOrderSlip(data){
        return axios.post('printOrderSlip', data)
    },
     citySearch(data) {
        return axios.post('city_Search', data)
    },
    stateSearch(data) {
        return axios.post('state_Search', data)
    },
    statusSearch(data) {
        return axios.post('status_Search', data)
    },
     PendingRefundstatus(data) {
        return axios.get('get_PackdetailRefund1', data)
    },
    PendingRefund_changeStatus(data) {
        return axios.post('Refundchange_Status', data)
    },
    // getPackdetail_Refund(data) {
    //     return axios.post('get_Packdetail_Refund', data)
    // },
    ListOrderStatus_assign(data) {
        return axios.post('listOrder_Status', data)
    },
    downloadsheet(data) {
        return axios.post('download_Sheet', data)
    },

    stateSearchoption(data) {
        return axios.post('state_Search_option', data)
    },
     stateSearchSt(data) {
        return axios.post('state_Search_Select', data)
    },
    getStates(data) {
        return axios.get('state_data', data)
    },
    getCity(data) {
        return axios.post('city_data', data)
    },
     getStatus(data) {
        return axios.post('status_data',data)
    },
     getPackdetail_Refund(data) {
        return axios.get('get_packdetail_Refund/' + data)
    },
    getPackDetail(data) {
        return axios.get('getpackdetail/' + data)
    },
    changeProcessingStatus(data) {
        return axios.post('changeProcessing_Status', data)
    },
       getProcessing_OrderDetails(data) {
        return axios.post('getProcessingOrder_Details', data)
    },
      getProcessing_data(data, status) {
        return axios.get('get_processing_data/' + data+'/'+status)
    },
    
       Processing_downloadsheet(data) {
        return axios.post('processing_download_Sheet', data)
    },
      Processing_downloadsheet(data) {
        return axios.post('processing_download_Sheet', data)
    },
    Confirm_downloadsheet(data) {
        return axios.post('confirm_download_Sheet', data)
    },
    Pending_downloadsheet(data) {
        return axios.post('pending_download_Sheet', data)
    },
     deliverysheet(data) {
        return axios.post('delivery_download_Sheet', data)
    },
      OnHold_downloadsheet(data) {
        return axios.post('onhold_download_Sheet', data)
    },
    changeStatusDispatch(data){
        return axios.post('change_status_on_dispatch', data)
    },
    returnAWB(data){
        return axios.post('return_awb', data)
    },
    getzone(data){
        return axios.get('zone_Search', data)
    },
    assignwallet(data){
        return axios.post('assign_wallet', data)
    },
    filterSearch(data) {
        return axios.post('filter_Search', data)
    },
    walletSearch(data) {
        return axios.post('wallet_Search',data)
    },
    changeProcessing_Status(data) {
        return axios.post('change_Processing_Status', data)
    },
    orderproduct_detail(data)
    {
        return axios.post('order_product_profile', data)
    },
    suborder_details(data)
    {
        return axios.post('suborder_details', data)
    },
    suborder_detailsdata(data)
    {
        return axios.post('suborder_details', data)
    },
    suborder_refund_amount(data)
    {
        return axios.post('refund_amount', data)
    },
    changeProcessing_Status(data) {
        return axios.post('changeProcessing_Status', data)
    },
    getpending_order(data) {
        return axios.post('pending_order', data)
    },
    orderSearchdata(data) {
        return axios.post('order_Searchdata',data)
    },
    refund_amount_data(data) {
        return axios.post('refundamount_update',data)
    },

}
