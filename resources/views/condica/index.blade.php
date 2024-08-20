<head>
    <meta charset="UTF-8">
    <title>Condica</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            @if (count($attendanceData)>0)
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" style="table-layout: fixed;">
        <colgroup>
            <col class="w-1/4"> <!-- Adjust the width as needed for each column -->
            <col class="w-1/4">
            <col class="w-1/4">
        </colgroup>
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-1000 dark:text-gray-400">
            <tr>
                <th class="px-6 py-3"> <!-- No need to specify width here -->
                    Nume
                </th>
                <th class="px-6 py-3">
                    Intrare/Iesire
                </th>
                <th class="px-6 py-3">
                    Data/Ora
                </th>
            </tr>
        </thead>
        <tbody id="tabel_condica">
        
            @foreach($attendanceData as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*' ) text-red-600 @endif">
                    {{$item->nume}}
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white @if($item->stare === 'intrare') text-green-600 @elseif($item->stare === 'iesire') text-red-600 @endif">
                    @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*' )    
                    {{''}}
                    @else
                    {{$item->stare}}
                    @endif
                </td>
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$item->data_ora}}
                </td>
            </tr>
            @endforeach
           
        </tbody>
    </table>
                    <div id="pagination_container">
        <!-- Pagination links will be rendered here -->
        {{ $attendanceData->links() }}
    </div>
    @else
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
  <span class="font-medium">Nu exista date disponibile !</span>
</div>  
@endif
    
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr datepicker
            const datepicker = flatpickr("#datepicker", {
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    // Redirect to another view with the selected date as a query parameter
                    window.location.href = `/condica/${dateStr}`;
                }
            });

            // Get the date part from the URL
            const url = window.location.href;
            const datePart = url.split('/').pop().split('?')[0]; // Extract the date part from the URL

            // Set the date part as the placeholder for the datepicker
            datepicker.input.placeholder = datePart;
        });
    </script>
</x-app-layout>
