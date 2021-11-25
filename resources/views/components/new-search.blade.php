@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
<div>
    <form method="POST" action="{{ route('task_post') }}">
    @csrf
        <div class="col-md-6">
            <x-label for="Search" :value="__('Search Engine')" />
            <select name="search_engine" id="search_engine" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="google">Google</option>
                <option value="bing">Bing</option>
            </select>
        </div>
    <!-- Name -->
        <div class="col-md-6">
            <x-label for="keyword" :value="__('Keyword')" />

            <x-input id="keyword" class="block mt-1 w-full" type="text" name="keyword" required autofocus />
        </div>
        <div class="col-md-6">
            <x-label for="country" :value="__('Select Country')" />
            <select name="country" id="country" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach($countries as $key => $value)
                    <option value="{{$key}}" @if($key == 'US') selected @endif >{{$value}}</option>
                @endforeach
            </select>
        </div>


        <div class="col-md-6">
            <x-label for="device" :value="__('Select Device')" />
            <select name="device" id="device" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach($devices as $key => $value)
                    <option value="{{$key}}" >{{$value}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <x-label for="iterations_count" :value="__('Number of Iterations')" />

            <x-input id="iterations_count" class="block mt-1 w-full" type="number" name="iterations_count" value="1" required min="1" max="100"/>
        </div>

        <div class="col-md-6">
            <x-label for="compare_with" :value="__('Compare With')" />

            <x-input id="compare_with" class="block mt-1 w-full" type="text" placeholder="Enter a url to be compared with search results" name="compare_with"  required />
        </div>


        <div class="flex items-center justify-end mt-4">
            <x-button class="ml-4">
                {{ __('Search') }}
            </x-button>
        </div>
    </form>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#country').select2();

        });
    </script>

@endpush
