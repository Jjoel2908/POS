const URL = "../../../controllers/login.php";

$("form#formLogin").on("submit", (e) => {

	e.preventDefault();
	let user = $("#user").val();
	let password = $("#password").val();

  	if (user == "") showAlert(false, "El nombre de usuario es requerido");
  		else if (password == "") showAlert(false, "La contraseña es requerida");
  			else {
				$.post(URL, { user: user, password: password }, (a) => {

					data = JSON.parse(a);					
					data.success
					? window.location = window.location.origin + data.url
					: showAlert(false, "Las credenciales son incorrectas");
				});
			}
});


/** Mostrar Alerta
 * @param {boolean} success Identificador para saber si la acción realizada fue exitosa.
 * @param {string} message Mensaje de satisfacción o mensaje de error
 */
const showAlert = (success, message) => {

	let a = success ? "success" : "error";
	let i = success ? "bx bx-check-circle" : "bx bx-x-circle";
 
	Lobibox.notify(a, {
	  size: "mini",
	  icon: i,
	  position: "bottom right",
	  msg: '<p class="my-1">'+  message + '</p>',
	  sound: "",
	});
 
 };