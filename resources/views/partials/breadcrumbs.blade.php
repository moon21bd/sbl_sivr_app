@if(isset($breadcrumbs))
    <nav class="text-white mb-4" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="flex items-center text-sm">
                    @if ($loop->last)
                        <span class="px-2">{{ $breadcrumb['label'] }}</span>
                    @else
                        <a href="{{ $breadcrumb['url'] }}"
                           class="text-white hover:underline">{{ $breadcrumb['label'] }}</a>
                        <span class="px-2"> / </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
