<div>
    <table class="table" width="100%">
        <thead>
            <tr>
                <th>Keyword</th>
                <th>Country</th>
                <th>Device</th>
                <th>Search Engine</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        @if($userSearches->count() == 0)
            <tr>
                <td colspan="5" style="text-align: center">No results found</td>
            </tr>
        @endif
        @foreach($userSearches as $search)
            <tr>
                <td>{{$search->keyword}}</td>
                <td>{{$search->country}}</td>
                <td>{{$search->device}}</td>
                <td>{{$search->search_engine}}</td>
                <td>
                    @if($search->status == 'PENDING')
                        <span class="badge badge-warning">Pending</span>
                    @elseif($search->status == 'PARTIAL_COMPLETED')
                        <span class="badge badge-success">Partial Completed</span>
                    @else
                         <span class="badge badge-success">Completed</span>
                    @endif
                </td>
                <td>
                    @if($search->status == 'COMPLETED' || $search->status == 'PARTIAL_COMPLETED' )
                        <a href="{{route('search_results', $search->id)}}"><i class="fa fa-eye"></i></a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
