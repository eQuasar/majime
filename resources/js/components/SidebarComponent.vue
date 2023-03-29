<template>
  <sidebar-menu
    :menu="menu"
    @update:collapsed="onToggleCollapse"
    @item-click="onItemClick"
  />
</template>

<script>
export default {
  data() {
    return {
      menu: [
        {
          href: { name: "admindashboard1" },
          title: "Dashboard",
          icon: "fas fa-th",
          child: [
            {
              href: { name: "adminperformance" },
              title: "Performance",
            },
          ],
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
    };
  },
  methods: {
    onToggleCollapse(collapsed) {
      document.getElementById("mySidebar").style.width = "0";
      document.getElementById("main").style.marginLeft = "0";
    },
    onItemClick(event, item) {
      document.getElementById("Menu").style.width = "250px";
      document.getElementById("main").style.marginLeft = "250px";
    },
  },
};
</script>
