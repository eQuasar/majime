import axios from './axios.js'

export default {

    getdashboard_detail(vid) {
        return axios.get('dashboard_detail/'+vid)
    },
    dashbaordSearch(data) {
        return axios.post('dashboard_search' , data)
    }

   }