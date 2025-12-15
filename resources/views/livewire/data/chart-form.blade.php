<form wire:submit.prevent="submit">


     
{{-- 
    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="start_date" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.start_date') }}
            </label>
        </div>
        <div>
            <x-wireui-datetime-picker
                id="start_date"
                
                wire:model.live="start_date"
                placeholder="{{ __('data.attributes.start_date') }}"
                parse-format="YYYY-MM-DD"
                class="w-full theme-input"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="end_date" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.end_date') }}
            </label>
        </div>
        <div>
            <x-wireui-datetime-picker
                id="end_date"
                wire:model.live="end_date"
                placeholder="{{ __('data.attributes.end_date') }}"
                parse-format="YYYY-MM-DD"
                class="w-full theme-input"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="devices" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.devices') }}
            </label>
        </div>
        <div>
            <x-wireui-select
                id="devices"
                label="{{ __('data.attributes.devices') }}"
                placeholder="{{ __('data.attributes.devices') }}"
                multiselect
                :async-data="route('measurement-devices.get_devices', $devices)"
                wire:model="device_ids"
                option-label="name"
                option-value="id"
                class="w-full theme-input mt-2"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">


   

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="devices" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.parameters') }}
            </label>
        </div>
        <div>
            <x-wireui-select
                    id="parameters"
                    label="{{ __('data.attributes.parameters') }}"
                    placeholder="{{ __('data.attributes.parameters') }}"
                    multiselect
                    :options="$this->parameters"
                    option-label="name"
                    option-value="id"
                    class="w-full theme-input"
                    wire:model="parameter_ids"
                >
                </x-wireui-select>
        </div>
    </div> --}}


    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">

                <div class="container">
                        <div class="col-md-6">
                            <canvas id="lineChart" height="100"></canvas>
                        </div>
                    </div>

                </div>


   
        </div>
       

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

      


    <script>
     let lineChart;

        let labels = {!! json_encode($labels) !!};
        let data1 = {!! json_encode($data1) !!};
        let data2 = {!! json_encode($data2) !!};
        let data3 = {!! json_encode($data3) !!};
        let data4 = {!! json_encode($data4) !!};
        let data5 = {!! json_encode($data5) !!};
        let data6 = {!! json_encode($data6) !!};

        let config = (type) => ({
        type: type,
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'PM1',
                    data: data1,
                    backgroundColor: [
                        '#cececeff',
                    ],
                borderWidth: 1
                },
                {
                    label: 'PM2_5',
                    data: data2,
                    backgroundColor: [
                        '#8b8b8bff',
                    ],
                borderWidth: 1
                },
                {
                    label: 'PM10',
                    data: data3,
                    backgroundColor: [
                        '#272727ff',
                    ],
                borderWidth: 1
                },
                {
                    label: 'Wilgotność',
                    data: data4,
                    backgroundColor: [
                        '#63a1ffff',
                    ],
                borderWidth: 1
                },
                {
                    label: 'Ciśnienie',
                    data: data5,
                    backgroundColor: [
                        '#ffef63ff',
                    ],
                borderWidth: 1
                },
                {
                    label: 'Temperatura',
                    data: data6,
                    backgroundColor: [
                        '#FF6384',
                    ],
                borderWidth: 1
                },

            
            
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
             scales: {
            y: {
                min: -10
            }
        }
        }
        });

        linechart = new Chart(document.getElementById('lineChart'), config('line'));

        document.addEventListener('livewire:initialized', () => {
           
            
            Livewire.on('chart_update', (event) => {

              lineChart.update();

            })


        });
  

    </script>

</form>


