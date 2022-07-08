<template>
  <sidebar-menu :menu="menu" />
</template>

<script>
export default {
  data() {
    return {
      menu: [
        {
          title: "Sales",
          icon: "fas fa-calendar-alt",
          child: [
            {
              href: { name: "ProductList" },
              title: "Products",
            },
            {
              href: { name: "listOrders" },
              title: "Orders",
            },
          ],
        },
        //                    {
        //
        //                        title: 'Wallet',
        //                        icon: 'fa fa-wallet',
        //                        href:{ name: 'wallet'}
        //
        //                  },
        // {
        //     href:{ name: 'report'},
        //     title: 'Reports',
        //     icon: 'fa fa-pencil',

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
              href: { name: "onholdorders" },
              title: "On-Hold Orders",
              // icon: 'fa fa-user',
            },
          ],
        },
        {
          title: "Return Mgt",
          icon: "fas fa-calendar-alt",
          child: [
            {
              // href:{ name: 'readytopack_orders'},
              href: { name: "dtoIntransit" },
              title: "DTO-InTransit",
              // icon: 'fa fa-user',
            },
            {
              // href:{ name: 'readytopack_orders'},
              href: { name: "dtoDelivered" },
              title: "DTo-Delivered",
              // icon: 'fa fa-user',
            },
            {
              href: { name: "dtoBooked" },
              title: "DTO-Booked",
              // icon: 'fa fa-user',
            },
            {
              href: { name: "pendingrefunds" },
              title: "Pending Refunds",
              // icon: 'fa fa-user',
            },
          ],
        },
      ],
    };
  },
};
</script>
