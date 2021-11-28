<x-app-layout>
    @push('css')
        <style>
            .scroll-table {
                display: block;
                max-width: 100%;
                width : 100%;
                margin: 0 auto;
                overflow-x: auto;
                white-space: nowrap;
            }
            #analytictable tr:nth-child(odd){
                background-color: #dee2e6;
            }
            #analytictable tr:nth-child(even) > td:nth-child(2){
                background-color: white;
            }
            #analytictable tr:nth-child(odd) > td:nth-child(2){
                background-color: #dee2e6;
            }
            #analytictable tr:nth-child(even) > td:last-child{
                background-color: cornflowerblue;
            }
            #analytictable tr:first-child > td:last-child{
                background-color: #dee2e6 !important;
            }
            #analytictable tr:nth-child(odd) > td:last-child{
                background-color: lightblue;
            }
            .freezecol {
                position: sticky;
                width: 5em;
                left: 0;
                top: auto;
                background-color: white;
            }
            .freezesecondcol {
                position: sticky;
                width: 5em;
                left: 188px;
                top: auto;
            }
            .freezelastcol {
                position: sticky;
                left: 0;
                right: 0;
                top: auto;
            }
        </style>
    @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analyzers Results') }}
        </h2>
        <a class="btn btn-info btn-sm" href="{{route('search_results', $id)}}">Back</a>
    </x-slot>
    @include('message')
    @include('errors')



    <div class="py-3" id="analyzer_results">
        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                            @{{ completedRequests }} / @{{ totalRequests }} Requests are completed
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                            Analytics
                            {{--                            <button style="margin-left: 3px;" type="button" onclick="$('#export_form').submit();"--}}
                            {{--                                    class="btn btn-sm btn-info pull-right">Export--}}
                            {{--                            </button>--}}
                            {{--                            <form action="{{route('export_collective_results')}}" method="POST" id="export_form">--}}
                            {{--                                @csrf()--}}
                            {{--                                <input type="hidden" name="data" v-model="JSON.stringify(collective_results)"/>--}}
                            {{--                            </form>--}}
                        </h1>
                        <div class="card">
                            <div class="card-body">

                                <table class="table table-bordered scroll-table " id="analytictable" style=" width: 100%;">
                                    <tbody>
                                    <tr>
                                        <td width="15%" class="freezecol"></td>
                                        <td width="10%" class="freezesecondcol">Average</td>
                                        <td v-for="(item, index) in analytics_data.wordCount" v-if="index >= 1 && index < analytics_data.wordCount.length -1">
                                            <a :href="item.url" target="_blank">Result @{{ index }}</a>
                                        </td>
                                        <td width="10%" class="freezelastcol">
                                            <a :href="analytics_data.wordCount[analytics_data.wordCount.length -1].url" target="_blank">Compare With</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="freezecol">Word Count</td>
                                        <td class="freezesecondcol">@{{ analytics_data.wordCount[0] }}</td>
                                        <td v-for="(item, index) in analytics_data.wordCount" v-if="index >= 1 && index < analytics_data.wordCount.length -1">
                                            @{{ item.count }}
                                        </td>
                                        <td class="freezelastcol">@{{ analytics_data.wordCount[analytics_data.wordCount.length -1].count }}</td>
                                    </tr>
                                    <tr>
                                        <td class="freezecol">Keywords in Sentence</td>
                                        <td class="freezesecondcol">@{{ analytics_data.keyword[0] }}</td>
                                        <td v-for="(item, index) in analytics_data.keyword" v-if="index >= 1 && index < analytics_data.keyword.length -1">
                                            @{{ item.count }}
                                        </td>
                                        <td class="freezelastcol">@{{ analytics_data.keyword[analytics_data.keyword.length -1].count }}</td>
                                    </tr>
                                    <tr>
                                        <td class="freezecol">Entities in Sentence</td>
                                        <td class="freezesecondcol">@{{ analytics_data.entites[0] }}</td>
                                        <td v-for="(item, index) in analytics_data.entites" v-if="index >= 1 && index < analytics_data.entites.length -1">
                                            @{{ item.count }}
                                        </td>
                                        <td class="freezelastcol">@{{ analytics_data.entites[analytics_data.entites.length -1].count }}</td>
                                    </tr>
                                    <tr>
                                        <td class="freezecol">LSI Words in Sentence</td>
                                        <td class="freezesecondcol">@{{ analytics_data.lsi[0] }}</td>
                                        <td v-for="(item, index) in analytics_data.lsi" v-if="index >= 1 && index < analytics_data.lsi.length -1">
                                            @{{ item.count }}
                                        </td>
                                        <td class="freezelastcol">@{{ analytics_data.lsi[analytics_data.lsi.length -1].count }}</td>
                                    </tr>
                                    <tr>
                                        <td class="freezecol">Relevant Words</td>
                                        <td class="freezesecondcol">@{{ analytics_data.rw[0] }}</td>
                                        <td v-for="(item, index) in analytics_data.rw" v-if="index >= 1 && index < analytics_data.rw.length -1">
                                            @{{ item.count }}
                                        </td>
                                        <td class="freezelastcol">@{{ analytics_data.rw[analytics_data.rw.length -1].count }}</td>
                                    </tr>
                                    <tr>
                                        <td class="freezecol">Relevant Density</td>
                                        <td class="freezesecondcol">@{{ analytics_data.rd[0] }}%</td>
                                        <td v-for="(item, index) in analytics_data.rd" v-if="index >= 1 && index < analytics_data.rd.length -1">
                                            @{{ item.count }}%
                                        </td>
                                        <td class="freezelastcol">@{{ analytics_data.rd[analytics_data.rd.length -1].count }}%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Results</h1>
                    <div class="accordion" id="accordionResults">
                        <div v-for="(analyzers, index) in results" class="card">
                            <div class="card-header" :id="'heading-'+index">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                            :data-target="'#collapse'+index" aria-expanded="true"
                                            :aria-controls="'collapse'+index">
                                        @{{ analyzers.url }}
                                    </button>
{{--                                    --}}
                                    <a :href="''+analyzers.url" target="_blank" class="btn btn-sm btn-info pull-right">View
                                        Link</a>
                                </h5>
                            </div>
                            <div :id="'collapse'+index" class="collapse show" :aria-labelledby="'heading-'+index"
                                 data-parent="#accordionResults">
                                <div class="card-body">
                                    <div v-for="(entities, analyzer) in analyzers.data" class="card">
                                        <div class="card-body">
                                            <h3 class="card-title"><b>@{{ analyzer }}</b></h3>
                                            <table class="table table-bordered" style=" width: 100%;">
                                                <thead style=" width: 100%;" class="thead-light">
                                                <tr>
                                                    <th>Entity</th>
                                                    <th>Matched Text</th>
                                                    <th>Analyzer Frequency</th>
                                                    <th>Custom Frequency</th>
                                                    <th>Confidence Score</th>
                                                    <th>Relevance Score</th>
                                                    <th>Entity Type</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr  v-if="entities.entitiesData.length > 0 && Object.keys(entities.entitiesData[0]).includes('error')">
                                                    <td colspan="7">
                                                        @{{ entities.entitiesData[0].error }}
                                                    </td>
                                                </tr>
                                                <tr v-else v-for="(data, entity) in _.orderBy(entities.entitiesData, ['htmlCount'], ['desc'])">
                                                    <td style=" width: 10%;">@{{data.entity}}</td>
                                                    <td style=" width: 5%;">@{{data.matchedText}}</td>
                                                    <td style=" width: 10%;">@{{data.count}}</td>
                                                    <td style=" width: 10%;">@{{data.htmlCount}}</td>
                                                    <td style=" width: 10%;">@{{data.confidenceScore}}</td>
                                                    <td style=" width: 10%;">@{{data.relevanceScore}}</td>
                                                    <td style=" width: 50%;">
                                                        <div>
                                                            <span><b>Types:</b></span>
                                                            @{{data.type}}
                                                        </div>
                                                        <div>
                                                            <span><b>Freebase Types: </b></span>
                                                            @{{data.freebaseTypes}}
                                                        </div>

                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                            Collective Results
                            <button style="margin-left: 3px;" type="button" onclick="$('#export_form').submit();"
                                    class="btn btn-sm btn-info pull-right">Export
                            </button>
                            <form action="{{route('export_collective_results')}}" method="POST" id="export_form">
                                @csrf()
                                <input type="hidden" name="data" v-model="JSON.stringify(collective_results)"/>
                            </form>
                        </h1>
                        <div class="card">
                            <div class="card-body">

                                <table class="table table-bordered" style=" width: 100%;">
                                    <thead style=" width: 100%;" class="thead-light">
                                    <tr>
                                        <th>Entity</th>
                                        <th>Analyzer Frequency</th>
                                        <th>Custom Frequency</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(count, entity) in collective_results">
                                        <td>@{{entity}}</td>
                                        <td>@{{count.count}}</td>
                                        <td>@{{count.htmlCount}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
            <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
            <script>
                var analyzerResults = new Vue({
                    el: '#analyzer_results',
                    data: {
                        results: {},
                        urls: {!! json_encode($urls) !!},
                        totalRequests: 0,
                        completedRequests:0,
                        id: "{{$id}}",
                        count: 0,
                        collective_results: {},
                        analytics_data: {},
                    },
                    methods: {
                        getAnalyzerResults() {
                            HoldOn.open();
                            var that = this;

                            axios({
                                method: 'POST',
                                url: '{{url('get-analyzer-results')}}'
                                , data: {
                                    'urls': that.urls,
                                    'id': that.id
                                }
                            })
                                .then(response => {
                                    HoldOn.close();
                                    that.results = response.data.data;
                                    that.collective_results = response.data.collectiveData;
                                    that.analytics_data = response.data.analyticsData;
                                    that.totalRequests = response.data.totalRequests;
                                    that.completedRequests = response.data.completedRequests;
                                    if(that.completedRequests < that.totalRequests){
                                        setTimeout(that. getAnalyzerResults, 7000);
                                    }
                                }).catch(e => {
                                HoldOn.close();
                                swal({
                                    title: "Error",
                                    text: e.response.data.message,
                                    icon: "error",
                                });
                            });
                        },
                        reanalyzeUrl(url, index) {
                            var that = this;
                            swal({
                                title: "Are you sure?",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            }).then((confirm) => {
                                if (confirm) {
                                    HoldOn.open();
                                    axios({
                                        method: 'POST',
                                        url: '{{url('reanalyze-url')}}'
                                        , data: {
                                            'url': url
                                        }
                                    }).then(response => {
                                        HoldOn.close();
                                        that.results[index] = response.data.data;
                                        console.log(that.results);
                                    }).catch(e => {
                                        HoldOn.close();
                                        swal({
                                            title: "Error",
                                            text: e.response.data.message,
                                            icon: "error",
                                        });
                                    });
                                }
                            });
                        },
                        incrementCount() {
                            this.count += 1;
                            console.log(this.count);
                        },
                        exportColectiveResults() {
                            var that = this;
                            HoldOn.open();
                            axios({
                                method: 'POST',
                                url: '{{url('export-collective-results')}}'
                                , data: {
                                    'data': that.collective_results
                                }
                            }).then(response => {
                                HoldOn.close();

                            }).catch(e => {
                                HoldOn.close();
                                swal({
                                    title: "Error",
                                    text: e.response.data.message,
                                    icon: "error",
                                });
                            });
                        }
                    },
                    mounted() {
                        this.getAnalyzerResults();
                    },
                    computed: {
                        incrementCount: function () {
                            // this.count = this.count+ 1;
                            // console.log(this.count);
                            return this.count;
                        }
                    }
                });
            </script>
    @endpush
</x-app-layout>


