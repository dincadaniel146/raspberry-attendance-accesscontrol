<head>
    <meta charset="UTF-8">
    <title>Utilizatori</title>
    <script src="{{url('flowbite/flowbite.min.js')}}"></script>
    <link href="{{url('flowbite/flowbite.min.css')}}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="{{url('jquery.min.js')}}"></script>

</head>
<x-app-layout>
    <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2">Gestionarea utilizatorilor</h2>
    
    </x-slot>

    <div class="py-12">
    
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if(session('success'))
    <div id="confirmation-message" class="flex items-center p-4 mb-4 text-blue-800 border-t-4 border-blue-300 bg-blue-50 " role="alert">
    <div class="ms-3 text-sm font-medium">
    {{ session('success') }}
    </div>
      
    </button>
</div>
@endif


<!-- Modal pentru adaugarea utilizatorilor -->
<div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow ">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                <h3 class="text-lg font-semibold text-gray-900 ">
                    Utilizator nou
                </h3>
                <button type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 " data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="{{ route('utilizatori.newuser') }}" method="POST" class="p-4 md:p-5">
                @csrf
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="nume" class="block mb-2 text-sm font-medium text-gray-900 ">Nume<span style="color: red;">*</span></label>
                        <input type="text" name="nume" id="nume" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5  " placeholder="Introduceți un nume" required="">
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">E-mail</label>
                        <input type="text" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Introduceti o adresa de mail" >
                    </div>
                    <div>
                        <label for="rfid_uid" class="block mb-2 text-sm font-medium text-gray-900 ">UID Card<span style="color: red;">*</span></label>
                        <input type="text" name="rfid_uid" id="rfid_uid" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Codul de identificare a cardului" required="">
                    </div>
                    <div>
                        <label for="departament" class="block mb-2 text-sm font-medium text-gray-900 ">Departament</label>
                        <input type="text" name="departament" id="departament" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Introduceti departamentul">
                    </div>
                    <div>
                        <label for="timp_de_lucru" class="block mb-2 text-sm font-medium text-gray-900 ">Timp prezență stabilit (ore/zi)</label>
                        <input type="text" name="timp_de_lucru" id="timp_de_lucru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Norma de lucru" value="8">
                    </div>
                </div>
                <button type="submit" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">
                Adăugați
                </button>
            </form>
        </div>
    </div>
</div>
            <div class="p-6 text-gray-900">
                 <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 " type="button">
                 Adăugați utilizator nou
             </button>
             <input type="text" id="searchInput" class="mt-1 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 block w-half shadow-sm sm:text-sm" style="margin-bottom:15px;" placeholder="Introduceti un nume">

            @if (count($users)>0)
          <div id="userList">
          <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

        <table id="userTable" class="w-full text-sm text-left rtl:text-right text-gray-400 ">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3 rounded-s-lg">Nume complet</th>
                    <th scope="col" class="px-6 py-3">UID Card</th>
                    <th scope="col" class="px-6 py-3">Departament</th>
                    <th scope="col" class="px-6 py-3">Timp prezență stabilit</th>
                    <th scope="col" class="px-6 py-3">Dată de înregistrare</th>
                    
                    <th scope="col" class="px-6 py-3 rounded-s-lg"><span class="sr-only">Edit</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="bg-white border-b  hover:bg-gray-50 ">
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">{{$user->id}}</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                    <div class="ps-3">
                        <div class="text-base font-semibold">{{$user->nume}}</div>
                        <div class="font-normal text-gray-500">{{$user->email}}</div>
                    </div>      
                    </td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">{{$user->rfid_uid}}</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">{{$user->departament}}</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">{{$user->timp_de_lucru}} ore/zi</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">{{$user->created}}</td>
                

                                <td class="px-6 py-4 text-right">
                                <button data-modal-target="edit-modal-{{ $user->id }}" data-modal-toggle="edit-modal-{{ $user->id }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline focus:outline-none" style="padding-right:15px;">Editare</button>

                                    <button data-modal-target="popup-modal{{$user->id}}" data-modal-toggle="popup-modal{{$user->id}}" class="font-medium text-red-600 dark:text-blue-500 hover:underline focus:outline-none">Ștergere</button>
                                    <div id="popup-modal{{$user->id}}" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <!-- Modal pentru stergerea utilizatorilor -->
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow ">
                                                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal{{$user->id}}">
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    <span class="sr-only">Close modal</span>
                                                </button>
                                                <div class="p-4 md:p-5 text-center">
                                                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                    </svg>
                                                    <h3 class="mb-5 text-lg font-normal text-gray-500 ">Ștergeți utilizatorul din baza de date ?</h3>
                                                    <form action="{{ route('utilizatori.delete', $user->id)}}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        Ștergere
                                                        </button>
                                                    </form>
                                                    <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 " data-modal-hide="popup-modal{{$user->id}}">Înapoi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
