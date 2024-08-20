<head>
    <meta charset="UTF-8">
    <title>Utilizatori</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>
<x-app-layout>
    <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight pl-2">Gestionarea utilizatorilor</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
       
          <!-- BEGIN SEARCH -->  
          <form action="{{ route('utilizatori.search') }}" method="GET" class="max-w-md mx-auto">
    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        </div>
        <input type="search" name="search" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cautati dupa nume" required />
        <div id="suggestions-list" class="absolute top-full left-0 z-10 bg-white border border-gray-300 rounded-lg shadow-md mt-1 overflow-y-auto max-h-40"></div>
        <button type="submit" class="absolute end-2.5 bottom-2.5  text-sm px-4 py-2 text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cautare</button>
    </div>
</form>


            <!-- END SEARCH -->

            <div class="p-6 text-gray-900">

            <button type="button" id="inapoi" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Inapoi</button>
@if (count($results)>0)
    <div id="userList">
        <table id="userTable" class="w-full text-sm text-left rtl:text-right text-gray-400 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-1000 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>                   
                    <th scope="col" class="px-6 py-3 rounded-s-lg">Nume complet</th>
                    <th scope="col" class="px-6 py-3">UID Card</th>
                    <th scope="col" class="px-6 py-3">Departament</th>
                    <th scope="col" class="px-6 py-3">Timp de prezenta stabilit</th>
                    <th scope="col" class="px-6 py-3">Data de inregistrare</th>
                    <th scope="col" class="px-6 py-3 rounded-s-lg"><span class="sr-only">Edit</span></th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($results as $result)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$result->id}}</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <div class="ps-3">
                        <div class="text-base font-semibold">{{$result->nume}}</div>
                        <div class="font-normal text-gray-500">{{$result->email}}</div>
                    </div>      
                    </td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$result->rfid_uid}}</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$result->departament}}</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$result->timp_de_lucru}} ore/zi</td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{$result->created}}</td>


                                <td class="px-6 py-4 text-right">
                                <button data-modal-target="edit-modal-{{ $result->id }}" data-modal-toggle="edit-modal-{{ $result->id }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline focus:outline-none" style="padding-right:15px;">Editare</button>

                                    <button data-modal-target="popup-modal{{$result->id}}" data-modal-toggle="popup-modal{{$result->id}}" class="font-medium text-red-600 dark:text-blue-500 hover:underline focus:outline-none">Stergere</button>
                                    <div id="popup-modal{{$result->id}}" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal{{$result->id}}">
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    <span class="sr-only">Close modal</span>
                                                </button>
                                                <div class="p-4 md:p-5 text-center">
                                                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                    </svg>
                                                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Stergeti utilizatorul din baza de date ?</h3>
                                                    <form action="{{ route('utilizatori.destroy', $result->id)}}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                            Stergere
                                                        </button>
                                                    </form>
                                                    <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" data-modal-hide="popup-modal{{$result->id}}">Inapoi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Button to trigger modal -->
 
<!-- Main modal for editing user details -->
<div id="edit-modal-{{ $result->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Actualizare informatii
                </h3>
                <button type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" data-modal-toggle="edit-modal-{{ $result->id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ route('utilizatori.update', $result->id) }}" method="POST" id="edit-form-{{ $result->id }}" class="p-4 md:p-5">
                @csrf
                @method('PUT')
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="nume" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nume<span style="color: red;">*</span></label>
                        <input type="text" name="nume" id="nume" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter name" value="{{ $result->nume }}" required="">
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail</label>
                        <input type="text" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter e-mail" value="{{ $result->email }}" required="">
                    </div>
                    <div>
                        <label for="rfid_uid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">UID Card<span style="color: red;">*</span></label>
                        <input type="text" name="rfid_uid" id="rfid_uid" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter UID Card" value="{{ $result->rfid_uid }}" required="">
                    </div>
                    <div>
                        <label for="departament" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Departament</label>
                        <input type="text" name="departament" id="departament" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter department" value="{{ $result->departament }}">
                    </div>
                    <div>
                        <label for="timp_de_lucru" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Timp de prezenta stabilit (ore/zi)</label>
                        <input type="text" name="timp_de_lucru" id="timp_de_lucru" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Enter working hours expected" value="{{ $result->timp_de_lucru }}">
                    </div>
                </div>
                <button type="submit" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Actualizati
                </button>
            </form>
        </div>
    </div>
</div>
                            @endforeach
                            @else
                            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
  <span class="font-medium">Utilizatorul nu a fost gasit.</span>
</div>
@endif
                        </tbody>
                    </table>
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
</script>

<script>
    // Get references to the button and modal
    const addUserBtn = document.getElementById('add-user-btn');
    const addUserModal = document.getElementById('add-user-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // Add event listener to show modal when button is clicked
    addUserBtn.addEventListener('click', function() {
        addUserModal.classList.remove('hidden');
    });

    // Add event listener to close modal when close button is clicked
    closeModalBtn.addEventListener('click', function() {
        addUserModal.classList.add('hidden');
    });

   
</script>

<script>
    // Function to open the edit modal
    function openEditModal(id) {
        const editModal = document.getElementById('edit-modal-' + id);
        editModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // Function to close the edit modal
    function closeEditModal(id) {
        const editModal = document.getElementById('edit-modal-' + id);
        editModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Add event listeners to open and close the modal
    document.addEventListener('DOMContentLoaded', function () {
        // Get all buttons that toggle the edit modal
        const editModalButtons = document.querySelectorAll('[data-modal-toggle^="edit-modal-"]');
        
        // Attach event listeners to each button
        editModalButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const id = button.getAttribute('data-modal-toggle').replace('edit-modal-', '');
                openEditModal(id);
            });
        });

        // Get all buttons inside the edit modals that close the modal
        const editModalCloseButtons = document.querySelectorAll('[data-modal-toggle^="edit-modal-"] button');

        // Attach event listeners to each close button
        editModalCloseButtons.forEach(function (closeButton) {
            closeButton.addEventListener('click', function () {
                const id = closeButton.getAttribute('data-modal-toggle').replace('edit-modal-', '');
                closeEditModal(id);
            });
        });
    });
</script>
<script>
document.getElementById('default-search').addEventListener('input', function() {
    var searchQuery = this.value;

    // Send an AJAX request to fetch autosuggestions
    fetch('/utilizatori/autosuggest?query=' + encodeURIComponent(searchQuery))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Log the response data for debugging

            // Display suggestions
            var suggestionsList = document.getElementById('suggestions-list');
            suggestionsList.innerHTML = '';

            data.forEach(function(suggestion) {
                var suggestionItem = document.createElement('div');
                suggestionItem.textContent = suggestion;
                suggestionItem.classList.add('suggestion-item');
                suggestionsList.appendChild(suggestionItem);

                // Add click event listener to each suggestion item
                suggestionItem.addEventListener('click', function() {
                    // Redirect user to utilizatori.index with search query
                    window.location.href = '/utilizatori/search?search=' + encodeURIComponent(suggestion);
                });
            });
        })
        .catch(error => {
            console.error('Error fetching autosuggestions:', error);
        });
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmationMessage = document.getElementById('confirmation-message');

        // Show the confirmation message
        confirmationMessage.style.display = 'block';

        // Hide the confirmation message after 5 seconds (adjust as needed)
        setTimeout(function() {
            confirmationMessage.style.display = 'none';
        }, 3000); // 5000 milliseconds = 5 seconds
    });
</script>
<script>
document.getElementById('inapoi').addEventListener('click', function() {
    window.location.href = '/utilizatori';
});
</script> 
