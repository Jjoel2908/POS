let URL_DASHBOARD = "../../../controllers/dashboard.php?";

$(() => {
   moduleDashboard.productStockMin();
   moduleDashboard.productBestSelling();
});

let moduleDashboard = {

   /**  S T O C K   M I N I M U M  */
   productStockMin: () => {
      $.post(URL_DASHBOARD + 'op=productStockMin', e => {

         let response = JSON.parse(e);
         if (response.success) {

            var ctx = document.getElementById("chart-stock-minimo").getContext("2d");

            var gradientStroke8 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke8.addColorStop(0, "#84fab0");
            gradientStroke8.addColorStop(1, "#8fd3f4");

            var gradientStroke9 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke9.addColorStop(0, "#f093fb");
            gradientStroke9.addColorStop(1, "#f5576c");

            var gradientStroke10 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke10.addColorStop(0, "#4facfe");
            gradientStroke10.addColorStop(1, "#00f2fe");

            var gradientStroke11 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke11.addColorStop(0, "#4481eb");
            gradientStroke11.addColorStop(1, "#04befe");

            var gradientStroke12 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke12.addColorStop(0, "#FFFEFF");
            gradientStroke12.addColorStop(1, "#D7FFFE");

            var gradientStroke13 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke13.addColorStop(0, "#f9d5e5");
            gradientStroke13.addColorStop(1, "#ee9ca7");
   
            var gradientStroke14 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke14.addColorStop(0, "#90f7ec");
            gradientStroke14.addColorStop(1, "#32CCBC");
   
            var gradientStroke15 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke15.addColorStop(0, "#fff1eb");
            gradientStroke15.addColorStop(1, "#ace0f9");
   
            var gradientStroke16 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke16.addColorStop(0, "#ff758c");
            gradientStroke16.addColorStop(1, "#ff7eb3");
   
            var gradientStroke17 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke17.addColorStop(0, "#f6d365");
            gradientStroke17.addColorStop(1, "#fda085");
   
            var gradientStroke18 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke18.addColorStop(0, "#b8cbb8");
            gradientStroke18.addColorStop(1, "#b8cbb8");
   
            var gradientStroke19 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke19.addColorStop(0, "#f093fb");
            gradientStroke19.addColorStop(1, "#f5576c");
   
            var gradientStroke20 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke20.addColorStop(0, "#f6e4ea");
            gradientStroke20.addColorStop(1, "#f6e4ea");

            let product  = response.product;
            let quantity = response.quantity;

            new Chart(ctx, {
               type: 'pie',
               data: {
                  labels: product,
                  datasets: [{
                     backgroundColor: [
                        gradientStroke8,
                        gradientStroke9,
                        gradientStroke10,
                        gradientStroke11,
                        gradientStroke13,
                        gradientStroke14,
                        gradientStroke15,
                        gradientStroke16,
                        gradientStroke17,
                        gradientStroke18,
                        gradientStroke19,
                        gradientStroke20
                     ],

                     hoverBackgroundColor: [
                        gradientStroke8,
                        gradientStroke9,
                        gradientStroke10,
                        gradientStroke11,
                        gradientStroke13,
                        gradientStroke14,
                        gradientStroke15,
                        gradientStroke16,
                        gradientStroke17,
                        gradientStroke18,
                        gradientStroke19,
                        gradientStroke20
                     ],
                     data: quantity
                  }]
               },
               options: {
                  maintainAspectRatio: false,
                  legend: {
                     display: true,
                  },
                  tooltips: {
                     displayColors: false
                  }
               }
            });

         } else $("#message").html(response.message);
      })
   },

   /**  B E S T   S E L L I N G  */
   productBestSelling: () => {
      $.post(URL_DASHBOARD + 'op=productBestSelling', e => {

         let response = JSON.parse(e);
         if (response.success) {

            var ctx = document.getElementById("products-best-selling").getContext("2d");

            var gradientStroke8 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke8.addColorStop(0, "#f093fb");
            gradientStroke8.addColorStop(1, "#f5576c");

            var gradientStroke9 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke9.addColorStop(0, "#00c6fb");
            gradientStroke9.addColorStop(1, "#005bea");

            var gradientStroke10 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke10.addColorStop(0, "#2af598");
            gradientStroke10.addColorStop(1, "#009efd");

            let product  = response.product;
            let quantity = response.quantity;

            new Chart(ctx, {
               type: 'doughnut',
               data: {
                  labels: product,
                  datasets: [{
                     backgroundColor: [
                           gradientStroke8,
                           gradientStroke9,
                           gradientStroke10
                     ],

                     hoverBackgroundColor: [
                           gradientStroke8,
                           gradientStroke9,
                           gradientStroke10
                     ],
                     data: quantity
                  }]
               },
               options: {
                  maintainAspectRatio: false,
                  legend: {
                     display: true,
                  },
                  tooltips: {
                     displayColors: false
                  }
               }
            });

         } else $("#message-product").html(response.message);
      })
   }
};