<!-- Modal pentru actualizarea utilizatorilor -->
<div id="edit-modal-{{ $user->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
<div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow ">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t ">
                <h3 class="text-lg font-semibold text-gray-900 ">
                    Actualizare informații
                </h3>
                <button type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 " data-modal-toggle="edit-modal-{{ $user->id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form action="{{ route('utilizatori.update', $user->id) }}" method="POST" id="edit-form-{{ $user->id }}" class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="nume" class="block mb-2 text-sm font-medium text-gray-900 ">Nume<span style="color: red;">*</span></label>
                        <input type="text" name="nume" id="nume" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Enter name" value="{{ $user->nume }}" required="">
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">E-mail</label>
                        <input type="text" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Enter name" value="{{ $user->email }}" >
                    </div>
                    <div>
                        <label for="rfid_uid" class="block mb-2 text-sm font-medium text-gray-900 ">UID Card<span style="color: red;">*</span></label>
                        <input type="text" name="rfid_uid" id="rfid_uid" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Enter UID Card" value="{{ $user->rfid_uid }}" required="">
                    </div>
                    <div>
                        <label for="departament" class="block mb-2 text-sm font-medium text-gray-900 ">Departament</label>
                        <input type="text" name="departament" id="departament" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Enter department" value="{{ $user->departament }}">
                    </div>
                    <div>
                        <label for="timp_de_lucru" class="block mb-2 text-sm font-medium text-gray-900 ">Timp prezență stabilit (ore/zi)</label>
                        <input type="text" name="timp_de_lucru" id="timp_de_lucru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5    " placeholder="Enter working hours expected" value="{{ $user->timp_de_lucru }}">
                    </div>
                </div>
                <button type="submit" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">
                    Actualizați
                </button>
            </form>
        </div>
    </div>
</div>
</div>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $users->links() }}
                    @else
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 " role="alert">
  <span class="font-medium">Nu există utilizatori înrolați!</span>
</div>
@endif
                </div>
            </div>
        </div>
    </div>
</div>




</x-app-layout>
<script>
    function toggleModal(id) {
        const modal = document.getElementById('modal' + id);
        modal.classList.toggle('show');
    }

    function showModal(elementId) {
        document.getElementById(elementId).classList.remove('hidden');
    }

    function hideModal(elementId) {
        document.getElementById(elementId).classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const editModalButtons = document.querySelectorAll('[data-modal-toggle^="edit-modal-"]');
        
        editModalButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const id = button.getAttribute('data-modal-toggle').replace('edit-modal-', '');
                toggleModal(id);
            });
        });

        const editModalCloseButtons = document.querySelectorAll('[data-modal-toggle^="edit-modal-"] button');

        editModalCloseButtons.forEach(function (closeButton) {
            closeButton.addEventListener('click', function () {
                const id = closeButton.getAttribute('data-modal-toggle').replace('edit-modal-', '');
                toggleModal(id);
            });
        });
    });
</script>


<script> //script pentru afisarea mesajelor de confirmare 
    document.addEventListener('DOMContentLoaded', function() {
        const confirmationMessage = document.getElementById('confirmation-message');

        confirmationMessage.style.display = 'block';

        // Ascunderea mesajului dupa 5 secunde
        setTimeout(function() {
            confirmationMessage.style.display = 'none';
        }, 3000); 
    });
</script>
<script> //script live search
    $(document).ready(function(){
        $('#searchInput').on('keyup', function(){
            var query = $(this).val().toLowerCase(); 
            $('#userTable tr').each(function(){
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
