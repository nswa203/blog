<ul class="pagination" role="navigation">
    @if ($paginator->hasPages())
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="page-link" aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="page-link" aria-hidden="true">&rsaquo;</span>
            </li>
        @endif
    @endif

    {{-- per Page selector must be available even if no pagination required --}}
    {{-- Requires the myPage js below                                       --}}
    @if (isset($paginator->pager))
        <div class="container ml-3">
            <div class="row">
                <div class="col-xs-3 col-xs-offset-3">
                    <div class="input-group number-spinner">
                        <span class="input-group-btn">
                            <li class="btn page-link page-btn" onClick="myPage('down')">
                                <span class="fas fa-arrow-down"></span>
                            </li>
                        </span>
                        <input class="page-in text-primary" onKeyUp="myPage(this.value)" type="text" value="{{ $paginator->perPage() }}">
                        <span class="input-group-btn">
                            <li class="btn page-link page-btn" onClick="myPage('up')">
                                <span class="fas fa-arrow-up"></span>
                            </li>
                        </span>
                        <span class="input-group-btn">
                            <li class="btn page-link page-btn-update" style="display:none;">
                                <a onClick="myPage('update')" href="{{ $elements[0][1] }}">
                                    <span class="fas fa-sync-alt "></span>
                                </a>
                            </li>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <script>
          function myPage(op) {
                pager={!! json_encode($paginator->pager) !!}
                var pageMin =pager['min' ]; pageMin !='undefined' ? pageMin  :  5;
                var pageMax =pager['max' ]; pageMax !='undefined' ? pageMax  : 50;
                var pageStep=pager['step']; pageStep!='undefined' ? pageStep :  5;
                console.log('Min='+pageMin+' Max='+pageMax+' Step='+pageStep);

                var elInputs =document.getElementsByClassName('page-in');           // Input  elements
                var elUpdates=document.getElementsByClassName('page-btn-update');   // Update elements
                var num=Number(elInputs[0].value);
                if      (op=='up'    ) { num=num+pageStep<pageMax ? num+pageStep : pageMax; }
                else if (op=='down'  ) { num=num-pageStep>pageMin ? num-pageStep : pageMin; }
                else if (op=='update') {         }
                else                   { num=op; }

                for (i=0; i<elInputs.length; ++i) {                                 
                    elInputs[i].value=num;
                    if (op=='update') {
                        elUpdates[i].firstElementChild.href=elUpdates[0].firstElementChild.href+'&pp='+num;
                    } else { elUpdates[i].style.display='block'; }   
                }
                console.log('Input='+elInputs[0].value+' Link='+elUpdates[0].firstElementChild.href);
            }    
        </script>

    @endif
</ul>
