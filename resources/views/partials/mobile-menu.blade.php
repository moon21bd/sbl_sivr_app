<!-- Mobile Menu Start -->
<div class="w-0 h-full fixed top-0 left-0 overflow-hidden ease-in-out duration-100 z-50"
     style="background: linear-gradient(21.64deg, #D9A629 19.97%, #0F5DA8 80.91%);" id="mobileMenu">

    <div class="container px-4 mx-auto">
        <div class="pt-4 inline-block">
            <a href="javascript:void(0)" onclick="closeNav()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-10 h-10 stroke-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </a>
        </div>
        <div class="flex flex-col justify-center items-center h-screen">
            <div class="w-full lg:w-[40%] bg-white rounded-md justify-center items-center text-center px-4">
                <div class="flex flex-col py-4">
                    {{--<a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1" href="#">Profile</a>
                    <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1" href="#">Settings</a>--}}
                    <a class="text-[color:var(--brand-color-blue)] text-lg font-bold py-1 my-1" href="#">{{ __('messages.support') }}</a>
                    <a class="text-red-600 text-lg font-bold py-1 my-1" href="javascript:void(0)"
                       id="btnLogout">{{ __('messages.log-out') }}</a>
                </div>
            </div>

            <div class="div mt-8">
                <a href="javascript:void(0)" class="text-white text-lg" onclick="closeNav()">{{ __('messages.cancel') }}</a>
            </div>
        </div>
    </div>

</div>
<!-- Mobile Menu End -->
