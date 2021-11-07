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
                                    {{--                                    </button>--}}
                                </h5>
                            </div>
                            <div :id="'collapse'+url" class="collapse show" :aria-labelledby="'heading-'+url" data-parent="#accordionResults">
                                <div class="card-body">
                                    <div v-for="(entities, analyzer) in analyzers" class="card" >
                                        <div class="card-body">
                                            <h3 class="card-title"><b>@{{ analyzer }}</b></h3>
                                            <span v-for="(data, entity) in entities">
                                                @{{entity}}(@{{data.length}}) ,
                                            </span>
                                        </div>
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
                    }
                },
                mounted(){
                    this.getAnalyzerResults();
                }
            });
        </script>
    @endpush
</x-app-layout>
