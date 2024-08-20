<head>
    <meta charset="UTF-8">
    <title>Prezente</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<x-app-layout>
    <x-slot name="header">



        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">Prezente zilnice<a href="/prezentalunara" class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">|     Prezente lunare</a></h2>
        

    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="p-6 text-gray-900">
        <input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('Y-0n-j')?>">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-1000 dark:text-gray-400">
            <tr>
            <th scope="col" class="px-6 py-3 rounded-s-lg">
                    ID
                </th>
                <th scope="col" class="px-6 py-3 rounded-s-lg">
                    Nume
                </th>
                <th scope="col" class="px-6 py-3 rounded-s-lg">
                    Departament
                </th>
                <th scope="col" class="px-6 py-3">
                    Prezent/a
                </th>
                
                <th scope="col" class="px-6 py-3">
                    Data/Ora
                </th>
                
            </tr>
        </thead>
        <tbody id="tabel_condica">
            @foreach($prezenta as $item)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $item['id'] }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $item['nume'] }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $item['departament'] }}
                </th>
                

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white @if($item['stare'] === 'Da') text-green-600 @elseif($item['stare'] === 'Nu') text-red-600 @endif">
                    {{ $item['stare'] }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ isset($item['data_ora']) ? $item['data_ora'] : '' }}
                </th>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#datepicker", {
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            // Make an AJAX request to fetch attendance data for the selected date
            fetch(`/prezenta/${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    // Render attendance data in the table
                    renderPrezentaData(data.prezenta);
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    });

    function renderPrezentaData(prezenta) {
        // Get the table body element
        const tableBody = document.getElementById('tabel_condica');

        // Clear existing table rows
        tableBody.innerHTML = '';

        // Render new table rows with attendance data
        if (prezenta.length === 0) {
            // If no attendance data available, display a message
            tableBody.innerHTML = '<tr><td colspan="4">Nu există date disponibile.</td></tr>';
        } else {
            // Loop through the attendance data and create table rows
            prezenta.forEach(item => {
                if (item.stare=='Da'){
                const row = "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'>" +
                "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.id + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.nume + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.departament + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-green-600'>" + item.stare + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.data_ora + "</td>" +
                    "</tr>";
                    tableBody.innerHTML += row;
                }
                else
                {
                    const row = "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.id + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.nume + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.departament + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white text-red-600'>" + item.stare + "</td>" +
                    "</tr>";
                    tableBody.innerHTML += row;
                }
                
            });
        }
    }
});
</script>

</x-app-layout>

