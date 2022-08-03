import App from './components/AppComponent.vue'
import AdminDashboard from './components/Admin/Dashboard.vue'
import ImportVendor from './components/Admin/ImportVendor.vue'
import wallet from './components/Wallet/wallet.vue'
import AddAWBData from './components/Manage/AddAWBData.vue'
import accounts from './components/Accounts/Accounts.vue'
import ProductList from './components/Products/ProductList.vue'
import AdminProductList from './components/Products/ProductList.vue'
import vendorDashboard from './components/Vendor/Dashboard.vue'
import listOrders from './components/Orders/listOrders.vue'
import ProcessingOrders from './components/Orders/ProcessingOrders.vue'
import AdminlistOrders from './components/Orders/listOrders.vue'
//import ReportProfile from './components/Report/OrderDetail.vue'
import OrderProfile2 from './components/Orders/OrderProfile.vue'
import productprofile from './components/Products/productprofile.vue'
import ReadytopackOrders from './components/Orders/ReadytopackOrders.vue'
import ConfirmedOrders from './components/Orders/ConfirmedOrders.vue'
import CompleteOrders from './components/Orders/CompleteOrders.vue'
import PrintSlip from './components/Orders/PrintSlip.vue'
import TransactionDetail from './components/Admin/TransactionDetail.vue'
import ViewTransaction from './components/Admin/ViewTransaction.vue'
import PendingRefunds from './components/Orders/PendingRefunds.vue'
import PackedOrders from './components/Orders/PackedOrders.vue'
import ForDelivery from './components/Products/ForDelivery.vue'
import OnHoldOrders from './components/Orders/OnHoldOrders.vue'
import DtoIntransit from './components/Orders/dtoIntransit.vue'
import DtoBooked from './components/Orders/dtoBooked.vue'
import DtoDelivered from './components/Orders/dtoDelivered.vue'
// import ZoneDetail from './components/Master/zoneDetail.vue'
import ZoneRateCard from './components/Master/zonerateCard.vue'
import VendorRateCard from './components/Master/vendorrateCard.vue'
import ZRCView from './components/Master/ZRCView.vue'
import VRCView from './components/Master/VRCView.vue'
import demo from './components/Admin/demo.vue'


export const routes = [ 
    {path: '/',component: App},
	{path: '/home',component: App},
    {path:'/vendor/ProductList',component: ProductList,name:'ProductList',meta: { auth: true}},
    {path:'/admin/ProductList',component: AdminProductList,name:'adminProductList',meta: { auth: true}},
    // {path:'/admin/ZoneDetail',component: ZoneDetail,name:'zoneDetail',meta: { auth: true}},
    {path:'/admin/ZoneRateCard',component: ZoneRateCard,name:'zonerateCard',meta: { auth: true}},
    {path:'/admin/VendorRateCard',component: VendorRateCard,name:'vendorrateCard',meta: { auth: true}},
    {path:'/admin/ZRCView',component: ZRCView,name:'ZRCView',meta: { auth: true}},
    {path:'/admin/VRCView',component: VRCView,name:'VRCView',meta: { auth: true}},
   //  {path:'/vendor/OrderProfile',component: OrderProfile,name:'OrderProfile',meta: { auth: true}},
    {path:'/vendor/listOrders',component: listOrders,name:'listOrders',meta: { auth: true}},
    {path:'/vendor/ProcessingOrders',component: ProcessingOrders,name:'ProcessingOrders',meta: { auth: true}},
    {path:'/admin/listOrders',component: AdminlistOrders,name:'adminlistOrders',props: true,meta: { auth: true}},
    {path:'/vendor/ReadytopackOrders',component: ReadytopackOrders,name:'readytopack_orders',meta: { auth: true}},
    {path:'/vendor/PackedOrders',component: PackedOrders,name:'packedorders',meta: { auth: true}},
    {path:'/vendor/PendingRefunds',component: PendingRefunds,name:'pendingrefunds',meta: { auth: true}},
    {path:'/vendor/ConfirmedOrders',component: ConfirmedOrders,name:'confirmedorders',meta: { auth: true}},
    {path:'/admin/CompleteOrders',component: CompleteOrders,name:'CompleteOrders',meta: { auth: true}},
    {path:'/vendor/print-slip',component: PrintSlip,name:'printslip',meta: { auth: true}},
    {path:'/vendor/dashboard',component: vendorDashboard,name:'vendorDashboard',meta: { auth: true}},
    {path:'/admin/dashboard',component: AdminDashboard,name:'admindashboard',meta: { auth: true}},
    {path:'/admin/wallet',component: wallet,name:'wallet',meta: { auth: true}},
    {path:'/admin/awb-data',component: AddAWBData,name:'admin_awbdata',meta: { auth: true}},
    {path:'/vendor/awb-data',component: AddAWBData,name:'vendor_awbdata',meta: { auth: true}},
    {path:'/vendor/accounts',component: accounts,name:'accounts',meta: { auth: true}},
    {path:'/accounts/allaccounts',component: accounts,name:'allaccounts',meta: { auth: true}},
    {path:'/admin/import-vendor',component: ImportVendor,name:'importvendor',meta: { auth: true}},
    //{path:'/vendor/Order-detail/:clientid',component: ReportProfile,props: true,name:'orderdetail',meta: { auth: true}},
    {path:'/vendor/OrderProfile/:oid',component: OrderProfile2,props: true,name:'OrderProfile',meta: { auth: true}},
    {path:'/vendor/ListOrdersforVariationID/:variation_id',component: productprofile,props: true,name:'productprofile',meta: { auth: true}},
     {path:'/admin/transaction',component:TransactionDetail ,props: true,name:'TransactionDetail',meta: { auth: true}},
     {path:'/admin/ViewTransaction',component:ViewTransaction ,props: true,name:'ViewTransaction',meta: { auth: true}},
     {path:'/vendor/ForDelivery',component:ForDelivery,name:'fordelivery',meta: { auth: true}},
     {path:'/vendor/OnHoldOrders',component:OnHoldOrders,name:'onholdorders',meta: { auth: true}},
      {path:'/vendor/dtoIntransit',component:DtoIntransit,name:'dtoIntransit',meta: { auth: true}},
      {path:'/vendor/dtoBooked',component:DtoBooked,name:'dtoBooked',meta: { auth: true}},
    {path:'/vendor/dtoDelivered',component:DtoDelivered,name:'dtoDelivered',meta: { auth: true}},
    {path:'/admin/demo',component: demo,name:'demo',meta: { auth: true}},

  
];