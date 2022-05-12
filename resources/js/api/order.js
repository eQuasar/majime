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
        // alert(vid+" uuuu "+status);
        return axios.get('getOrderOnStatus/'+vid+'/'+status)
    },
    assignAWB(data){
        return axios.post('assignAWB', data)
    }
}

