<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Results') }} {{$completed}} / {{$total}} Completed
        </h2>
    </x-slot>
    @include('message')
    @include('errors')

    <div class="pt-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Search Results</h1>

                    <canvas id="speedChart" width="600" height="600"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="pt-12 col-lg-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Search Results</h1>
                    <div class="pt-3">
                        <button class="btn btn-primary" id="select">Select All</button>
                        <button class="btn btn-warning" id="unselect">Unselect All</button>
                        <button class="btn btn-success" id="analyze">Analyze</button>
                    </div>
                    <div class="pt-2">

                        <div class="table-responsive">

                            <table class="table"  style=" width: 100%;">
                                <thead style=" width: 100%;">
                                <tr>
                                    <th></th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Url</th>
                                     <th>Rank/Iteration</th>
                                </tr>
                                </thead>
                                @if(count($tableData) == 0)
                                    <tr>
                                        <td colspan="5" style="text-align: center">No results found</td>
                                    </tr>
                                @endif
                                @foreach($tableData as $search)

                                        <tr>
                                            <td style="width: 4%;">
                                                <input class="site" type="checkbox" checked >
                                            </td>
                                            <td style=" width: 20%;">{{$search['title']}}</td>
                                            <td style=" width: 20%; word-wrap: break-word;min-width: 160px;max-width: 160px;">{{$search['description']}}</td>
                                            <td style=" width: 20%; word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                                Visit:
{{--                                                @if(count($search['url']) > 0)--}}
                                                    <a target="_blank" href="{{$search['url']}}">{{$search['url']}}</a><br>
{{--                                                    @if(count(array_unique($search['url'])) > 1)--}}

{{--                                                     <button class="btn btn-outline-primary btn-sm" onclick="displayUrls({{json_encode(array_unique($search['url']))}})">See All</button>--}}
{{--                                                    @endif--}}
{{--                                                @endif--}}

                                            </td>
                                            <td style=" width: 20%; word-wrap: break-word;min-width: 160px;max-width: 160px;">{{implode('|', $search['ranks'])}}</td>
                                        </tr>


                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">All Links</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="urls">

          </div>
          <div class="modal-footer">

          </div>
        </div>
      </div>
    </div>
    <x-analyze-result></x-analyze-result>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
        <script src="https://unpkg.com/chart.js@3"></script>
        <script src="https://unpkg.com/@sgratzl/chartjs-chart-boxplot@3"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
            var analyzeModal = new Vue({
                el: '#analyze_results',
                data: {
                    results:{}

                }
                });
        </script>
        <script>
            var data = {!! json_encode($graphData) !!};
            var boxplotDataFromServer = {!! json_encode($boxpot_data) !!};
            function randomValues(count, min, max) {
                const delta = max - min;
                return Array.from({length: count}).map(() => Math.random() * delta + min);
            }

            const boxplotData = {
                // define label tree
                // labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                labels: data.labels,
                datasets: [{
                    label: 'Ranks Volatility',
                    backgroundColor: 'rgba(255,0,0,0.5)',
                    borderColor: 'red',
                    borderWidth: 1,
                    outlierColor: '#999999',
                    padding: 10,
                    itemRadius: 0,
                    data: boxplotDataFromServer
                }]
            };
            window.onload = () => {
                const ctx = document.getElementById("speedChart").getContext("2d");
                window.myBar = new Chart(ctx, {
                    type: 'boxplot',
                    data: boxplotData,
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        legend: {
                            position: 'top',
                        },
                        scales: {
                            yAxis: {
                                display: false,
                            }
                        }
                    },
                });

            };

            // const config = {
            //     type: 'line',
            //     data: data,
            //     options: {
            //         responsive: true,
            //         stacked: false,
            //         plugins: {
            //             title: {
            //                 display: true,
            //                 text: 'Rank fluctuation graph'
            //             },
            //             legend: {
            //                 display: false
            //             },
            //             tooltip: {
            //                 enabled: true,
            //                 callbacks: {
            //                     // label: function(tooltipItems) {
            //                     //     tooltipItems.label = tooltipItems.label + " Iteration"
            //                     //     console.log(tooltipItems);
            //                     //     // return tooltipItems.yLabel + ' : ' + tooltipItems.xLabel + " Files";
            //                     // }
            //                 }
            //             },
            //             datalabels: {
            //                 function (context) {
            //                     return context.chart.isDatasetVisible(context.datasetIndex);
            //                 }
            //             },
            //         },
            //         scales: {
            //             y: {
            //                 type: 'linear',
            //                 display: true,
            //                 position: 'left',
            //                 stacked: false,
            //                  reverse: true,
            //                  beginAtZero: false,
            //                 ticks : {
            //                     min: 1,
            //                     max: 100,
            //                     stepSize : 5,
            //
            //                 }
            //             },
            //             x: {
            //                 ticks: {
            //                     min : 0,
            //                     beginAtZero: true
            //                 }
            //             }
            //         }
            //     },
            // };
            // var speedCanvas = document.getElementById("speedChart");
            //
            // var lineChart = new Chart(speedCanvas, config);
            //
            // function dataDisplay(event, label){
            //     console.log(label);
            //     console.log(event.target.checked);
            //      lineChart.data.datasets.forEach(function(ds) {
            //
            //
            // 	     if(ds.label == label){
            //
            // 	        if(event.target.checked){
            //                 ds.hidden = false;
            //             }else{
            //                  ds.hidden = true;
            //             }
            // 	        return false;
            // 	     }
            //
            //       });
            //       lineChart.update();
            // }
            //
            $("#select").on('click', function() {
            	$('.site').prop('checked', true);
                window.myBar.data.datasets.forEach(function(ds) {
                    ds.hidden = false;
                });
                window.myBar.update();
            });

            $("#unselect").on('click',function() {
            	$('.site').prop('checked', false);
                    window.myBar.data.datasets.forEach(function(ds) {
                        ds.hidden = true;

                    });
                window.myBar.update();
            });

            function displayUrls(urls){
                console.log(urls);
                 $('#urls').html('');
                var html = '';
                $.each(urls, function(index, url){
                    html += '<div style="width=100%"><a href="'+url+'">'+url+'</a></div><hr>'
                });
                $('#urls').html(html);
                $('#exampleModal').modal('show');
            }

            $("#analyze").on('click', function () {
                // HoldOn.open();
                var urls = [];
                $.each($('input[class="site"]:checked'), function (index, checkbox) {
                    urls.push(checkbox.value);
                });

                $('#analyze_results').modal('show');
                axios({
                    method: 'POST',
                    url: '{{url('analyze')}}'
                    , data: {
                        urls: urls,
                    }
                })
                    .then(response => {
                        HoldOn.close();
                        analyzeModal.results = response.data.data;

                    }).catch(e => {
                    HoldOn.close();
                    swal({
                        title: "Error",
                        text: e.response.data.message,
                        icon: "error",
                    });
                });

            });


        </script>
        @endpush
</x-app-layout>
