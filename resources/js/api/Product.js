import axios from './axios.js'

export default {

    getProductDetail(data) {
        return axios.post('getProductdetail', data)
    },
     categorySearch(data) {
        return axios.post('category_Search', data)
    },
    productSearch(data) {
        return axios.post('product_search', data)
    },
        colorSearchSearch(data) {
        return axios.post('color_Search', data)
    },
    getProduct(data) {
        return axios.get('product_data', data)
    },
      getStatus(data) {
        return axios.get('status_data', data)
    },
     getProductProfile(variation_id, vid) {
        return axios.get('product_Profile/' + variation_id+'?vid='+vid)
    },
    getProductItems(variation_id, vid) {
        return axios.get('product_items/' + variation_id+'?vid='+vid)
    },
     getDeliveryDetails(data) {
        return axios.post('getDelivery_Details', data)
    },
     Product_downloadsheet(data) {
        return axios.post('product_Sheet_download', data)
    },
       ProductorderSearch(data) {
        return axios.post('product_Order_Search',data)
    },







}