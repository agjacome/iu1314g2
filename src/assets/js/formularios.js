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

	if(!(confirm('¿Estás seguro de eliminar este usuario?'))){
		return false;
	}
}

function modifyprodd(){
	if(validate_modifyprodd()) 
		if(confirm('¿Estás seguro de modificar este producto?')){
			document.modifyprod.submit();
		}
}

function deleteproductt(){

	if(!(confirm('¿Estás seguro de eliminar este usuario?'))){
		return false;
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

function buyProdd(){
	if(validate_buyProdd()){
		document.buyProd.submit();
	}

}

function bidProdd(){
	if(validate_bidProdd()){
		document.bidProd.submit();
	}
}

function payy(){

	if(validate_payy()){
		document.pay.submit();
	}
}

function changeComm(){

	if(validate_comm()){
		document.com.submit();
	}
}

//Validaciones de campos todos los campos de cada formularios 

function validate_registerr(){
	if(validarTexto(document.register.elements[0])&&validarTexto(document.register.elements[1])&&validarPassRepeat(document.register.elements[1], document.register.elements[2])&&validarEmail(document.register.elements[3])&&validarTexto(document.register.elements[4])&&validarTexto(document.register.elements[5])&&validarTelefono(document.register.elements[6]))
		return true;
	else return false;
}

function validate_modifyuserr(){
	if(validarTexto(document.modifyuser.elements[0])&&validarTexto(document.modifyuser.elements[4])&&validarTexto(document.modifyuser.elements[5])&&validarEmail(document.modifyuser.elements[3])&&validarTexto(document.modifyuser.elements[6])&&validarTelefono(document.modifyuser.elements[6]))
		return true;
	else return false;
}

function validate_modifyprodd(){
	if(validarTexto(document.modifyprod.elements[0])&&validarTexto(document.modifyprod.elements[1]))
		return true;
	else return false;
}

function validate_insertSalee(){
	if(validarPrecio(document.insertSale.elements[0])&&validarUnidades(document.insertSale.elements[1]))
		return true;
	else return false;
}

function validate_insertBiddingg(){
	if(validarPrecio(document.insertBidding.elements[0])&&validarFecha(document.insertBidding.elements[1])&&validarFechaEsPosterior(document.insertBidding.elements[1]))
		return true;
	else return false;
}
function validate_insprodd(){

    if (validarTexto(document.insprod.elements[0]) && validarTexto(document.insprod.elements[1]))
		return true;
	else return false;
}

function validate_buyProdd(){
	if(validarUnidades(document.buyProd.elements[0])&&validarFormasPago(document.buyProd.elements[3], document.buyProd.elements[2]))
		return true;
	else return false;
}
function validate_bidProdd(){
	if(validarPrecio(document.bidProd.elements[0]))
		return true;
	else return false;
}
function validate_payy(){
	if(validarFormasPago(document.pay.elements[2], document.pay.elements[1]))
		return true;
	else return false;	
}
function validate_comm(){
	if(validarPorcentaje(document.com.elements[0]))
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
	var year=fecha.value.slice(0,4);
	var mes=fecha.value.slice(5,7);
	mes--;
	var dia=fecha.value.slice(8,10);
	var fechapost= new Date();
	fechapost.setFullYear(year, mes, dia);
	if(fechapost<hoy) return false;
	else return true;
}

function validarTexto(text){
	if (text.value  == '') {
	 	alert ('Campo vacío'); 
		return false;
    } else
		return true;
}

function validarPrecio(price){
	var value= price.value;
	if(isNaN(value)){
		alert('Precio no válido');
		return false;
	}
	else
		return true;
}

function validarEmail(email){
	var value= email.value;
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value))
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
	if(!(/^[0-9]+/.test(value))){
		alert('Unidades en formato no correcto');
		return false;
	}
	else
		return true;
}

function validarFormasPago(tarj, payPal){
	var value1=payPal.value;
	var value2=tarj.value;

	if(value1 != '' && value2 != ''){
		alert('Dos formas de pago elegidas, elimine una');
		return false;
	}
	else{
		if(value1 == ''){
			return validarTarjeta(value2);
        } else
			return validarPayPal(value1);	
	}
}

function validarPayPal(cuenta){
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(cuenta))
        return true
    else  {
        alert("La cuenta de PayPal " + cuenta + " no es válida");
        return false;
    }
}

function validarTarjeta(tarjeta){
	var value=tarjeta;
	if(/^[0-9]{16}/.test(value)){
		return true;
	}
		else{
			alert('Tarjeta de pago no valida');
			return;
		}
}

function validarPorcentaje(percent){
	var value= percent.value;
	if(isNaN(value)){
		alert('Porcentaje no válido');
		return false;
	}
	else
		return true;
}
