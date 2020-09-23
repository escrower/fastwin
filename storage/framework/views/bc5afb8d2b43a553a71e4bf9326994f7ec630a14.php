

<?php $__env->startSection('content'); ?>
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
							<?php echo e($user->username); ?>

						</h3>
					</div>
				</div>
				<div class="kt-portlet__body">
					<div class="kt-widget28">
						<div class="kt-widget28__visual" style="background: url(<?php echo e($user->avatar); ?>) bottom center no-repeat"></div>
						<div class="kt-widget28__wrapper kt-portlet__space-x">
							<div class="tab-content">
								<div id="menu11" class="tab-pane active">
									<div class="kt-widget28__tab-items">
										<div class="kt-widget12">
											<?php if(!$user->fake): ?>
											<div class="kt-widget12__content">
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Cумма пополнений</span> 
														<span class="kt-widget12__value"><?php echo e($pay); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Cумма выводов</span> 
														<span class="kt-widget12__value"><?php echo e($withdraw); ?> р.</span>	
													</div>		 	 
												</div>
												<div class="kt-widget12__item">	
													<div class="kt-widget12__info text-center">				 	 
														<span class="kt-widget12__desc">Сумма обменов</span> 
														<span class="kt-widget12__value"><?php echo e($exchanges); ?> р.</span>
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
														<span class="kt-widget12__value"><?php echo e($jackpotWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($jackpotLose); ?> р.</span>	
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
														<span class="kt-widget12__value"><?php echo e($wheelWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($wheelLose); ?> р.</span>	
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
														<span class="kt-widget12__value"><?php echo e($crashWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($crashLose); ?> р.</span>	
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
														<span class="kt-widget12__value"><?php echo e($coinWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($coinLose); ?> р.</span>	
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
														<span class="kt-widget12__value"><?php echo e($battleWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($battleLose); ?> р.</span>	
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
														<span class="kt-widget12__value"><?php echo e($diceWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($diceLose); ?> р.</span>	
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
														<span class="kt-widget12__value"><?php echo e($betWin); ?> р.</span>
													</div>

													<div class="kt-widget12__info text-center">
														<span class="kt-widget12__desc">Проиграл</span> 
														<span class="kt-widget12__value"><?php echo e($betLose); ?> р.</span>	
													</div>		 	 
												</div>
											</div>
											<?php endif; ?>
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
				<?php if(!$user->fake): ?>
				<form class="kt-form" method="post" action="/admin/user/save">
					<div class="kt-portlet__body">
						<input name="id" value="<?php echo e($user->id); ?>" type="hidden">
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Фамилия Имя:</label>
								<input type="text" class="form-control" value="<?php echo e($user->username); ?>" disabled>
							</div>
							<div class="col-lg-6">
								<label class="">IP адрес:</label>
								<input type="text" class="form-control" value="<?php echo e($user->ip); ?>" disabled>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Баланс:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="balance" value="<?php echo e($user->balance); ?>">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div>
							<div class="col-lg-6">
								<label>Бонусы:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="bonus" value="<?php echo e($user->bonus); ?>">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-diamond"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Привилегии:</label>
								<select class="form-control" name="priv">
									<option value="admin" <?php if($user->is_admin): ?> selected <?php endif; ?>>Администратор</option>
									<option value="moder" <?php if($user->is_moder): ?> selected <?php endif; ?>>Модератор
									</option>
									<option value="youtuber" <?php if($user->is_youtuber): ?> selected <?php endif; ?>>YouTube`r</option>
									<option value="user" <?php if(!$user->is_admin && !$user->is_moder && !$user->is_youtuber): ?> selected <?php endif; ?>>Пользователь</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>Страница VK:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="https://vk.com/id<?php echo e($user->user_id); ?>" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-vk"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label class="">Бан на сайте:</label>
								<select class="form-control" name="ban">
									<option value="0" <?php if($user->ban == 0): ?> selected <?php endif; ?>>Нет</option>
									<option value="1" <?php if($user->ban == 1): ?> selected <?php endif; ?>>Да</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>Причина бана на сайте:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="ban_reason" value="<?php echo e($user->ban_reason); ?>">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-exclamation-triangle"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label class="">Бан в чате до:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="banchat" value="<?php echo e(!is_null($user->banchat) ? \Carbon\Carbon::parse($user->banchat)->format('d.m.Y H:i:s') : ''); ?>">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-calendar-o"></i></span></span>
								</div>
							</div>
							<div class="col-lg-6">
								<label>Причина бана в чате:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="banchat_reason" value="<?php echo e($user->banchat_reason); ?>">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-exclamation-triangle"></i></span></span>
								</div>
							</div>
						</div>
						<div class="form-group row">  
							<div class="col-lg-6">
								<label class="">Отыграно:</label> 
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="requery" value="<?php echo e($user->requery); ?>">
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div> 
							<div class="col-lg-6"> 
								<label class="">Реферальная ссылка:</label> 
								<div class="kt-input-icon">
									<input type="text" class="form-control" name="ref_id" value="<?php echo e(strtolower($_SERVER['REQUEST_SCHEME']).'://'); ?><?php echo e(strtolower($settings->domain)); ?>/?ref=<?php echo e($u->unique_id); ?>" disabled> 
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-link"></i></span></span> 
								</div> 
							</div>  
						</div>
						<div class="form-group row">  
						<div class="col-lg-6">
								<label>Привёл игроков по реф.ссылке:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="<?php echo e($user->link_reg); ?>" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-diamond"></i></span></span>
								</div>
							</div>
													<div class="col-lg-6">
								<label>Заработанные деньги на реферальной системе:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="<?php echo e($user->ref_money); ?>" disabled>
									<span class="kt-input-icon__icon kt-input-icon__icon--right"><span><i class="la la-rub"></i></span></span>
								</div>
							</div>
					</div>
					<div class="form-group row">
											<div class="col-lg-6">
								<label>Все заработанные деньги на реферальной системе:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="<?php echo e($user->ref_money_all); ?>" disabled>
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
									  	  <?php $__currentLoopData = $sends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										  <tr class="col-xl-8">
											<td><a href="/admin/user/<?php echo e($s['id']); ?>"><?php echo e($s['username']); ?></a></td>
											<td><?php echo e($s['sum']); ?></td>
											<td><?php echo e($s['date']); ?></td>
										  </tr>
										  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
									  	  <?php $__currentLoopData = $sends_from; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										  <tr>
											<td><a href="/admin/user/<?php echo e($s['id']); ?>"><?php echo e($s['username']); ?></a></td>
											<td><?php echo e($s['sum']); ?></td>
											<td><?php echo e($s['date']); ?></td>
										  </tr>
										  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
				<?php else: ?>
				<form class="kt-form" method="post" action="/admin/user/save">
					<div class="kt-portlet__body">
						<input name="id" value="<?php echo e($user->id); ?>" type="hidden">
						<div class="form-group row">
							<input type="hidden" class="form-control" name="balance" value="<?php echo e($user->balance); ?>">
							<input type="hidden" class="form-control" name="bonus" value="<?php echo e($user->bonus); ?>">
							<input type="hidden" class="form-control" name="ban" value="<?php echo e($user->ban); ?>">
							<div class="col-lg-6">
								<label>Фамилия Имя:</label>
								<input type="text" class="form-control" value="<?php echo e($user->username); ?>" disabled>
							</div>
							<div class="col-lg-6">
								<label>Время ставки</label>
								<select class="form-control" name="time">
									<option value="1" <?php echo e($user->time == 1 ? 'selected' : ''); ?>>Утром (с 6ч до 12ч)</option>
									<option value="2" <?php echo e($user->time == 2 ? 'selected' : ''); ?>>Днем (с 12ч до 18ч)</option>
									<option value="3" <?php echo e($user->time == 3 ? 'selected' : ''); ?>>Вечером (с 18ч до 00ч)</option>
									<option value="4" <?php echo e($user->time == 4 ? 'selected' : ''); ?>>Ночью (с 00ч до 6ч)</option>
									<option value="0" <?php echo e($user->time == 0 ? 'selected' : ''); ?>>Все время</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Привилегии:</label>
								<select class="form-control" name="priv">
									<option value="admin" <?php if($user->is_admin): ?> selected <?php endif; ?>>Администратор</option>
									<option value="moder" <?php if($user->is_moder): ?> selected <?php endif; ?>>Модератор</option>
									<option value="youtuber" <?php if($user->is_youtuber): ?> selected <?php endif; ?>>YouTube`r</option>
									<option value="user" <?php if(!$user->is_admin && !$user->is_moder && !$user->is_youtuber): ?> selected <?php endif; ?>>Пользователь</option>
								</select>
							</div>
							<div class="col-lg-6">
								<label>Страница VK:</label>
								<div class="kt-input-icon">
									<input type="text" class="form-control" value="https://vk.com/id<?php echo e($user->user_id); ?>" disabled>
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
				<?php endif; ?>
				<!--end::Form-->
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/admin/user.blade.php */ ?>