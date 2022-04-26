import App from './components/AppComponent.vue'
import AdminDashboard from './components/Admin/Dashboard.vue'
import wallet from './components/Wallet/wallet.vue'
import report from './components/Report/report.vue'
import accounts from './components/Accounts/Accounts.vue'


import ProductList from './components/Products/ProductList.vue'
import vendorDashboard from './components/Vendor/Dashboard.vue'
import listOrders from './components/Orders/listOrders.vue'
import ReportProfile from './components/Report/OrderDetail.vue'
export const routes = [
    {path: '/',component: App},
	{path: '/home',component: App},
    {path:'/vendor/ProductList',component: ProductList,name:'ProductList',meta: { auth: true}},
    {path:'/vendor/listOrders',component: listOrders,name:'listOrders',meta: { auth: true}},
    {path:'/vendor/dashboard',component: vendorDashboard,name:'vendorDashboard',meta: { auth: true}},
    {path:'/admin/dashboard',component: AdminDashboard,name:'admindashboard',meta: { auth: true}},
    {path:'/vendor/wallet',component: wallet,name:'wallet',meta: { auth: true}},
    {path:'/vendor/report',component: report,name:'report',meta: { auth: true}},
    {path:'/vendor/accounts',component: accounts,name:'accounts',meta: { auth: true}},
    {path:'/vendor/Order-detail/:clientid',component: ReportProfile,props: true,name:'orderdetail',meta: { auth: true}},
];