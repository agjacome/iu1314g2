 //JavaScript Document

//Funciones confirmacion y validacion formularios

function insprodd(){ 

	if(validate_insprodd())
		document.insprod.submit();
} 

function registerr(){

	if (validate_registerr()) 
		document.register.submit();

}

function modifyuserr(){

	if(validate_modifyuserr()) 
		if(confirm('¿Estás seguro de modificar este usuario?')){
			document.modifyuser.submit();
		}
}

function deleteuserr(){

	if(confirm('¿Estás seguro de eliminar este usuario?')){
		//acción a realizar
	}
}

function modifyprodd(){
	if(validate_modifyprodd()) 
		if(confirm('¿Estás seguro de modificar este producto?')){
			document.modifyprod.submit();
		}
}

function deleteproductt(){

	if(confirm('¿Estás seguro de eliminar este usuario?')){
		//acción a realizar
	}
}

function insertSalee(){
	if(validate_insertSalee()){
		document.insertSale.submit();
	}
}

function insertBiddingg(){
	if(validate_insertBiddingg()){
		document.insertBidding.submit();
	}
}

//Validaciones de campos todos los campos de cada formularios 

function validate_registerr(){
	if(validarTexto(document.register.elements[0])&&validarTexto(document.register.elements[1])&&validarTexto(document.register.elements[2])&&validarEmail(document.register.elements[3])&&validarTexto(document.register.elements[4])&&validarTelefono(document.register.elements[5])&&validarTelefono(document.register.elements[6])&&validarTexto(document.register.elements[7])&&validarPassRepeat(document.register.elements[7],document.register.elements[8])&&validarTexto(document.register.elements[9])&&validarTexto(document.register.elements[10])&&validarTexto(document.register.elements[11]))
		return true;
	else return false;
}

function validate_modifyuserr(){
	if(validarTexto(document.register.elements[0])&&validarTexto(document.register.elements[1])&&validarTexto(document.register.elements[2])&&validarEmail(document.register.elements[3])&&validarTexto(document.register.elements[4])&&validarTelefono(document.register.elements[5])&&validarTelefono(document.register.elements[6])&&validarTexto(document.register.elements[7])&&validarPassRepeat(document.register.elements[7],document.register.elements[8])&&validarTexto(document.register.elements[9])&&validarTexto(document.register.elements[10])&&validarTexto(document.register.elements[11]))
		return true;
	else return false;
}

function validate_modifyprodd(){
	if(validarTexto(document.insprod.elements[0])&&validarTexto(document.insprod.elements[1]))
		return true;
	else return false;
}

function validate_insertSalee(){
	if(validarPrecio(document.insertSale.elements[0])&&validarUnidades(document.insertSale.elements[1]))
		return true;
	else return false;
}

function validate_insertBiddingg(){
	if(validarPrecio(document.insertBiddingg.elements[0])&&validarFecha(document.insertBiddingg.elements[1])&&validarFechaEsPosterior(document.elements[1]))
		return true;
	else return false;
}
function validate_insprodd(){

	if(validarFecha(document.insprod.elements[5])&&validarFechaEsPosterior(document.insprod.elements[5])&&validarTexto(document.insprod.elements[0])&&validarTexto(document.insprod.elements[1])&&validarPrecio(document.insprod.elements[2]))
		return true;
	else return false;
}



//Validaciones de campos

function validarFecha(fecha){
	// expresion regular para comprobar que es una fecha válida
    var re = /^\d{4}\-\d{2}\-\d{2}$/;

    if(fecha.value != '' && !fecha.value.match(re)) {
      alert("Fecha invalida: " + fecha.value);
      return false;
    }
    else return true;
}

function validarFechaEsPosterior(fecha){
	var hoy=new Date();
	year=fecha.value.slice(0,4);
	mes=fecha.value.slice(5,7);
	mes--;
	dia=fecha.value.slice(8,10);
	var fechapost= new Date();
	fechapost.setFullYear(year, mes, dia);
	if(fecha<hoy) return false;
	else return true;
}

function validarTexto(text){
	if (text.value   == '') {
	 	alert ('Campo vacío'); 
		return false;
	else
		return true
}

function validarPrecio(price){
	var value= precio.value;
	if(isNaN(value)){
		alert('Precio no válido');
		return false;
	}
	else
		return true;
}

function validarEmail(email){
	var value= email.value;
	if(/^\w+([\.-_]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3,4})+$/.test(value))
		return true;
	else{
		alert('El email'+ value +'no es válido');
		return false;
	}
}

function validarTelefono(telefono){
	var value=telefono.value;
	if( !(/^\d{9}$/.test(value)) ) {
  		alert('Telefono no valido');
  		return false;
	}
	else
		return true;

}

function validarPassRepeat(pass1, pass2){
	var value1=pass1.value;
	var value2=pass2.value;
	if(value1!=value2){
		alert('Las contraseñas no coinciden');
		return false;
	}
	else 
		return true;
}

function validarUnidades(unidades){
	var value=unidades.value;
	if(!(/^[0-9]+/.test(value)){
		alert('Unidades en formato no correcto');
		return false;
	}
	else
		return true;
}
