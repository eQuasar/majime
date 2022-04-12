<template>
  <sidebar-menu :menu="menu"/>
</template>

<script>
    export default {
        data() {
            return {
                menu: [
                    {
                        href: {name:'servicedashboard'},
                        title: 'Dashboard',
                        icon: 'fas fa-th'
                    },
			{
                        title: 'Appointments',
                        icon: 'fas fa-calendar-alt',
                        child: [
                            {
                                href: {name:'serviceaddappointment'},
                                title: 'Add Appointment'
                            },
                            {
                                href: { name: 'serviceappointments'},
                                title: 'List Appointments'
                            },
                            {
                                href: { name: 'serviceexcelappointments'},
                                title: 'Assigned Appointments'
                            }
                        ]
                    },
                    {
                        
                        title: 'Clients',
                        icon: 'fa fa-user',
                        child: [
                            {
                                href: {name:'servicecreateclient'},
                                title: 'Add Client'
                            },
                            {
                                href: { name: 'servicelistclient'},
                                title: 'List Clients'
                            }
                        ]
                    },
                    
                ]
            }
        }
    }
</script>