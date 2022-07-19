<template>
  <sidebar-menu :menu="menu" @update:collapsed="onToggleCollapse" @item-click="onItemClick"/>
</template>

<script>
export default {
  data() {
    return {
      menu: [
        {
          href: { name: "admindashboard" },
          title: "Dashboard",
          icon: "fas fa-th",
        },
        {
          title: "Sales",
          icon: "fas fa-th",
          child: [
            {
              href: { name: "addappointment" },
              title: "Products",
            },
            {
              href: { name: "appointments" },
              title: "Orders",
            },
          ],
        }, //,
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
      ],
    }
  },
  methods: {
    onToggleCollapse(collapsed) {
      console.log(collapsed);
    },
    onItemClick(event, item) {
      console.log(event);
      console.log(item);
    }
  }
};
</script>
