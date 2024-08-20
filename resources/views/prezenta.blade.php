<head>
    <meta charset="UTF-8">
    <title>Prezente</title>
    <script src="{{url('flowbite/flowbite.min.js')}}"></script>
    <link href="{{url('flowbite/flowbite.min.css')}}" rel="stylesheet" />
    <script src="{{url('jquery.min.js')}}"></script>
    <link href="{{url('flatpickr/flatpickr.min.css')}}" rel="stylesheet" />
    <script src="{{url('flatpickr/index.js')}}"></script>
    <script src="{{url('flatpickr/flatpickr.min.js')}}"></script>
</head>
<x-app-layout>
    <x-slot name="header">



        <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">Prezente zilnice<a href="/prezentalunara" class="font-semibold text-xl text-gray-800 leading-tight pl-2 ">|     Prezente lunare</a></h2>
        

    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="p-6 text-gray-900">
        <input type="text" id="searchInput" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" placeholder="Introduceti un nume">

        <input type="text" id="datepicker" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="<?=Date('Y-0n-j')?>">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">


<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-1000 ">
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
            <tr class="bg-white border-b   hover:bg-gray-50 ">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $item['id'] }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $item['nume'] }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    {{ $item['departament'] }}
                </th>
                

                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap  @if($item['stare'] === 'Da') text-green-600 @elseif($item['stare'] === 'Nu') text-red-600 @endif">
                    {{ $item['stare'] }}
                </th>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                {{ isset($item['data_ora']) ? $item['data_ora'] : '' }}
                </th>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#datepicker", {
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            // Cerere AJAX pentru preluarea prezentelor din data selectata
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
        // Randarea datelor primite din cererea AJAX
        const tableBody = document.getElementById('tabel_condica'); 

        // Golim tabelul
        tableBody.innerHTML = '';

        // Caz in care nu exista un utilizator inrolat vom afisa un mesaj
        if (prezenta.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4">Nu există date disponibile.</td></tr>';
        } else {
            // Iteram prin date si cream randuri noi
            prezenta.forEach(item => {
                if (item.stare=='Da'){ //daca utilizatorul este prezent in ziua respectiva, starea va fi afisata cu verde
                const row = "<tr class='bg-white border-b   hover:bg-gray-50 '>" +
                "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.id + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.nume + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.departament + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap  text-green-600'>" + item.stare + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.data_ora + "</td>" +
                    "</tr>";
                    tableBody.innerHTML += row;
                }
                else //daca utilizatorul nu este prezent in ziua respectiva, starea va fi afisata cu rosu
                {
                    const row = "<tr class='bg-white border-b   hover:bg-gray-50 '>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.id + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.nume + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap '>" + item.departament + "</td>" +
                    "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap  text-red-600'>" + item.stare + "</td>" +
                    "</tr>";
                    tableBody.innerHTML += row;
                }
                
            });
        }
    }
});
</script>
<script> //script pentru live search
    $(document).ready(function(){
        $('#searchInput').on('keyup', function(){
            var query = $(this).val().toLowerCase(); // Convertim query-ul in lowercase 
            $('#tabel_condica tr').each(function(){
                var rowText = $(this).text().toLowerCase(); // Convertim continutul text din rand in lowercase
                // Verificarea query-ului
                if(rowText.indexOf(query) === -1) {
                    $(this).hide(); // Daca randul nu corespunde cu textul cautat
                } else {
                    $(this).show(); // caz contrar
                }
            });
        });
    });
</script>



</x-app-layout>
