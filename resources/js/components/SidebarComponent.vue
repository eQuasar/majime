<template>
	<sidebar-menu :menu="menu" />
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
                        title: 'Orders',
                        icon: 'fas fa-th',
                        child: [
                            {
                                href: {name:'addappointment'},
                                title: 'List Orders'
                            },
                            {
                                href: { name: 'appointments'},
                                title: 'List Products'
                            }
                        ]
                    }//,
                    // {
                        
                    //     title: 'Clients',
                    //     icon: 'fa fa-user',
                    //     child: [
                    //         {
                    //             href: {name:'createclient'},
                    //             title: 'Add Client'
                    //         },
                    //         {
                    //             href: { name: 'listclient'},
                    //             title: 'List Clients'
                    //         }
                    //     ]
                    // }
                ]
            }
        }
    }
</script>