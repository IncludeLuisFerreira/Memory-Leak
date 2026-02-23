// Documento para tratar erros de login e cadastro com comunicação com o php



const errordiv = document.getElementById('error');
const params = new URLSearchParams(window.location.search);

// Erros de Login
if (params.get('erro') === 'email') {
    errordiv.innerHTML = '<p>E-mail não encontrado!</p>'
}
else if (params.get('erro') === 'senha') {
    errordiv.innerHTML = '<p>Senha incorreta!</p>'
}
else if (params.get('erro') === 'As senhas não coincidem') {
    errordiv.innerHTML = '<p>As senhas não coincidem!</p>'
    document.getElementById('nome').value = decodeURIComponent(params.get('nome'));
    document.getElementById('email').value = decodeURIComponent(params.get('email'));
}
else if (params.get('erro') === "E-mail já cadastrado.") {
    errordiv.innerHTML = '<p>E-mail já cadastrado</p>'  
    document.getElementById('nome').value = decodeURIComponent(params.get('nome'));

}
