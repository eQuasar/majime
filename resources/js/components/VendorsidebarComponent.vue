<template>
  <sidebar-menu :menu="menu"/>
</template>

<script>
    export default {
        data() {
            return {
                menu: [
        			{
                        title: 'Sales',
                        icon: 'fas fa-calendar-alt',
                        child: [
                          
                            {
                                href: { name: 'ProductList'},
                                title: 'Products'
                            },
                            {
                                href: { name: 'listOrders'},
                                title: 'Orders'
                            },
                          
                          
                        ]
                    },
                    {
                        
                        title: 'Wallet',
                        icon: 'fa fa-wallet',
                        href:{ name: 'wallet'}
                       
                    },
                    // {
                    //     href:{ name: 'report'},
                    //     title: 'Reports',
                    //     icon: 'fa fa-pencil',
                       
                    // },
                    {
                        title: 'Orders Mgt',
                        icon: 'fas fa-calendar-alt',
                        child: [
                            {
                                // href:{ name: 'readytopack_orders'},
                                href:{ name: 'ProcessingOrders'},
                                title: 'Confirm Orders',
                                // icon: 'fa fa-user',  
                               
                            },
                            {
                                // href:{ name: 'readytopack_orders'},
                                href:{ name: 'confirmedorders'},
                                title: 'Ready to Pack',
                                // icon: 'fa fa-user',  
                               
                            },
                            {
                                href:{ name: 'packedorders'},
                                title: 'Packed Orders',
                                // icon: 'fa fa-user',
                               
                            },
                            {
                                href:{ name: 'pendingrefunds'},
                                title: 'Pending Refunds',
                                // icon: 'fa fa-user',
                               
                            },
                            {
                                href:{ name: 'fordelivery'},
                                title: 'For Delivery',
                                // icon: 'fa fa-user',
                               
                            },
                            {
                                href:{ name: 'vendor_awbdata'},
                                title: 'AWBData',
                                // icon: 'fa fa-user',
                               
                            },
                            // {
                            //     href:{ name: 'vendor_awbdata'},
                            //     title: 'Temp-VendorPickup',
                                // icon: 'fa fa-info',
                               
                            // },
                            // {
                                // href:{ name: 'printslip'},
                                // title: 'Print Slip',
                                // href:{ name: 'readytopack_orders'},
                                // title: 'Temp-RTPack',
                                // icon: 'fa fa-info',
                               
                            // },
                        ]
                    },
                    //  {
                    //     href:{ name: 'accounts'},
                    //     title: 'Accounts',
                    //     icon: 'fa fa-user',
                       
                    // },
                    
                ]
            }
        }
    }
</script>