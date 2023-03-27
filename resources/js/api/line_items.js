import axios from './axios.js'

export default {

    getLineItemsDetail() {
        return axios.get('line_items')
    }
}