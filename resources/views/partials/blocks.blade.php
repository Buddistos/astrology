{{--
@foreach($blocks as $block)
    @if(\Illuminate\Support\Facades\View::exists($block['view']) )
        @include($block['parent']['view'] ?: $block['view'], [
            'properties' => (isset($block['properties']) ? $block['properties'] : []),
            'props' => (isset($block['props']) ? $block['props'] : []),
            'parent' => $block['parent'],
            ''
        ])
    @endif
@endforeach
--}}
