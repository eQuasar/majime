import App from './components/AppComponent.vue'
import AdminDashboard from './components/Admin/Dashboard.vue'
import ImportVendor from './components/Admin/ImportVendor.vue'
import wallet from './components/Wallet/wallet.vue'
import AddAWBData from './components/Manage/AddAWBData.vue'
import accounts from './components/Accounts/Accounts.vue'


import ProductList from './components/Products/ProductList.vue'
import vendorDashboard from './components/Vendor/Dashboard.vue'
import listOrders from './components/Orders/listOrders.vue'
import ReportProfile from './components/Report/OrderDetail.vue'
import OrderProfile2 from './components/Orders/OrderProfile.vue'
import ReadytopackOrders from './components/Orders/ReadytopackOrders.vue'
import ConfirmedOrders from './components/Orders/ConfirmedOrders.vue'


export const routes = [ 
    {path: '/',component: App},
	{path: '/home',component: App},
    {path:'/vendor/ProductList',component: ProductList,name:'ProductList',meta: { auth: true}},
   //  {path:'/vendor/OrderProfile',component: OrderProfile,name:'OrderProfile',meta: { auth: true}},

    {path:'/vendor/listOrders',component: listOrders,name:'listOrders',meta: { auth: true}},
    {path:'/vendor/ReadytopackOrders',component: ReadytopackOrders,name:'readytopack_orders',meta: { auth: true}},
    {path:'/vendor/ConfirmedOrders',component: ConfirmedOrders,name:'confirmedorders',meta: { auth: true}},
    {path:'/vendor/dashboard',component: vendorDashboard,name:'vendorDashboard',meta: { auth: true}},
    {path:'/admin/dashboard',component: AdminDashboard,name:'admindashboard',meta: { auth: true}},
    {path:'/vendor/wallet',component: wallet,name:'wallet',meta: { auth: true}},
    {path:'/admin/awb-data',component: AddAWBData,name:'admin_awbdata',meta: { auth: true}},
    {path:'/vendor/awb-data',component: AddAWBData,name:'vendor_awbdata',meta: { auth: true}},
    {path:'/vendor/accounts',component: accounts,name:'accounts',meta: { auth: true}},
    {path:'/accounts/allaccounts',component: accounts,name:'allaccounts',meta: { auth: true}},
    {path:'/admin/import-vendor',component: ImportVendor,name:'importvendor',meta: { auth: true}},
    //{path:'/vendor/Order-detail/:clientid',component: ReportProfile,props: true,name:'orderdetail',meta: { auth: true}},
    {path:'/vendor/OrderProfile/:oid',component: OrderProfile2,props: true,name:'OrderProfile',meta: { auth: true}},
    
];