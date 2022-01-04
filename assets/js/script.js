(()=>{
    var property = 'modal-click';

    var modals = document.querySelectorAll(`[${property}]`);

    modals.forEach((modal) => {
        var elementId = modal.getAttribute(property);
        modal.onclick = () => {
            var m = document.getElementById(elementId);
            m.setAttribute('modal', 'show');

            m.querySelector('[modal=close]').onclick = () => {
                m.setAttribute('modal', 'hidden');
            }

            m.querySelector('[modal=cancel]').onclick = () => {
                m.setAttribute('modal', 'hidden');
            }
        }
    });

    let startIntervalNotification = false;
    var notifications = [];

    /**
     * Muestra notificaciones mediante datos recividos
     *
     * @param {string} title titulo de la notificacion
     * @param {string} description descripcion de la notificacion
     * @param {string} idElement id del elemento donde se debe mostrar
     * @param {int} second segundos de duracion de la notificacion
     * @author Rafael Minaya
     * @access public
     * @returns {void} sin retorno
     * @copyright R.M.B
     *
     */
    function showNotification(title, description, idElement = null, second = 5) {
        var alert = document.createElement('div');
        var div1 = document.createElement('div');
        var div2 = document.createElement('div');
        var close = document.createElement('span');

        alert.setAttribute('alert', 'pop-up');
        alert.onwebkittransitionend = () => {
            if (element.hasChildNodes(alert)) {
                element.removeChild(alert);
            }
        }

        close.innerHTML = '&times;';
        close.setAttribute('alert', 'close');
        close.onclick = () => {
            if (element.hasChildNodes(alert)) {
                element.removeChild(alert);
            }
        }

        div1.setAttribute('alert', 'title');
        div1.textContent = title;

        div2.setAttribute('alert', 'description');
        div2.textContent = description;

        alert.appendChild(div1);
        alert.appendChild(div2);
        alert.appendChild(close);

        var element = document.getElementById(idElement);

        notifications.push({
            id: alert.id = 'ntf_' + (notifications.length + 1),
            element: alert,
            second: second,
            opacity: 100,
            delete: false
        })

        element.appendChild(alert);

        if (!startIntervalNotification) {
            startIntervalNotification = true;
            setInterval(() => {
                notifications.forEach((notification) => {
                    if (!notification.delete && notification.second == 0) {
                        let thread = setInterval(() => {
                            if (notification.opacity > 0) {
                                if (document.getElementById(notification.id) !== null) {
                                    document.getElementById(notification.id).style.opacity = `${notification.opacity--}%`;
                                }
                            } else {
                                notification.delete = true;
                                clearInterval(thread);
                            }
                        }, 5);
                    }
                    notification.second = !notification.delete ? --notification.second : -1;
                })
            }, 1000);
        }
    }

    // ocultando y mostrando chat al hacer click en el header
    var chats = document.querySelectorAll('[chat~=header]');
    chats.forEach((chat) => {
        chat.onclick = () => {
            var valueChat = chat.getAttribute('chat').split(' ');
            if (valueChat.includes('hidden')) {
                valueChat = valueChat.filter((value) => {
                    return value !== 'hidden';
                })
                chat.setAttribute('chat', valueChat.join(' '));
                chat.parentNode.style.height = '20rem';
                chat.parentNode.style.marginTop = '0'
            } else {
                chat.setAttribute('chat', (chat.getAttribute('chat') + ' hidden'));
                chat.parentNode.style.height = '2.5rem';
                chat.parentNode.style.marginTop = '17.5rem'
            }
        }
    });

    // colocando el scroll al final en cada chat
    document.querySelectorAll('[chat~=content] [chat~=body]').forEach((chatBody) => {
        chatBody.scrollTop = (chatBody.scrollHeight - chatBody.clientHeight);
    });

    /**
     * cambia la etiqueta de un elemento cuando la longitud del texto es mayor a 25.
     *
     * @returns {void} sin retorno
     * @author Rafael Minaya
     * @access public
     * @copyright R.M.B
     *
     */
    function resetChat() {
        document.querySelectorAll('[chat~=body] [message~=description]').forEach((message) => {
            if (message.textContent.length >= 25) {
                var div = document.createElement('div');
                message.getAttributeNames().forEach((attr) => {
                    div.setAttribute(attr, message.getAttribute(attr));
                });
                div.innerHTML = message.innerHTML;
                message.parentNode.replaceChild(div, message);
            }
        });
    }

    resetChat();

    function show_hidden(array) {
        var sing = '';
        if (array.includes('hidden')) {
            array = array.filter((val) => {
                return val != 'hidden';
            });
            array.push('show');
            sing = '-';
        } else if (array.includes('show')) {
            array = array.filter((val) => {
                return val != 'show';
            });
            array.push('hidden');
            sing = '+';
        }

        return {
            arr: array,
            sing: sing
        }
    }

    document.querySelectorAll('[accordion~=content] [accordion~=header]').forEach((accordion) => {
        accordion.onclick = () => {
            var description = accordion.parentNode.querySelector('[accordion~=description]');
            var value = description.getAttribute('accordion');
            var more = accordion.querySelector('[accordion~=more]');

            value = show_hidden(value.split(' '));
            more.textContent = value.sing;

            description.setAttribute('accordion', value.arr.join(' '));

            document.querySelectorAll('[accordion~=content]').forEach((accordions) => {
                if (accordion.parentNode != accordions) {
                    var description = accordions.querySelector('[accordion~=description]');
                    var value = description.getAttribute('accordion').split(' ');
                    var more = accordions.querySelector('[accordion~=header] [accordion~=more]');

                    if (value.includes('show')) {
                        value = value.filter((val) => {
                            return val != 'show';
                        });
                        value.push('hidden');
                        more.textContent = '+';
                    }

                    description.setAttribute('accordion', value.join(' '));
                }
            });
        }
    });

    setInterval(() => {
        document.querySelectorAll('[progress]').forEach((element) => {
            element.innerHTML = '';
            var percent = element.getAttribute('progress');

            if (percent > 100) {
                percent = 100;
                element.setAttribute('progress', percent);
            }

            var color = element.getAttribute('color');
            var div = document.createElement('div');

            div.style.backgroundColor = color;
            div.style.width = `${percent}%`;
            div.setAttribute('pt-1', '');
            div.setAttribute('pb-1', '');

            element.appendChild(div);
        }, 100);

    });
})()

function exit(){
    $.post('login/close', ()=>{
        location.reload();
    });
}