window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    // const datatablesSimple = document.getElementById('datatablesSimple');
    document.querySelectorAll('[class~="datatablesSimple"]').forEach((datatablesSimple)=>{
        new simpleDatatables.DataTable(datatablesSimple);
    });
});
