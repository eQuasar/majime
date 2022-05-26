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

    getOrderProfile(oid, vid) {
        return axios.get('order_Profile/' + oid+'?vid='+vid)
    },
    getOrderItems(oid, vid) {
        return axios.get('order_items/' + oid+'?vid='+vid)
    },
    changeStatus(data) {
        return axios.post('changeStatus', data)
    },
    getPackDetail(data) {
        return axios.get('getpackdetail/' + data)
    },
    getOrderOnStatus(vid,status) {
        return axios.get('getOrderOnStatus/'+vid+'/'+status)
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
}

