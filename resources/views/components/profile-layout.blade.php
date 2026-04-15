<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-6">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold">
                    {{ $title }}
                </h1>
                @isset($subtitle)
                    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                @endisset
            </div>

            @isset($action)
                {{ $action }}
            @endisset
            
           
        </div>

        <div class="grid grid-cols-12 gap-6">

            {{-- SIDEBAR --}}
            <div class="col-span-3">
                {{ $sidebar }}
            </div>

            {{-- MAIN --}}
            <div class="col-span-9">
                {{ $slot }}
            </div>

        </div>

    </div>
</x-app-layout>