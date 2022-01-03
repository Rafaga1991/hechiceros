$(document).ready(()=>{
    $('#login').click(()=>{
        footer.innerHTML = '';
        $.post(
            'login/isUser', {
                username: username.value,
                password: password.value,
                __token: __token.value
            }, (data)=>{
            data = JSON.parse(data);
            var ul = document.createElement('ul');
            if(data.access){
                var li = document.createElement('li');
                if(data.state === 'allow') location.reload();
                else if(data.state === 'block') li.textContent = 'Usuario bloqueado.';
                else if(data.state === 'denied') li.textContent = 'Usuario y/o clave incorrectos.';
                li.setAttribute('text-color', 'danger');
                ul.appendChild(li);
            }else{
                data.message.forEach((ms)=>{
                    var li = document.createElement('li');
                    li.textContent = ms;
                    li.setAttribute('text-color', 'danger');
                    ul.appendChild(li);
                })
            }
            footer.appendChild(ul);
        })
    })
})