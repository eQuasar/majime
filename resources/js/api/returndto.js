import axios from './axios.js'

export default {

   get_Statusdto(data, status) {
        return axios.get('get_Status/' + data+'/'+status)
    },

}