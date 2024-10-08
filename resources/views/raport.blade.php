<head>
    <meta charset="UTF-8">
    <title>Raport timp prezenta</title>
    <script src="{{url('flowbite/flowbite.min.js')}}"></script>
    <link href="{{url('flowbite/flowbite.min.css')}}" rel="stylesheet" />
    <link href="{{url('flatpickr/flatpickr.min.css')}}" rel="stylesheet" />
    <script src="{{url('flatpickr/index.js')}}"></script>
    <script src="{{url('flatpickr/flatpickr.min.js')}}"></script>
    <script src="{{url('flowbite/datepicker.min.js')}}"></script>
    <script src="{{url('jquery.min.js')}}"></script>

</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">Raport timp de prezenta zilnic <a href="/raportlunar" class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">|     Timp de prezenta lunar</a></h2>
        </h2>
    </x-slot>

    <div class="py-12">

<div class="max-w-8xl mx-auto sm:px-6 lg:px-8">


<!-- Modal export-->
<div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow ">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                <h3 class="text-lg font-semibold text-gray-900 ">
                    Export Excel
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form method="POST" action="/export">
                @csrf
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2 sm:col-span-1" style="margin-left:25px;">
                        <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900 " style="margin-top:10px;">Alegeti data de inceput</label>
                        <input id="start_date" name="start_date" type="date" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm">
                    </div>
                    <div class="col-span-2 sm:col-span-1" style="margin-right:-10px;">
                        <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900 " style="margin-top:10px;">Alegeti data de sfarsit</label>
                        <input id="end_date" name="end_date" type="date" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm">
                    </div>
                </div>
                <button type="submit" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" style="margin-left:10px;margin-bottom:10px;">
                    Export
                </button>
            </form>
        </div>
    </div>
</div> 

<div class="p-6 text-gray-900">
<button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100" type="button">
Export Excel
</button>
<input type="text" id="searchInput" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" placeholder="Introduceti un nume">

<input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('Y-0n-j')?>">


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">

<table class="w-full text-sm text-left rtl:text-right text-gray-500" >
        <colgroup>
           
        </colgroup>
<thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
    <tr>
    
        <th scope="col" class="px-6 py-3 rounded-s-lg">
            ID
        </th>
        <th scope="col" class="px-6 py-3 rounded-s-lg">
          Nume
        </th>
        <th scope="col" class="px-6 py-3">
            Departament
        </th>
        <th scope="col" class="px-6 py-3">
            Check-in
        </th>
        <th scope="col" class="px-6 py-3">
            Check-out
        </th>
        <th scope="col" class="px-6 py-3">
            Timp prezent/a
        </th>
        <th scope="col" class="px-6 py-3">
            Peste/Sub Timpul Stabilit
        </th>
        <th scope="col" class="px-6 py-3">
          Pauze & Durata pauzelor
        </th>

    </tr>
</thead>
<tbody id="tabel_raport">
@foreach($raportData as $item)
            <tr class="bg-white border-  hover:bg-gray-50">
           
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ $item->ID }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ $item->NUME }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ $item->DEPARTAMENT }}
                </th>                
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ $item->CHECK_IN }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ $item->CHECK_OUT }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ $item->TIMP_LUCRAT }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap  @if (str_starts_with($item->PESTE_SUB_NORMA ,'SUB :')) text-red-600 @elseif (str_starts_with($item->PESTE_SUB_NORMA ,'PESTE :')) text-green-600 @endif ">
                {{ $item->PESTE_SUB_NORMA }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    @if($item->PAUZA_COUNT == 1)
                {{ $item->PAUZA_COUNT }} pauza
||
                {{$item->PAUZA_DURATA}} minute
                @else
                {{ $item->PAUZA_COUNT }} pauze
||
                {{$item->PAUZA_DURATA}} minute
@endif
                </th>
            </tr>
            @endforeach
        </tbody>
    </table>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#datepicker", {
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            //Cerere AJAX pentru preluarea datelor raportului
            fetch(`/raport/${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    renderRaportData(data.raportData);
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    });
    // randarea datelor primite
    function renderRaportData(raportData) {
        const tableBody = document.getElementById('tabel_raport');

        // Golim tabelul
        tableBody.innerHTML = '';

        raportData.forEach(item => {
            let pestSubNormaClass = '';
    if (item.PESTE_SUB_NORMA.startsWith('SUB :')) { //afisarea culorilor
        pestSubNormaClass = 'text-red-600';
    } else if (item.PESTE_SUB_NORMA.startsWith('PESTE :')) {
        pestSubNormaClass = 'text-green-600';
    }
            const row = `<tr class="bg-white border-  hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">${item.ID}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">${item.NUME}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">${item.DEPARTAMENT}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">${item.CHECK_IN}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">${item.CHECK_OUT}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">${item.TIMP_LUCRAT}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap  ${pestSubNormaClass}">${item.PESTE_SUB_NORMA}</td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
    ${item.PAUZA_COUNT === 1 ? item.PAUZA_COUNT + ' pauza' : item.PAUZA_COUNT + ' pauze'} || ${item.PAUZA_DURATA} minute  
</td>
            </tr>`;
            tableBody.innerHTML += row;
        });
    }
});
</script>
<script> //script live search
    $(document).ready(function(){
        $('#searchInput').on('keyup', function(){
            var query = $(this).val().toLowerCase(); 
            $('#tabel_raport tr').each(function(){
                var rowText = $(this).text().toLowerCase(); 
                if(rowText.indexOf(query) === -1) {
                    $(this).hide(); 
                } else {
                    $(this).show(); 
                }
            });
        });
    });
</script>