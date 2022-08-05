/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap');

window.Vue = require('vue').default;
import VueRouter from 'vue-router'
import Vuex from 'vuex'
import axios from 'axios'
import VueAxios from 'vue-axios'
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import { TabsPlugin } from 'bootstrap-vue'
import { OverlayPlugin } from 'bootstrap-vue'
import { FormTagsPlugin } from 'bootstrap-vue'
import VueStepWizard from 'vue-step-wizard'
import 'vue-step-wizard/dist/vue-step-wizard.css'
import VueAutosuggest from "vue-autosuggest"
import { DropdownPlugin } from 'bootstrap-vue'
import { FormRadioPlugin } from 'bootstrap-vue'
import { IconsPlugin } from 'bootstrap-vue'
import { PopoverPlugin } from 'bootstrap-vue'
import { TablePlugin } from 'bootstrap-vue'
import { ModalPlugin } from 'bootstrap-vue'
import { SpinnerPlugin } from 'bootstrap-vue'
import { BFormTimepicker } from 'bootstrap-vue'
import VueSimpleAlert from "vue-simple-alert";
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faUserSecret)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false


//Routes
import { routes } from './routes';

Vue.use(VueRouter)
Vue.use(Vuex)
Vue.use(VueAxios, axios)
Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(VueSidebarMenu)
Vue.use(TabsPlugin)
Vue.use(OverlayPlugin)
Vue.use(FormTagsPlugin)
Vue.use(VueStepWizard)
Vue.use(VueAutosuggest)
Vue.use(DropdownPlugin)
Vue.use(FormRadioPlugin)
Vue.use(IconsPlugin)
Vue.use(PopoverPlugin)
Vue.use(TablePlugin)
Vue.use(ModalPlugin)
Vue.use(SpinnerPlugin)
Vue.use(VueSimpleAlert);

/* add font awesome icon component */
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('b-form-timepicker', BFormTimepicker)
Vue.prototype.$userId = document.querySelector("meta[name='user-id']").getAttribute('content');
Vue.prototype.$userName = document.querySelector("meta[name='user-name']").getAttribute('content');
Vue.prototype.$axios = axios;

//Register Routes
export const router = new VueRouter({
    mode: 'history',
    routes
});
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('app-header', require('./components/HeaderComponent.vue').default);
Vue.component('app-footer', require('./components/FooterComponent.vue').default);
Vue.component('app-sidebar', require('./components/SidebarComponent.vue').default);
Vue.component('service-sidebar', require('./components/ServiceSidebar.vue').default);
Vue.component('vendor-sidebar', require('./components/VendorsidebarComponent.vue').default);
Vue.component('account-sidebar', require('./components/AccountsidebarComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    router
}).$mount('#app')
