<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                <th>{!!trans('validation.attributes.date')!!}</th>
                <th>{!!trans('validation.attributes.name')!!}</th>
                <th>{!!trans('front.devices')!!}</th>
                <th>{!!trans('validation.attributes.quantity')!!}</th>
                <th>{!!trans('validation.attributes.cost')!!}</th>
                <th>{!!trans('validation.attributes.supplier')!!}</th>
                <th>{!!trans('validation.attributes.buyer')!!}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if(count($expenses) > 0)
                @foreach($expenses as $expense)
                <tr>
                    <td>{{$expense->date}}</td>
                    <td>{{$expense->name}}</td>
                    <td>{{count($expense->devices)}}</td>
                    <td>{{$expense->quantity}}</td>
                    <td>{{$expense->cost}}</td>
                    <td>{{$expense->supplier}}</td>
                    <td>{{$expense->buyer}}</td>
                    <td><a href="javascript:" data-url="{!!route('expenses.do_destroy', $expense->id)!!}" data-modal="expenses_destroy"><i class="icon delete"></i></a></td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7">{!!trans('front.no_reports')!!}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $expenses->render() !!}
</div>