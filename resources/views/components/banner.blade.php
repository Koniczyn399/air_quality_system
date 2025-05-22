@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{{ json_encode(['show' => true, 'style' => $style, 'message' => $message]) }}"
     :class="{
        'theme-bg-success': style == 'success',
        'theme-bg-danger': style == 'danger',
        'theme-bg-warning': style == 'warning',
        'theme-bg-info': style != 'success' && style != 'danger' && style != 'warning'
     }"
     style="display: none;"
     x-show="show && message"
     x-on:banner-message.window="
        style = event.detail.style;
        message = event.detail.message;
        show = true;
     "
     class="max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8 transition-all duration-300">
    <div class="flex items-center justify-between flex-wrap">
        <div class="w-0 flex-1 flex items-center min-w-0">
            <span class="flex p-2 rounded-lg" :class="{
                'theme-icon-success': style == 'success',
                'theme-icon-danger': style == 'danger',
                'theme-icon-warning': style == 'warning',
                'theme-icon-info': style != 'success' && style != 'danger' && style != 'warning'
            }">
                <!-- Ikony są już zdefiniowane w app.css -->
            </span>

            <p class="ms-3 font-medium text-sm theme-text truncate" x-text="message"></p>
        </div>

        <div class="shrink-0 sm:ms-3">
            <button
                type="button"
                class="-me-1 flex p-2 rounded-md focus:outline-none sm:-me-2 transition"
                :class="{
                    'hover:bg-indigo-600 focus:bg-indigo-600': style == 'success',
                    'hover:bg-red-600 focus:bg-red-600': style == 'danger',
                    'hover:bg-yellow-600 focus:bg-yellow-600': style == 'warning'
                }"
                aria-label="Dismiss"
                x-on:click="show = false">
                <svg class="size-5 theme-text" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>