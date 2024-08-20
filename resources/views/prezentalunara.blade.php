<head>
    <meta charset="UTF-8">
    <title>Prezente lunare</title>
    <script src="{{url('flowbite/flowbite.min.js')}}"></script>
    <link href="{{url('flowbite/flowbite.min.css')}}" rel="stylesheet" />
    <script src="{{url('flatpickr/index.js')}}"></script>
    <script src="{{url('flatpickr/flatpickr.min.js')}}"></script>
    <link href="{{url('flatpickr/flatpickr.min.css')}}" rel="stylesheet" />
    <link href="{{url('flatpickr/style.css')}}" rel="stylesheet" />
    <script src="{{url('jquery.min.js')}}"></script>





</head>
<x-app-layout>
    <x-slot name="header">



        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">Prezente lunare<a href="/prezenta" class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">|     Prezente zilnice</a></h2>

    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="p-6 text-gray-900">
        <input type="text" id="searchInput" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" placeholder="Introduceti un nume">

        <input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('0n-Y')?>">


            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
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
<tr class="bg-white border-b hover:bg-gray-50 ">
<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>{{ $item['id'] }}</td>
    <td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>{{ $item['nume'] }}</td>
    <td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>{{ $item['departament'] }}</td>
    <td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>{{ $item['prezente'] }}</td>
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
            //Cerere AJAX pentru preluarea numarului de prezente 
            fetch(`/prezentalunara/${dateStr}`)
                .then(response => response.json())
                .then(data => {
                    
                    renderPrezentaData(data.prezentalunara); 
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    });
});
// randarea datelor primite
function renderPrezentaData(prezenta) {
    const tableBody = document.getElementById('tabel_condica');

    // Golim tabelul
    tableBody.innerHTML = '';

    // Iteram peste fiecare item din array
    prezenta.forEach(item => {
        // Iteram prin date si cream randuri noi
        const row = "<tr class='bg-white border-b hover:bg-gray-50 '>" +
        "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.id + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.nume + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.departament + "</td>" +
            "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.prezente + "</td>" +
            "</tr>";
        tableBody.innerHTML += row;
    });
}
</script>

<script>//script pentru live search
    $(document).ready(function(){
        $('#searchInput').on('keyup', function(){
            var query = $(this).val().toLowerCase(); 
            $('#tabel_condica tr').each(function(){
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
</x-app-layout>
