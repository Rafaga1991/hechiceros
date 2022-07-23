function onClickSelect(value) {
    if (value.checked) player.innerText = parseInt(player.innerText) + 1;
    else player.innerText = parseInt(player.innerText) - 1;
}
