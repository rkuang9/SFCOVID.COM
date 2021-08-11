getData();


function getData() {
    var orion = new OrionAjax('/controllers/us/get_us_data.php', 'get');
    orion.getResponse(callback);
}


function callback(response) {
    let result = JSON.parse(response);

    var states_array = [];
    let total_new_today = 0;
    let total_cumulative = 0;

    for (let i = 0; i < result.length; i++) {
        total_new_today += parseInt(result[i].nc);
        total_cumulative += parseInt(result[i].tc);

        states_array.push([
            // display country code as country name
            //{v: 'CA', f: 'test'},
            (result[i].s),
            parseInt((result[i].nc)),
            showInfoBox(number_format(result[i].nc), number_format(result[i].tc),
                number_format(result[i].nd), number_format(result[i].td))
        ])
    }

    document.getElementById('new_cases_total').innerText = number_format(total_new_today);
    document.getElementById('cumulative_total').innerText = number_format(total_cumulative);


    /** Display Map **/
    google.charts.load('current', {
        'packages': ['geochart'],
        'mapsApiKey': 'AIzaSyBgu_2DZKBsDxhfhj_UzUVvR_kP5GDQ2NQ'
        //'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
    });

    google.charts.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {
        //var data = new google.visualization.arrayToDataTable(country_array);
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'State');
        data.addColumn('number', 'nc');
        data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
        data.addRows(states_array);

        var options = {
            legend: 'false',
            region: 'US',
            resolution: 'provinces',
            tooltip: {
                textStyle: {
                    fontSize: 15,
                    fontWeight: 'bold'
                },
                isHtml: true
            },
            colorAxis: {colors: ['#eac9c9', '#b13434', '#981616']},
        };

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
        chart.draw(data, options);
    }

    /** end map **/

    // info box width is a function of height since most displays are longer than they are wide
    function showInfoBox(new_cases, total_cases, new_deaths, total_deaths) {
        /*return '<div class="container-fluid flex-wrap" style="width: 25vh; font-size: 15px">' + '' +
                    '<div class="row"> ' +
                        '<div class="col-6 d-flex justify-content-end" style="color: #000000">New Cases</div>' +
                        '<div class="col-6 d-flex justify-content-start" style="font-weight: bold">' + new_cases + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-6 d-flex justify-content-end" style="color: #000000">Total Cases</div>' +
                        '<div class="col-6 d-flex justify-content-start" style="font-weight: bold">' + total_cases + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-6 d-flex justify-content-end " style="color: #000000">New Deaths</div>' +
                        '<div class="col-6 d-flex justify-content-start" style="font-weight: bold">' + new_deaths + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="col-6 d-flex justify-content-end">Total Deaths</div>' +
                        '<div class="col-6 d-flex justify-content-start" style="font-weight: bold">' + total_deaths + '</div>' +
                    '</div>' +
                '</div>';*/
        return '<div style="width: 25vh">' +
            'New Cases: <b>' + new_cases + '</b><br>' +
            'Total Cases: <b>' + total_cases + '</b><br>' +
            'New Deaths: <b>' + new_deaths + '</b><br>' +
            'Cumulative Deaths: <b>' + total_deaths + '</b></div>';
    }




    //https://datatables.net/forums/discussion/52701/styling-the-pagination-element-with-bootstrap-4
    $(document).ready(function () {


        $('#parent-table').DataTable({
            "sDom": "rtipl",
            pageLength: 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": true,
            "bPaginate": true,
            "info": false,
            "data": result,
            autoWidth: false,
            "columns": [
                {
                    "data": "s",
                    "render": function (data) {
                        // fixed country name column width and allow long names to wrap
                        data = '<div class="" style="width:20vw; display: block; word-wrap: break-word; white-space: normal">' +
                            '<a style="color:black" href="/views/statedetail.php?state=' + data + '">' + data + '</a></div>';

                        return data;
                    }
                },
                {"data": "nc", render: $.fn.dataTable.render.number(',', '.')},
                {"data": "nd", render: $.fn.dataTable.render.number(',', '.')},
                {"data": "tc", render: $.fn.dataTable.render.number(',', '.')},
                {"data": "td", render: $.fn.dataTable.render.number(',', '.')}

            ],

            fixedColumns: {
                heightMatch: 'none',
                leftColumns: 1,
            },

            scrollX: true,
            scrollCollapse: true,
            scrollY: '80vh',

            "order": [[1, "desc"]],

            columnDefs: [

                {"title" : "State", "targets": 0},
                {"title" : "New Cases", "targets": 1},
                {"title" : "New Deaths", "targets": 2},
                {"title" : "Total Cases", "targets": 3},
                {"title" : "Total Deaths", "targets": 4},

                {width: '20vw', targets: [0]},
                //{targets: [0], className: 'bg-secondary text-white'},
               // {targets: [1], className: 'text-left bg-warning'},
              // {targets: [2], className: 'text-left bg-light'},
             //   {targets: [3], className: 'text-left bg-warning'},
             //   {targets: [4], className: 'text-white bg-danger'},

                //{targets: [-1, -2], className: 'text-right bg-success text-white'}
            ],
        });

        $('thead tr th').addClass("bg-secondary text-white");
    });


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