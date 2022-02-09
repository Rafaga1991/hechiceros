function onClickSelect(value) {
    var id = value.value.replace('#', '');

    if (value.checked) {
        player.innerText = parseInt(player.innerText) + 1;
        let input = document.createElement('input');
        input.type = 'hidden';
        input.id = `add_${id}`;
        input.value = value.value;
        input.name = 'player[]'
        form.appendChild(input);
    } else {
        player.innerText = parseInt(player.innerText) - 1;
        $(`#add_${id}`).remove();
    }

}