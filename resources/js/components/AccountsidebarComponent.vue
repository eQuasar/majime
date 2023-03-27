<template>
  <sidebar-menu :menu="menu"/>
</template>

<script>
    export default {
        data() {
            return {
                menu: [
                     {
                        href:{ name: 'allaccounts'},
                        title: 'Accounts',
                        icon: 'fa fa-user',
                       
                    },
                    
                ]
            }
        }
    }
</script>