import axios from './axios.js'

export default {

    getVid(data) {
        return axios.post('getVid', data)
    },

}