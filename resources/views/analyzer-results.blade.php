<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analyzers Results') }}
        </h2>
        <a class="btn btn-info btn-sm" href="{{route('search_results', $id)}}">Back</a>
    </x-slot>
    @include('message')
    @include('errors')
    <div class="py-3" id="analyzer_results">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight mb-2">Results</h1>
                    <div class="accordion" id="accordionResults">
                        <div v-for="(analyzers, url) in results" class="card">
                            <div class="card-header" :id="'heading-'+url">
                                <h5 class="mb-0">
{{--                                    <button class="btn btn-link" type="button" data-toggle="collapse" :data-target="'#collapse'+url" aria-expanded="true":aria-controls="'collapse'+url">--}}
                                      <a :href="url" target="_blank">@{{ url }}</a>
                                    <button type="button" class="btn btn-sm btn-info pull-right" @click="reanalyzeUrl(url)">Re-Analyze</button>
                                    {{--                                    </button>--}}
                                </h5>
                            </div>
                            <div :id="'collapse'+url" class="collapse show" :aria-labelledby="'heading-'+url" data-parent="#accordionResults">
                                <div class="card-body">
                                    <div v-for="(entities, analyzer) in analyzers" class="card" >
                                        <div class="card-body">
                                            <h3 class="card-title"><b>@{{ analyzer }}</b></h3>
                                            <table  class="table table-bordered"  style=" width: 100%;">
                                                <thead style=" width: 100%;" class="thead-light">
                                                <tr>
                                                    <th>Entity</th>
                                                    <th>Confidence Score</th>
                                                    <th>Relevance Score</th>
                                                    <th>Entity Type</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr  v-for="(data, entity) in _.orderBy(entities, ['count'], ['desc'])">
                                                        <td>@{{data.entity}}</td>
                                                        <td>@{{data.confidenceScore}}</td>
                                                        <td>@{{data.relevanceScore}}</td>
                                                        <td>
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
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script>
            var analyzerResults = new Vue({
                el: '#analyzer_results',
                data: {
                    results:{},
                    urls : {!! json_encode($urls) !!}
                },
                methods : {
                    getAnalyzerResults(){
                        var that = this;
                        axios({
                            method: 'POST',
                            url: '{{url('get-analyzer-results')}}'
                            , data: {
                                'urls' : that.urls
                            }
                        })
                            .then(response => {
                                HoldOn.close();
                                that.results = response.data.data;
                            }).catch(e => {
                            HoldOn.close();
                            swal({
                                title: "Error",
                                text: e.response.data.message,
                                icon: "error",
                            });
                        });
                    },
                    reanalyzeUrl(url){
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
                                        'url' : url
                                    }
                                }).then(response => {
                                    HoldOn.close();
                                    that.results[url] = response.data.data;
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
                    }
                },
                mounted(){
                    this.getAnalyzerResults();
                }
            });
        </script>
    @endpush
</x-app-layout>
