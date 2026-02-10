@php
    if (! isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
           (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
        JS
        : '';
@endphp

<div class="paginate-root">
    @if ($paginator->hasPages())
        <nav class="d-flex justify-items-center justify-content-between">

            <div class=" flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">

                <div>
                    <div class="text-center">
                        &nbsp;
                        {{-- Previous Page Link --}}
                        @if ($paginator->onFirstPage())
                            <span class="page-item disabled" aria-disabled="true" aria-label="قبلی">
                                <span class="btn btn-dark" aria-hidden="true">قبلی</span>
                            </span>
                        @else
                            <span class="page-item">
                                <button type="button" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="btn btn-dark" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" aria-label="قبلی">قبلی</button>
                            </span>
                        @endif
                        &nbsp;
                        &nbsp;
                        {{-- Next Page Link --}}
                        @if ($paginator->hasMorePages())
                            <span class="page-item">
                                <button type="button" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}" class="btn btn-dark" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" aria-label="بعدی">بعدی</button>
                            </span>
                        @else
                            <span class="page-item disabled" aria-disabled="true" aria-label="بعدی">
                                <span class="btn btn-dark" aria-hidden="true">بعدی</span>
                            </span>
                        @endif
                        &nbsp;

                        <br>
                        <br>
                        <ul class="pagination flex-row">

                        {{-- Pagination Elements  --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if($page==1 or $page==$paginator->lastPage())
                                        @if ($page == $paginator->currentPage())
                                            <li class="page-item active" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}" aria-current="page"><span class="btn btn-dark">{{ $page }}</span></li>
                                        @else
                                            <li class="page-item" wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"><button type="button" class="btn btn btn-light" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}">{{ $page }}</button></li>
                                        @endif
                                    @endif
                                    @if($page==1)
                                            <li style="padding: 10px 14px 0 14px" class="page-item">
                                                <p style="" class="small text-muted">
                                                    صفحه
                                                    <span class="fw-semibold">{{ $paginator->currentPage() }}</span>
                                                    از
                                                    <span class="fw-semibold">{{ $paginator->lastPage() }}</span>
                                                </p>
                                            </li>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    @endif
</div>
