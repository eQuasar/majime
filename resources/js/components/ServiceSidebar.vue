<template>
  <sidebar-menu
    id="sidebar"
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
          href: { name: "admindashboard" },
          title: "Dashboard",
          icon: "fas fa-th",
        },
        {
          href: { name: "adminProductList" },
          title: "Products",
          icon: "fas fa-th",
        },
        {
          href: { name: "category" },
          title: "Category",
          icon: "fas fa-th",
        },
        {
          href: { name: "adminlistOrders" },
          title: "Orders",
          icon: "fas fa-th",
        },
        // {
        //   title: "Sales",
        //   icon: "fas fa-calendar-alt",
        //   child: [
        //     {
        //       href: { name: "adminProductList" },
        //       title: "Products",
        //     },
        //     {
        //       href: { name: "adminlistOrders" },
        //       title: "Orders",
        //     },
        //   ],
        // },
        {
          title: "Transactions",
          icon: "fas fa-calendar-alt",
          child: [
            {
              href: { name: "ViewTransaction" },
              title: "View",
            },
            {
              href: { name: "TransactionDetail" },
              title: "Add",
            },
          ],
        },
        // {
        //   title: "Master",
        //   icon: "fas fa-calendar-alt",
        //   child: [
        //     // {
        //     //   href: { name: "zoneDetail" },
        //     //   title: "Zone Detail",
        //     // },
        //     {
        //       href: { name: "zonerateCard" },
        //       title: "Zone Rate Card",
        //     },
        //     {
        //       href: { name: "ZRCView" },
        //       title: "ZRCView",
        //     },
        //     {
        //       href: { name: "vendorrateCard" },
        //       title: "Vendor Rate Card",
        //     },
        //     {
        //       href: { name: "VRCView" },
        //       title: "VRCView",
        //     },
        //   ],
        // },

        {
          title: "Orders Mgt",
          icon: "fas fa-calendar-alt",
          child: [
            {
              // href:{ name: 'readytopack_orders'},
              href: { name: "ProcessingOrders" },
              title: "Processing Orders",
              // icon: 'fa fa-user',
            },
            {
              // href:{ name: 'readytopack_orders'},
              href: { name: "confirmedorders" },
              title: "Ready2Pack/Confirmed",
              // icon: 'fa fa-user',
            },

            {
              href: { name: "packedorders" },
              title: "Packed Orders",
              // icon: 'fa fa-user',
            },
            {
              href: { name: "fordelivery" },
              title: "Dispatched Orders",
              // icon: 'fa fa-user',
            },
            {
              href: { name: "pendingrefunds" },
              title: "Pending Refunds",
              // icon: 'fa fa-user',
            },

            {
              href: { name: "onholdorders" },
              title: "On-Hold Orders",
              // icon: 'fa fa-user',
            },
            {
              // href:{ name: 'readytopack_orders'},
              href: { name: "CompleteOrders" },
              title: "Complete Orders",
              // icon: 'fa fa-user',
            },
            {
              href: { name: "deliveredorders" },
              title: "Delivered Orders",
            },
          ],
        },
        {
          title: "Vendor Configuration",
          icon: "fas fa-calendar-alt",
          child: [
            // {
            //   href: { name: "zoneDetail" },
            //   title: "Zone Detail",
            // },
            {
              href: { name: "admin_awbdata" },
              title: "Vendor Settings",
            },
            {
              href: { name: "vendorrateCard" },
              title: "Vendor Rate Card",
            },
            {
              href: { name: "zonerateCard" },
              title: "Zone Rate Card",
            },
          ],
        },
        {
          title: "Global Configuration",
          icon: "fas fa-calendar-alt",
          child: [
            // {
            //   href: { name: "zoneDetail" },
            //   title: "Zone Detail",
            // },
            // {
            //   href: { name: "hsndetail" },
            //   title: "Enter HSN Detail",
            // },
            {
              href: { name: "hsn_detail" },
              title: "HSN Master",
            },
            {
              href: { name: "import_file" },
              title: "Import Data",
            },
          ],
        },
        {
          href: { name: "wallet" },
          title: "Wallet",
          icon: "fa fa-user",
        },
        {
          href: { name: "billing" },
          title: "Billing",
          icon: "fa-solid fa-file-invoice",
        },

        // {
        //   href: { name: "report" },
        //   title: "Reports",
        //   icon: "fa fa-user",
        // },
        // {
        //   href: { name: "accounts" },
        //   title: "Accounts",
        //   icon: "fa fa-user",
        // },
        {
          href: { name: "importvendor" },
          title: "Import Vendor",
          icon: "fa fa-user",
        },
        // {
        //   href: { name: "demo" },
        //   title: "demo",
        //   icon: "fa fa-user",
        // },
      ],
    };
  },
  methods: {
    onToggleCollapse(collapsed) {
      console.log(collapsed);
    },
    onItemClick(event, item) {
      // document.getElementById(menu).style.width = "250px";
      // document.getElementById("main").style.marginLeft = "250px";
    },
  },
};
</script>
