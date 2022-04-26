<template>
  <sidebar-menu :menu="menu"/>
</template>

<script>
    export default {
        data() {
            return {
                menu: [
                    {
                        href: {name:'admindashboard'},
                        title: 'Dashboard',
                        icon: 'fas fa-th'
                    },
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
                        icon: 'fa fa-user',
                        href:{ name: 'wallet'}
                       
                    },
                    {
                        href:{ name: 'report'},
                        title: 'Reports',
                        icon: 'fa fa-user',
                       
                    },
                     {
                        href:{ name: 'accounts'},
                        title: 'Accounts',
                        icon: 'fa fa-user',
                       
                    },
                    
                ]
            }
        }
    }
</script>