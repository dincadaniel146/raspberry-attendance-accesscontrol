<head>
    <meta charset="UTF-8">
    <title>Panou de bord</title>
    <script src="{{url('flowbite/flowbite.min.js')}}"></script>
    <link href="{{url('flowbite/flowbite.min.css')}}" rel="stylesheet" />
    <script src="{{url('apexcharts/apexcharts.min.js')}}"></script>

</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bun venit!') }}
            </h2>
<h2 id="dataH2" class="font-semibold text-xl text-gray-800 leading-tight"></h2>
        
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CHART & STATISTICI -->
                <div class="w-full ">
                    <div class="p-6 text-gray-900">
                    <div class="shadow-md sm:rounded-lg">
                    <div class="grid max-w-screen-xl grid-cols-2 md:grid-cols-4 gap-8 p-4 mx-auto text-gray-900 sm:grid-cols-4 xl:grid-cols-4 sm:p-8">
    <div class="flex flex-col items-center justify-center">
        <dt id="userCount" class="mb-2 text-3xl font-extrabold"></dt>
        <dd class="text-gray-500">Utilizatori</dd>
    </div>
    <div class="flex flex-col items-center justify-center">
        <dt id="AbsentiCount" class="mb-2 text-3xl font-extrabold"></dt>
        <dd class="text-gray-500">Absenți</dd>
    </div>
    <div class="flex flex-col items-center justify-center">
        <dt id="checkInCount" class="mb-2 text-3xl font-extrabold"></dt>
        <dd class="text-gray-500">Check-in</dd>
    </div>
    <div class="flex flex-col items-center justify-center">
        <dt id="checkOutCount" class="mb-2 text-3xl font-extrabold"></dt>
        <dd class="text-gray-500">Check-out</dd>
    </div>
</div>


                        <div class="py-6" id="pie-chart"></div>
                        <div class="grid grid-cols-1 items-center border-gray-200  dark:border-gray-700 justify-between">
                            <a href="/raport" class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2"style="margin-bottom:10px;">
                                Vizualizați raportul complet
                                <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
</div>
<div class="p-6 text-gray-900">
    <!-- Ultimile Intrari  -->
    <div class="w-full md:w-1/2 mb-6 overflow-x-auto">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <colgroup>
                    <col class="w-1/4">
                    <col class="w-1/4">
                    <col class="w-1/4">
                </colgroup>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th class="px-6 py-3">Nume</th>
                        <th class="px-6 py-3">Intrare/Iesire</th>
                        <th class="px-6 py-3">Data/Ora</th>
                    </tr>
                </thead>
                <tbody id="tabel_intrari">
                    @foreach($intrari as $item)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*') text-red-600 @endif">
                            {{$item->nume}}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap @if($item->stare === 'intrare') text-green-600 @elseif($item->stare === 'iesire') text-red-600 @elseif($item->stare === 'pauza') text-yellow-600 @endif">
                            @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*')    
                            {{''}}
                            @else
                            {{$item->stare}}
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{$item->data_ora}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (count($intrari) == 0)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            <span class="font-medium">Nu există intrări recente!</span>
        </div>
        @endif
    </div>

    <!-- Ultimele Iesiri  -->
    <div class="w-full md:w-1/2 overflow-x-auto">
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <colgroup>
                    <col class="w-1/4">
                    <col class="w-1/4">
                    <col class="w-1/4">
                </colgroup>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Nume</th>
                        <th class="px-6 py-3">Intrare/Iesire</th>
                        <th class="px-6 py-3">Data/Ora</th>
                    </tr>
                </thead>
                <tbody id="tabel_intrari">
                    @foreach($iesiri as $item)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*') text-red-600 @endif">
                            {{$item->nume}}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap @if($item->stare === 'intrare') text-green-600 @elseif($item->stare === 'iesire') text-red-600 @elseif($item->stare === 'pauza') text-yellow-600 @endif">
                            @if ($item->nume == '*Incercare de intrare nereusita*' || $item->nume == '*Incercare de iesire nereusita*')    
                            {{''}}
                            @else
                            {{$item->stare}}
                            @endif
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{$item->data_ora}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (count($iesiri) == 0)
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 " role="alert">
            <span class="font-medium">Nu există ieșiri recente!</span>
        </div>
        @endif
    </div>
</div>

                </div>
            </div>
        </div>
    </div>
</div>



    <script>

const getChartOptions = (checkInCount, checkOutCount, absentiCount, userCount) => {
  // Calculam procentajele bazate pe numarul total de utilizatori 
  const checkInPercentage = (checkInCount / userCount) * 100;
  const absentiPercentage = (absentiCount / userCount) * 100;

  return {
    series: [checkInPercentage,absentiPercentage],
    colors: ["#1C64F2", "#ff0015"],
    chart: {
      height: 420,
      width: "100%",
      type: "pie",
    },
    stroke: {
      colors: ["white"],
      lineCap: "",
    },
    plotOptions: {
      pie: {
        labels: {
          show: true,
        },
        size: "100%",
        dataLabels: {
          offset: -25
        }
      },
    },
    labels: ["Prezenți","Absenți"],
    dataLabels: {
      enabled: true,
      style: {
        fontFamily: "Inter, sans-serif",
      },
    },
    legend: {
      position: "bottom",
      fontFamily: "Inter, sans-serif",
    },
    yaxis: {
      labels: {
        formatter: function (value) {
          return value + "%"
        },
      },
    },
    xaxis: {
      labels: {
        formatter: function (value) {
          return value  + "%"
        },
      },
      axisTicks: {
        show: false,
      },
      axisBorder: {
        show: false,
      },
    },
  };
};

// din controller
Promise.all([
  fetch('/dashboard/checkin-count').then(response => response.json()),
  fetch('/dashboard/checkout-count').then(response => response.json()),
  fetch('/dashboard/absenti-count').then(response => response.json()),
  fetch('/dashboard/user-count').then(response => response.json())
])
.then(([checkInData, checkOutData, absentiData, userData]) => {
  const checkInCount = checkInData.checkInCount;
  const checkOutCount = checkOutData.checkOutCount;
  const absentiCount = absentiData.AbsentiCount;
  const userCount = userData.userCount;

  document.getElementById('userCount').innerText = userCount;
  document.getElementById('checkInCount').innerText = checkInCount;
  document.getElementById('checkOutCount').innerText = checkOutCount;
  document.getElementById('AbsentiCount').innerText = absentiCount;

  if (document.getElementById("pie-chart") && typeof ApexCharts !== 'undefined') {
    const chart = new ApexCharts(document.getElementById("pie-chart"), getChartOptions(checkInCount, checkOutCount, absentiCount, userCount));
    chart.render();
  }
})
.catch(error => {
  console.error('Error fetching data:', error);
});


</script>
<script>
    // nr user din controller
    fetch('/dashboard/data')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // actualizam div-ul cu numarul total de utilizatori
            document.getElementById('dataH2').innerText =data.data;
        })
        .catch(error => {
            console.error('Error fetching user count:', error);
        });
</script>
</x-app-layout>