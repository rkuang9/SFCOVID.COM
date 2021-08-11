function getData(state) {
    var orion = new OrionAjax('/controllers/us/get_state_data.php', 'get');
    orion.addParam('state', getStateAbbreviation(state));
    orion.getResponse(callback);
}



function callback(response) {
    let result = JSON.parse(response);

    $(document).ready(function () {
        $('#parent-table').DataTable({
            "sDom": "rtipl",
            pageLength: 25,
            "lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
            "searching": true,
            "bPaginate": true,
            "info": false,
            "data": result,
            autoWidth: false,
            "columns": [
                {"data": "submission_date"},
                {"data": "new_case", render: $.fn.dataTable.render.number(',', '.')},
                {"data": "tot_cases", render: $.fn.dataTable.render.number(',', '.')},
                {"data": "new_death", render: $.fn.dataTable.render.number(',', '.')},
                {"data": "tot_death", render: $.fn.dataTable.render.number(',', '.')}

            ],

            fixedColumns: {
                leftColumns: 1,
            },

            scrollX: true,
            scrollCollapse: true,
            scrollY: '80vh',

            "order": [[0, "desc"]],

            columnDefs: [
                {width: '15vw', targets: [0]},
                {targets: [0], className: 'bg-secondary text-white'},
                {targets: [1], className: 'text-left bg-warning'},
                {targets: [2], className: 'text-left bg-light'},
                {targets: [3], className: 'text-left bg-warning'},
                {targets: [4], className: 'text-white bg-danger'},
            ],
        });

        $('thead tr th').addClass("bg-dark text-white");
    });


    // why is jquery so weird?
    // give search box functionality since original box is disabled
    $(document).ready(function () {
        var table = $('#parent-table').DataTable();

        $('#searchbox').on('keyup', function () {
            table.search(this.value).draw();
        });
    });
}


function number_format(value) {
    return String(value).replace(/(.)(?=(\d{3})+$)/g, '$1,')
}