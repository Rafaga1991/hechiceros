function onClickSelect(value) {
    var id = value.value;

    if (value.checked) {
        player.innerText = parseInt(player.innerText) + 1;
        let input = document.createElement('input');
        input.type = 'hidden';
        input.value = id;
        input.name = 'player[]'
        form.appendChild(input);
    } else {
        player.innerText = parseInt(player.innerText) - 1;
        $(`form>input[value="${id}"`).remove();
    }
}
