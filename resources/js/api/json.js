import axios from './axios.js'

export default {
    getJson(data) {
        return axios.post('getJson', data)
    },
    getAllLinks(){
    	return axios.get('getAllLinks')
    }
}

