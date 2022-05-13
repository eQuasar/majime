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
                    {
                        href:{ name: 'report'},
                        title: 'Reports',
                        icon: 'fa fa-pencil',
                       
                    },
                    {
                        href:{ name: 'readytopack_orders'},
                        title: 'Ready to Pack',
                        icon: 'fa fa-user',
                       
                    },
                    {
                        href:{ name: 'confirmedorders'},
                        title: 'Assign AWB',
                        icon: 'fa fa-user',
                       
                    },
                    {
                        href:{ name: 'vendor_awbdata'},
                        title: 'AWB Pickup Location',
                        icon: 'fa fa-info',
                       
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