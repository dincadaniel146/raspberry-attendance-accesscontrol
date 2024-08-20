<head>
    <meta charset="UTF-8">
    <title>Activitate</title>
    <script src="{{url('flowbite/flowbite.min.js')}}"></script>
    <link href="{{url('flowbite/flowbite.min.css')}}" rel="stylesheet" />
    <link href="{{url('flatpickr/flatpickr.min.css')}}" rel="stylesheet" />
    <script src="{{url('flatpickr/index.js')}}"></script>
    <script src="{{url('flatpickr/flatpickr.min.js')}}"></script>
</head>
<x-app-layout>
    <x-slot name="header">


        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2">Activitate</h2>
        
    </x-slot>


    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6">    

            <div class="p-6 text-gray-900">
                <input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block  shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('Y-0n-j')?>">

                @if (count($condica)>0)
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-1000 ">
                            <tr>
                                <th class="px-6 py-3">
                                    Nume
                                </th>
                                <th class="px-6 py-3">
                                    Intrare/Ieșire
                                </th>
                                <th class="px-6 py-3">
                                    Data/Ora
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tabel_condica">
                            @foreach($condica as $item)
                            <tr class="bg-white border-b   hover:bg-gray-50 ">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*' ) text-red-600 @endif">
                                    {{$item->nume}}
                                </th>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap @if($item->stare === 'intrare') text-green-600 @elseif($item->stare === 'iesire') text-red-600 @endif">
                                    @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*' )    
                                    {{''}}
                                    @else
                                    {{$item->stare}}
                                    @endif
                                </th>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{$item->data_ora}}
                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50  " role="alert">
                    <span class="font-medium">Nu există date disponibile !</span>
                </div>    
                @endif

                <div id="pagination_container" class="pt-2 pr-2 pb-2 pl-2">
                    <!-- Paginare -->
                    {{ $condica->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initializare datepicker
    flatpickr("#datepicker", {
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            // Redirect catre un view cu data selectata ca query
            window.location.href = `/condica/${dateStr}`;
        }
    });

  
    
});


    </script>
</x-app-layout>