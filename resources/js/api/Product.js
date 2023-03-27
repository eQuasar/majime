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
        return axios.post('product_data', data)
    },
      getStatus(data) {
        return axios.post('status_data', data)
    },
     getProductProfile(product_id, vid) {
        return axios.get('product_Profile/' + product_id+'?vid='+vid)
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
    get_Product_detail(product_id, vid) {
        return axios.get('product_detail/' + product_id+'?vid='+vid)
    },
    updateProduct_detail(data){
        return axios.post('update_product_detail', data)
    },
    getProductVariation_detail(data)
    {
        return axios.post('getProductVariation_detail', data)
    },
    update_productVariation_detail(data)
    {
        return axios.post('update_productVariation_detail', data)
    },
    getcategory(data)
    {
        return axios.post('get_category', data)
    },
    get_import_data(data)
    {
        return axios.post('get_import_data', data)
    }





}