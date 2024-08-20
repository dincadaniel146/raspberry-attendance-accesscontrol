<head>
    <meta charset="UTF-8">
    <title>Prezente lunare</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    




</head>
<x-app-layout>
    <x-slot name="header">



        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">Prezente lunare<a href="/prezenta" class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">|     Prezente zilnice</a></h2>

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
                    Prezente
                </th>
                
                
            </tr>
        </thead>
        <tbody id="tabel_condica">
        @foreach ($prezenta as $item)
<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>{{ $item['id'] }}</td>
    <td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>{{ $item['nume'] }}</td>
    <td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>{{ $item['departament'] }}</td>
    <td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>{{ $item['prezente'] }}</td>
</tr>
@endforeach

        </tbody>
    </table>
    
</div>

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
            fetch(`/prezentalunara/${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    // Render attendance data in the table
                    renderPrezentaData(data.prezentalunara); // Access the attendance data array from the response
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    });
});

function renderPrezentaData(prezenta) {
    // Get the table body element
    const tableBody = document.getElementById('tabel_condica');

    // Clear existing table rows
    tableBody.innerHTML = '';

    // Iterate over each item in the attendance data array
    prezenta.forEach(item => {
        // Construct HTML table row for each item
        const row = "<tr class='bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'>" +
        "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.id + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.nume + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.departament + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white'>" + item.prezente + "</td>" +
            "</tr>";
        // Append row to table body
        tableBody.innerHTML += row;
    });
}
</script>


</x-app-layout>

