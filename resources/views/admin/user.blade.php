@extends('admin')

@section('content')
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Редактирование пользователя</h3>
	</div>
</div>
<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="row">
		<div class="col-xl-4">
			<div class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay">
				<div class="kt-portlet__head kt-portlet__space-x">
					<div class="kt-portlet__head-label" style="width: 100%;">
						<h3 class="kt-portlet__head-title text-center" style="width: 100%;">
							{{$user->username}}
						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="kt-widget28">
						<div class="kt-widget28__visual" style="background: url({{$user->avatar}}) bottom center no-repeat"></div>
						<div class="kt-widget28__wrapper kt-portlet__space-x">
							<div class="tab-content">
								<div id="menu11" class="tab-pane active">
									<div class="kt-widget28__tab-items">
										<div class="kt-widget12">
											@if(!$user->fake)
											<div class="kt-widget12__content">
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Cумма пополнений</span> 
														<span class="kt-widget12__value">{{$pay}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Cумма выводов</span> 
														<span class="kt-widget12__value">{{$withdraw}} р.</span>	
													</div>		 	 
												</div>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Сумма обменов</span> 
														<span class="kt-widget12__value">{{$exchanges}} р.</span>
													</div>
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Ставки Jackpot
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$jackpotWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$jackpotLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Ставки Wheel
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$wheelWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$wheelLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Ставки Crash
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$crashWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$crashLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Ставки PvP
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$coinWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$coinLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Ставки Battle
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$battleWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$battleLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Ставки Dice
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$diceWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$diceLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											<div class="kt-widget12__content">
												<h6 class="block capitalize-font text-center">
													Итог
												</h6>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Выиграл</span> 
														<span class="kt-widget12__value">{{$betWin}} р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value">{{$betLose}} р.</span>	
													</div>		 	 
												</div>
											</div>
											@endif
										</div>
									</div>					      	 		      	
								</div>					     
							</div>
						</div>			 	 
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-8">
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Информация о пользователе
						</h3>
					</div>
				</div>
				<!--begin::Form-->
				@if(!$user->fake)
				<form class="kt-form" method="post" action="/admin/user/save">
					<div class="kt-portlet__body">
						<input name="id" value="{{$user->id}}" type="hidden">
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Фамилия Имя:</label>
								<input type="text" class="form-control" value="{{$user->username}}" disabled>
							</div>
							<div class="col-lg-6">
								<label class="">IP адрес:</label>
								<input type="text" class="form-control" value="{{$user->ip}}" disabled>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Баланс:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="balance" value="{{$user->balance}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div>
							<div class="col-lg-6">
								<label>Бонусы:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="bonus" value="{{$user->bonus}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-diamond"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Привилегии:</label>
								<select class="form-control" name="priv">
									<option value="admin" @if($user->is_admin) selected @endif>Администратор</option>
									<option value="moder" @if($user->is_moder) selected @endif>Модератор
									</option>
									<option value="youtuber" @if($user->is_youtuber) selected @endif>YouTube`r</option>
									<option value="user" @if(!$user->is_admin && !$user->is_moder && !$user->is_youtuber) selected @endif>Пользователь</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>Страница VK:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="https://vk.com/id{{$user->user_id}}" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-vk"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label class="">Бан на сайте:</label>
								<select class="form-control" name="ban">
									<option value="0" @if($user->ban == 0) selected @endif>Нет</option>
									<option value="1" @if($user->ban == 1) selected @endif>Да</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>Причина бана на сайте:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="ban_reason" value="{{$user->ban_reason}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-exclamation-triangle"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label class="">Бан в чате до:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="banchat" value="{{ !is_null($user->banchat) ? \Carbon\Carbon::parse($user->banchat)->format('d.m.Y H:i:s') : '' }}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-calendar-o"></i></span></span>
								</div>
							</div>
							<div class="col-lg-6">
								<label>Причина бана в чате:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="banchat_reason" value="{{$user->banchat_reason}}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-exclamation-triangle"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">  
							<div class="col-lg-6">
								<label class="">Отыграно:</label> 
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="requery" value="{{ $user->requery }}">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div> 
							<div class="col-lg-6"> 
								<label class="">Реферальная ссылка:</label> 
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="ref_id" value="{{ strtolower($_SERVER['REQUEST_SCHEME']).'://' }}{{ strtolower($settings->domain) }}/?ref={{$u->unique_id}}" disabled> 
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-link"></i></span></span> 
								</div> 
							</div>  
						</div>
						<div class="form-group row">  
						<div class="col-lg-6">
								<label>Привёл игроков по реф.ссылке:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="{{$user->link_reg}}" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-diamond"></i></span></span>
								</div>
							</div>
													<div class="col-lg-6">
								<label>Заработанные деньги на реферальной системе:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="{{$user->ref_money}}" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div>
					</div>
					<div class="form-group row">
											<div class="col-lg-6">
								<label>Все заработанные деньги на реферальной системе:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="{{$user->ref_money_all}}" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div>
							</div>
			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Переводы пользователя
						</h3>
					</div>
				</div>
										<table class="table mb-0"> 
										<thead>
										  <tr>
											<th>Кому</th> 
											<th>Сумма</th>
											<th>Дата</th>
										  </tr> 
										</thead>
										<tbody>
									  	  @foreach($sends as $s)
										  <tr class="col-xl-8">
											<td><a href="/admin/user/{{$s['id']}}">{{$s['username']}}</a></td>
											<td>{{$s['sum']}}</td>
											<td>{{$s['date']}}</td>
										  </tr>
										  @endforeach
										</tbody>
									</table>
					</div>

			<div class="kt-portlet">
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Переводы от других пользователей.
						</h3>
					</div>
				</div>
									<table class="table mb-0">
										<thead>
										  <tr>
											<th>От кого</th>
											<th>Сумма</th>
											<th>Дата</th>
										  </tr>
										</thead>
										<tbody>
									  	  @foreach($sends_from as $s)
										  <tr>
											<td><a href="/admin/user/{{$s['id']}}">{{$s['username']}}</a></td>
											<td>{{$s['sum']}}</td>
											<td>{{$s['date']}}</td>
										  </tr>
										  @endforeach
										</tbody>
									</table>
					</div>
					<div class="kt-portlet__foot kt-portlet__foot--solid">
						<div class="kt-form__actions">
							<div class="row">
								<div class="col-12">
									<button type="submit" class="btn btn-brand">Сохранить</button>
								</div>
							</div>
						</div>
					</div>
				</form>
				@else
				<form class="kt-form" method="post" action="/admin/user/save">
					<div class="kt-portlet__body">
						<input name="id" value="{{$user->id}}" type="hidden">
						<div class="form-group row">
							<input type="hidden" class="form-control" name="balance" value="{{$user->balance}}">
							<input type="hidden" class="form-control" name="bonus" value="{{$user->bonus}}">
							<input type="hidden" class="form-control" name="ban" value="{{$user->ban}}">
							<div class="col-lg-6">
								<label>Фамилия Имя:</label>
								<input type="text" class="form-control" value="{{$user->username}}" disabled>
							</div>
							<div class="col-lg-6">
								<label>Время ставки</label>
								<select class="form-control" name="time">
									<option value="1" {{ $user->time == 1 ? 'selected' : '' }}>Утром (с 6ч до 12ч)</option>
									<option value="2" {{ $user->time == 2 ? 'selected' : '' }}>Днем (с 12ч до 18ч)</option>
									<option value="3" {{ $user->time == 3 ? 'selected' : '' }}>Вечером (с 18ч до 00ч)</option>
									<option value="4" {{ $user->time == 4 ? 'selected' : '' }}>Ночью (с 00ч до 6ч)</option>
									<option value="0" {{ $user->time == 0 ? 'selected' : '' }}>Все время</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Привилегии:</label>
								<select class="form-control" name="priv">
									<option value="admin" @if($user->is_admin) selected @endif>Администратор</option>
									<option value="moder" @if($user->is_moder) selected @endif>Модератор</option>
									<option value="youtuber" @if($user->is_youtuber) selected @endif>YouTube`r</option>
									<option value="user" @if(!$user->is_admin && !$user->is_moder && !$user->is_youtuber) selected @endif>Пользователь</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>Страница VK:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="https://vk.com/id{{$user->user_id}}" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-vk"></i></span></span>
								</div>
							</div>
						</div>
					</div>
					<div class="kt-portlet__foot kt-portlet__foot--solid">
						<div class="kt-form__actions">
							<div class="row">
								<div class="col-12">
									<button type="submit" class="btn btn-brand">Сохранить</button>
								</div>
							</div>
						</div>
					</div>
				</form>
				@endif
				<!--end::Form-->
			</div>
		</div>
	</div>
</div>
@endsection