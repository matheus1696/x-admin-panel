<!-- Modal Component -->
<div x-data="{ isModalOpen: @entangle('showModal') }" x-on:keydown.escape.window="isModalOpen = false" x-id="['modal-title']">

    <!-- Trigger Button -->
    <button type="button" @click="isModalOpen = true" class="focus:outline-none">
        {{ $button ?? '' }}
    </button>

    <!-- Overlay -->
    <div x-show="isModalOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Modal Content -->
        <div x-show="isModalOpen"
             x-transition.scale
             @click.outside="isModalOpen = false"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-auto overflow-hidden"
             @keydown.escape.window="isModalOpen = false"
             @keyup.escape.window="isModalOpen = false">

            <!-- Header -->
            @if(isset($title))
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 id="modal-title" class="text-lg font-semibold text-gray-800">
                        {{ $title }}
                    </h2>
                    <button @click="isModalOpen = false"
                            class="text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Body -->
            <div class="p-6 max-h-[80vh] overflow-y-auto">
                {{ $body }}
            </div>

            <!-- Optional Footer -->
            @if(isset($footer))
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-2">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
