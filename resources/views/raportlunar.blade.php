<head>
    <meta charset="UTF-8">
    <title>Raport timp prezenta lunar</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">Timp de prezenta lunar <a href="/raport" class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">|     Raport timp de prezenta zilnic</a></h2>
        </h2>
    </x-slot>

    <div class="py-12">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<div class="p-6 text-gray-900">
<input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('0n-Y')?>">

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
            Timp Total Prezent
        </th>
        
    </tr>
</thead>
<tbody id="tabel_raport">
@foreach($presenceMinutes as $userId => $userData)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
           
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $userId }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $userData['name'] }}
                </th>    
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $userData['departament'] }}
                </th>             
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                {{ $userData['totalMinutes'] }}
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
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "m-Y",
                altFormat: "F Y",
                theme: "light"
            })
        ],
        
        onChange: function(selectedDates, dateStr, instance) {
            // Make an AJAX request to fetch attendance data for the selected date
            fetch(`/raportlunar/${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    // Render attendance data in the table
                    renderRaportData(data.presenceMinutes); // Access the attendance data array from the response
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    });
});

function renderRaportData(presenceMinutes) {
    // Get the table body element
    const tableBody = document.getElementById('tabel_raport');

    // Clear existing table rows
    tableBody.innerHTML = '';

    // Iterate over each item in the attendance data array
    presenceMinutes.forEach(item => {
        // Construct HTML table row for each item
        const row = "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.id + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.name + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.departament + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.totalMinutes + "</td>" +
            "</tr>";
        // Append row to table body
        tableBody.innerHTML += row;
    });
}
</script>
