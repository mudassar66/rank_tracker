<!-- Modal -->
<div class="modal fade" id="analyze_results" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Results</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div v-for="(result, url) in results">
                    <div style="width: 100%; padding: 10px 5px; background-color: lightgray;">
                        <h5>@{{url}}</h5>
                    </div>
                    <span v-for="(data, entity) in result">
                        @{{entity}}(@{{data.length}}) ,
                    </span>
                </div>
            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
