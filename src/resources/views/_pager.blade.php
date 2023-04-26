<div class="mypagination">

    {!! $info !!}

    <div class="pagination-links pagi-rght">
        {!! $obj->links('pagination::bootstrap-4') !!}
        <div class="clr-fix"></div>
    </div>
    <div class="clr-fix"></div>
    {{--

        To show pagnation, following should be available to view:
        view()->share(array('paginationUI' => array(
            'perPage' => array(10, 20, 50),
            'perPageParam' => 'results_per_page',
            'pageNumParam' => 'questions_page'
        )));
    --}}
    @if (!empty($paginationUI) && is_array($paginationUI) && isset($paginationUI['perPage']) && is_array($paginationUI['perPage']) && isset($paginationUI['pageNumParam']) && isset($paginationUI['perPageParam']) &&
        ($obj->total() > (\Illuminate\Support\Facades\Input::get($paginationUI['pageNumParam']) * \Illuminate\Support\Facades\Input::get($paginationUI['perPageParam'])  )))

        <div style="float:right; margin-top:10px; margin-right: 20px;">
            <p style="float: right;">Jump to page:
                <input class="pagination-page-jump" page-num-param="{{$paginationUI['pageNumParam']}}" style="width:20px;">
            </p>
        </div>

        <p>Results per page:
            <select class="pagination-per-page" per-page-param="{{$paginationUI['perPageParam']}}">

                @foreach($paginationUI['perPage'] as $_v)
                    <option value="{{$_v}}" @if (\Illuminate\Support\Facades\Input::get($paginationUI['perPageParam']) == $_v) selected @endif>{{$_v}}</option>
                @endforeach
            </select>
        </p>

        <script>
            $(document).ready(function()
            {
                $('.pagination-page-jump').change(function(e)
                {
                    e.preventDefault();
                    e.stopPropagation();

                    v = $(this).val();

                    paginationLinks = $(this).parents('.mypagination').find('.pagination-links');
                    activeLink =  paginationLinks.find('li.active');
                    currentPageNum = activeLink.text();

                    // get the url of pagination so we can change page_number as active link doesn't have any url
                    linkA = $(paginationLinks.find('li:not(.active):not(.disabled)')[0]).find('a');
                    url = linkA.attr('href');

                    pageNumParam = '{{$paginationUI['pageNumParam']}}';
                    perPageParam = '{{$paginationUI['perPageParam']}}';
                    perPageParamValue = '';

                    if (paginationLinks.find('input.pagination-per-page').length)
                        perPageParamValue = paginationLinks.find('select.pagination-per-page').val();

                    pageNumParamRegex = pageNumParam + '=\\d{1,}';

                    re = new RegExp(pageNumParamRegex, "g");
                    updatedUrl = url.replace(re, pageNumParam + '=' + v) + '&' + perPageParam + '=' + perPageParamValue;
                    linkA.attr('href', updatedUrl).click();

                    return false;
                });

                $('.pagination-per-page').change(function(e)
                {
                    e.preventDefault();
                    e.stopPropagation();

                    v = $(this).val();

                    paginationLinks = $(this).parents('.mypagination').find('.pagination-links');
                    activeLink =  paginationLinks.find('li.active');
                    currentPageNum = activeLink.text();

                    if (paginationLinks.find('input.pagination-per-page').length)
                        currentPageNum = paginationLinks.find('input.pagination-page-jump').val();

                    // get the url of pagination so we can change page_number as active link doesn't have any url
                    linkA = $(paginationLinks.find('li:not(.active):not(.disabled)')[0]).find('a');
                    url = linkA.attr('href');

                    pageNumParam = '{{$paginationUI['pageNumParam']}}';
                    perPageParam = '{{$paginationUI['perPageParam']}}';
                    pageNumParamRegex = pageNumParam + '=\\d{1,}';

                    re = new RegExp(pageNumParamRegex, "g");
                    updatedUrl = url.replace(re, pageNumParam + '=' + currentPageNum) + '&' + perPageParam + '=' + v;
                    linkA.attr('href', updatedUrl).click();

                    return false;
                });
            });
        </script>
    @endif
    <?php
    // unset after usage
    $paginationUI = '';
    ?>
</div>
<div class="clr-fix"></div>