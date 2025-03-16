@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{-- Tombol First --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->url(1) ?? 'javascript:void(0);' }}">
                    <i class="icon-base ti tabler-chevrons-left icon-sm"></i>
                </a>
            </li>

            {{-- Tombol Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() ?? 'javascript:void(0);' }}">
                    <i class="icon-base ti tabler-chevron-left icon-sm"></i>
                </a>
            </li>

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ?? 'javascript:void(0);' }}">
                    <i class="icon-base ti tabler-chevron-right icon-sm"></i>
                </a>
            </li>

            {{-- Tombol Last --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) ?? 'javascript:void(0);' }}">
                    <i class="icon-base ti tabler-chevrons-right icon-sm"></i>
                </a>
            </li>
        </ul>
    </nav>
@endif
