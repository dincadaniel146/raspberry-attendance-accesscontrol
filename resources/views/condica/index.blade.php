<head>
    <meta charset="UTF-8">
    <title>Condica</title>
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
            <input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('Y-0n-j')?>">
            @if (count($condica)>0)
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 " style="table-layout: fixed;">
        <colgroup>
            <col class="w-1/4"> 
            <col class="w-1/4">
            <col class="w-1/4">
        </colgroup>
        <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
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
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap  @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*' ) text-red-600 @endif">
                    {{$item->nume}}
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap  @if($item->stare === 'intrare') text-green-600 @elseif($item->stare === 'iesire') text-red-600 @endif">
                    @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*' )    
                    {{''}}
                    @else
                    {{$item->stare}}
                    @endif
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    {{$item->data_ora}}
                </td>
            </tr>
            @endforeach
           
        </tbody>
    </table>
                    <div id="pagination_container">
        {{ $condica->links() }}
    </div>
    @else
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50  " role="alert">
  <span class="font-medium">Nu există date disponibile !</span>
</div>  
@endif
    
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datepicker = flatpickr("#datepicker", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    window.location.href = `/condica/${dateStr}`;
                }
            });

            //Preluam data de forma YYYY-MM-DD din URL pentru afisarea corecta in datepicker
            const url = window.location.href;
            const datePart = url.split('/').pop().split('?')[0]; //Extragerea datei din URL folosind separatorul "/" 

            // setam placeholder-ul din datepicker cu data selectata anterior
            datepicker.input.placeholder = datePart;
        });
    </script>
</x-app-layout>