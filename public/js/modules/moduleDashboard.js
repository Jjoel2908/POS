const urlController = "../../../controllers/";

const fetchTopSellingProducts = async () => {
   try {
      /** Creamos un objeto FormData para enviar la solicitud */
      let formData = new FormData();
      formData.append("module", "Dashboard");
      formData.append("operation", "fetchTopSellingProducts");

      /** Enviamos la solicitud al servidor */
      const response = await fetch(urlController, {
         method: "POST",
         body: formData,
      });

      /** Verificamos si la respuesta es válida */
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

      /** Convertimos la respuesta a JSON */
      const data = await response.json();

      if (data.success) {
         /** Extraemos los productos y cantidades desde el array 'data' */
         const products = data.data.map(item => item.product); // Extraemos los nombres de los productos
         const quantities = data.data.map(item => item.quantity); // Extraemos las cantidades de los productos

         /** Obtenemos el contexto del gráfico */
         const ctx = document.getElementById("products-best-selling").getContext("2d");

         /** Función para generar gradientes dinámicos */
         const createGradient = (colorStart, colorEnd) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, colorStart);
            gradient.addColorStop(1, colorEnd);
            return gradient;
         };

         /** Generamos gradientes dinámicos basados en la cantidad de productos */
         const gradients = [];
         const colors = [
            ["#f093fb", "#f5576c"],
            ["#00c6fb", "#005bea"],
            ["#2af598", "#009efd"]
         ];

         for (let i = 0; i < products.length; i++) {
            const colorPair = colors[i % colors.length]; // Repite los colores si son menos que los productos
            gradients.push(createGradient(colorPair[0], colorPair[1]));
         }

         /** Crear el gráfico */
         new Chart(ctx, {
            type: 'pie',
            data: {
               labels: products,
               datasets: [{
                  backgroundColor: gradients,
                  hoverBackgroundColor: gradients,
                  data: quantities
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
      } else {
         /** Mostrar mensaje si no hay datos */
         document.getElementById("message-product").innerHTML = "No existe información disponible";
      }
   } catch (error) {
      document.getElementById("message-product").innerHTML = "Hubo un error al cargar los productos más vendidos. Intenta de nuevo más tarde.";
   }
}