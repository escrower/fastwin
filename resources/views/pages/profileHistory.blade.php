@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/profileHistory.css">
<script type="text/javascript" src="/js/profileHistory.js"></script>
<div class="section">
    <div class="wallet_container">
        <div class="wallet_component">
            <div class="history_nav">
                <button type="button" class="btn isActive" data-tab="with"><span>История выводов</span></button>
                <button type="button" class="btn" data-tab="dep"><span>История пополнений</span></button>
            </div>
            <div class="history_wrapper with">
                <div class="withPager">
                    <div class="list">
                    	@if($withdraws->count() > 0)
                    	<div class="history_scroll">
							<table class="history_table">
								<thead>
									<tr>
										<th>Дата</th>
										<th>Система</th>
										<th>Сумма</th>
										<th class="text-right">Статус</th>
									</tr>
								</thead>
								<tbody>
									@foreach($withdraws as $w)
									<tr>
										<td>{{ Carbon\Carbon::parse($w->created_at)->format('d.m.Y') }}</td>
										<td>
											<div class="history_system"><div>{{$w->system}}</div><span class="popover-tip-block" data-toggle="popover-info" data-placement="top" data-contenthtml="{{$w->wallet}}"><span class="popover-tip-icon"><svg class="icon"><use xlink:href="/img/symbols.svg#icon-info"></use></svg></span></span></div>
										</td>
										<td>{{$w->valueWithCom}}р</td>
										<td class="text-right withStatus_{{$w->id}}">
											@if($w->status == 0)
											<div class="history_button" data-id="{{$w->id}}">
												<div class="history_icon">
													<svg class="icon">
														<use xlink:href="/img/symbols.svg#icon-timer"></use>
													</svg>
												</div>
												<div class="btn">
													<span>Отменить</span>
												</div>
											</div>
											@elseif($w->status == 1)
											<span class="color-green">Выплачено</span>
											@else
											Отменен
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
                        </div>
                        @else
                        <div class="history_empty">
                            <h4>N/A</h4>Вы еще не делали заявок
						</div>
                   		@endif
                    </div>
                </div>
            </div>
            <div class="history_wrapper dep" style="display: none;">
                <div class="withPager">
                    <div class="list">
                    	@if($pays->count() > 0)
                    	<div class="history_scroll">
							<table class="history_table">
								<thead>
									<tr>
										<th>Дата</th>
										<th>Система</th>
										<th class="text-right">Сумма</th>
									</tr>
								</thead>
								<tbody>
									@foreach($pays as $p)
									<tr>
										<td>{{ Carbon\Carbon::parse($p->created_at)->format('d.m.Y') }}</td>
										<td>
											<div class="history_system">{{$p->system}}</div>
										</td>
										<td class="text-right">{{$p->sum}}р</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
                        @else
                        <div class="history_empty">
                            <h4>N/A</h4>Вы еще не делали заявок
						</div>
                   		@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection