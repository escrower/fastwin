

<?php $__env->startSection('content'); ?>
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Настройки</h3>
	</div>
</div>

<div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="kt-portlet kt-portlet--tabs">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-toolbar">
				<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#site" role="tab" aria-selected="true">
							Настройки сайта
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#jackpot" role="tab" aria-selected="false">
							Jackpot
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#wheel" role="tab" aria-selected="false">
							Wheel
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#crash" role="tab" aria-selected="false">
							Crash
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#pvp" role="tab" aria-selected="false">
							PvP
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#battle" role="tab" aria-selected="false">
							Battle
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#dice" role="tab" aria-selected="false">
							Dice
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#hilo" role="tab" aria-selected="false">
							HiLo
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#tower" role="tab" aria-selected="false">
							Tower
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#fake" role="tab" aria-selected="false">
							Система фейковых ставок
						</a>
					</li>
				</ul>
			</div>
		</div>
		<form class="kt-form" method="post" action="/admin/setting/save">
			<div class="kt-portlet__body">
				<div class="tab-content">
					<div class="tab-pane active" id="site" role="tabpanel">
						<div class="kt-section">
							<h3 class="kt-section__title">
								Общие настройки:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Доменное имя:</label>
									<input type="text" class="form-control" placeholder="domain.ru" value="<?php echo e($settings->domain); ?>" name="domain">
								</div>
								<div class="col-lg-4">
									<label>Имя сайта:</label>
									<input type="text" class="form-control" placeholder="sitename.ru" value="<?php echo e($settings->sitename); ?>" name="sitename">
								</div>
								<div class="col-lg-4">
									<label>Заголовок сайта (титул):</label>
									<input type="text" class="form-control" placeholder="sitename.ru - краткое описание" value="<?php echo e($settings->title); ?>" name="title">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Описание для поисковых систем:</label>
									<input type="text" class="form-control" placeholder="Описание для сайта..." value="<?php echo e($settings->description); ?>" name="description">
								</div>
								<div class="col-lg-4">
									<label>Ключевые слова для поисковых систем:</label>
									<input type="text" class="form-control" placeholder="сайт, имя, домен и тд..." value="<?php echo e($settings->keywords); ?>" name="keywords">
								</div>
								<div class="col-lg-4">
									<label>Замена цензурных слов в чате на:</label>
									<input type="text" class="form-control" placeholder="i ❤ site" value="<?php echo e($settings->censore_replace); ?>" name="censore_replace">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Система фейковых ставок:</label>
									<select class="form-control" name="fakebets">
										<option value="0" <?php echo e(($settings->fakebets == 0) ? 'selected' : ''); ?>>Выключены</option>
										<option value="1" <?php echo e(($settings->fakebets == 1) ? 'selected' : ''); ?>>Включены</option>
									</select>
								</div>
								<div class="col-lg-4">
									<label>Минимальная сумма для обмена бонусов:</label>
									<input type="text" class="form-control" placeholder="1000" value="<?php echo e($settings->exchange_min); ?>" name="exchange_min">
								</div>
								<div class="col-lg-4">
									<label>Курс для обмена бонусов:</label>
									<input type="text" class="form-control" placeholder="2" value="<?php echo e($settings->exchange_curs); ?>" name="exchange_curs">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Интервал выдачи бонуса за подписку (каждые N минут):</label>
									<input type="text" class="form-control" placeholder="15" value="<?php echo e($settings->bonus_group_time); ?>" name="bonus_group_time">
								</div>
								<div class="col-lg-4">
									<label>Кол-во активных рефералов для получения бонуса:</label>
									<input type="text" class="form-control" placeholder="8" value="<?php echo e($settings->max_active_ref); ?>" name="max_active_ref">
								</div>
								<div class="col-lg-4">
									<label>Сумма пополнения для использования чата. 0 - Отключено:</label>
									<input type="text" class="form-control" placeholder="0" value="<?php echo e($settings->chat_dep); ?>" name="chat_dep">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Минимальная сумма пополнения для выдачи бонуса. 0 - Отключено</label>
									<input type="text" class="form-control" placeholder="0" value="<?php echo e($settings->dep_bonus_min); ?>" name="dep_bonus_min">
								</div>
								<div class="col-lg-4">
									<label>Процент от суммы пополнения в качестве бонуса:</label>
									<input type="text" class="form-control" placeholder="0" value="<?php echo e($settings->dep_bonus_perc); ?>" name="dep_bonus_perc">
								</div>
								<div class="col-lg-4">
									<label>Процент от суммы выигрышей для отыгровки:</label>
									<input type="text" class="form-control" placeholder="0" value="<?php echo e($settings->requery_perc); ?>" name="requery_perc">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Процент от суммы ставки для отыгровки:</label>
									<input type="text" class="form-control" placeholder="0" value="<?php echo e($settings->requery_bet_perc); ?>" name="requery_bet_perc">
								</div>
								<div class="col-lg-4">
									<label>Тех. работы:</label>
									<select class="form-control" name="site_disable">
										<option value="0" <?php echo e(($settings->site_disable == 0) ? 'selected' : ''); ?>>Выключены</option>
										<option value="1" <?php echo e(($settings->site_disable == 1) ? 'selected' : ''); ?>>Включены</option>
									</select>
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Настройки реферальной системы:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Какой процент от выигрыша получает пригласивший:</label>
									<input type="text" class="form-control" placeholder="Введите процент" value="<?php echo e($settings->ref_perc); ?>" name="ref_perc">
								</div>
								<div class="col-lg-4">
									<label>Какую сумму получает приглашенный на реальный счет:</label>
									<input type="text" class="form-control" placeholder="Введите сумму" value="<?php echo e($settings->ref_sum); ?>" name="ref_sum">
								</div>
								<div class="col-lg-4">
									<label>Минимальная сумма для снятия с реф. счета:</label>
									<input type="text" class="form-control" placeholder="Введите сумму" value="<?php echo e($settings->min_ref_withdraw); ?>" name="min_ref_withdraw">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Остальные настройки:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Минимальная сумма пополнения:</label>
									<input type="text" class="form-control" placeholder="Введите сумму" value="<?php echo e($settings->min_dep); ?>" name="min_dep">
								</div>
								<div class="col-lg-4">
									<label>Сумма пополнений для совершения вывода:</label>
									<input type="text" class="form-control" placeholder="Введите сумму" value="<?php echo e($settings->min_dep_withdraw); ?>" name="min_dep_withdraw">
								</div>
								<div class="col-lg-4">
									<label>Какую часть отдавать от суммы пополнения (1/N):</label>
									<input type="text" class="form-control" placeholder="Введите сумму" value="<?php echo e($settings->profit_koef); ?>" name="profit_koef">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Настройки группы VK:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>Ссылка на группу VK:</label>
									<input type="text" class="form-control" placeholder="https://vk.com/..." value="<?php echo e($settings->vk_url); ?>" name="vk_url">
								</div>
								<div class="col-lg-4">
									<label>Ссылка на сообщения группы VK:</label>
									<input type="text" class="form-control" placeholder="https://vk.com/im?media=&sel=..." value="<?php echo e($settings->vk_support_link); ?>" name="vk_support_link">
								</div>
								<div class="col-lg-4">
									<label>Сервисный ключ доступа приложения:</label>
									<input type="text" class="form-control" placeholder="1f27230c1f27230c1f27230c841..." value="<?php echo e($settings->vk_service_key); ?>" name="vk_service_key">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Настройки платежной системы FreeKassa:
							</h3>
							<div class="form-group row">
								<div class="col-lg-4">
									<label>ID Магазина FK:</label>
									<input type="text" class="form-control" placeholder="Fxxxxxx" value="<?php echo e($settings->fk_mrh_ID); ?>" name="fk_mrh_ID">
								</div>
								<div class="col-lg-4">
									<label>FK Secret 1:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->fk_secret1); ?>" name="fk_secret1">
								</div>
								<div class="col-lg-4">
									<label>FK Secret 2:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->fk_secret2); ?>" name="fk_secret2">
								</div>
							</div>
							<div class="form-group row">
								<div class="col-lg-6">
									<label>FK Кошелек:</label>
									<input type="text" class="form-control" placeholder="Pxxxxxx" value="<?php echo e($settings->fk_wallet); ?>" name="fk_wallet">
								</div>
								<div class="col-lg-6">
									<label>FK API Key:</label>
									<input type="text" class="form-control" placeholder="xxxxxxx" value="<?php echo e($settings->fk_api); ?>" name="fk_api">
								</div>
							</div>
						</div>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Настройка комиссии на вывод средств:
							</h3>
							<div class="form-group row">
								<div class="col-sm-1-5">
									<label>QIWI (+%)</label>
									<input type="text" class="form-control" name="qiwi_com_percent" value="<?php echo e($settings->qiwi_com_percent); ?>" placeholder="%">
									<label>QIWI (+руб)</label>
									<input type="text" class="form-control" name="qiwi_com_rub" value="<?php echo e($settings->qiwi_com_rub); ?>" placeholder="руб">
									<label>QIWI Мин. сумма</label>
									<input type="text" class="form-control" name="qiwi_min" value="<?php echo e($settings->qiwi_min); ?>" placeholder="Мин. сумма">
								</div>
								<div class="col-sm-1-5">
									<label>Yandex (+%)</label>
									<input type="text" class="form-control" name="yandex_com_percent" value="<?php echo e($settings->yandex_com_percent); ?>" placeholder="%">
									<label>Yandex (+руб)</label>
									<input type="text" class="form-control" name="yandex_com_rub" value="<?php echo e($settings->yandex_com_rub); ?>" placeholder="руб">
									<label>Yandex Мин. сумма</label>
									<input type="text" class="form-control" name="yandex_min" value="<?php echo e($settings->yandex_min); ?>" placeholder="Мин. сумма">
								</div>
								<div class="col-sm-1-5">
									<label>WebMoney (+%)</label>
									<input type="text" class="form-control" name="webmoney_com_percent" value="<?php echo e($settings->webmoney_com_percent); ?>" placeholder="%">
									<label>WebMoney (+руб)</label>
									<input type="text" class="form-control" name="webmoney_com_rub" value="<?php echo e($settings->webmoney_com_rub); ?>" placeholder="руб">
									<label>WebMoney Мин. сумма</label>
									<input type="text" class="form-control" name="webmoney_min" value="<?php echo e($settings->webmoney_min); ?>" placeholder="Мин. сумма">
								</div>
								<div class="col-sm-1-5">
									<label>VISA (+%)</label>
									<input type="text" class="form-control" name="visa_com_percent" value="<?php echo e($settings->visa_com_percent); ?>" placeholder="%">
									<label>VISA (+руб)</label>
									<input type="text" class="form-control" name="visa_com_rub" value="<?php echo e($settings->visa_com_rub); ?>" placeholder="руб">
									<label>VISA Мин. сумма</label>
									<input type="text" class="form-control" name="visa_min" value="<?php echo e($settings->visa_min); ?>" placeholder="Мин. сумма">
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="jackpot" role="tabpanel">
						<div class="form-group">
							<label>Комиссия игры в %:</label>
							<input type="text" class="form-control" placeholder="Введите процент" value="<?php echo e($settings->jackpot_commission); ?>" name="jackpot_commission">
						</div>
						<?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<div class="kt-section">
							<h3 class="kt-section__title">
								Комната "<?php echo e($r->title); ?>":
							</h3>
							<div class="form-group row">
								<div class="col-lg-3">
									<label>Таймер:</label>
									<input type="text" class="form-control" name="time_<?php echo e($r->name); ?>" value="<?php echo e($r->time); ?>" placeholder="Таймер">
								</div>
								<div class="col-lg-3">
									<label>Минимальная сумма ставки:</label>
									<input type="text" class="form-control" name="min_<?php echo e($r->name); ?>" value="<?php echo e($r->min); ?>" placeholder="Минимальная сумма ставки">
								</div>
								<div class="col-lg-3">
									<label>Максимальная сумма ставки:</label>
									<input type="text" class="form-control" name="max_<?php echo e($r->name); ?>" value="<?php echo e($r->max); ?>" placeholder="Максимальная сумма ставки">
								</div>
								<div class="col-lg-3">
									<label>Максимальное кол-во ставок для игрока:</label>
									<input type="text" class="form-control" name="bets_<?php echo e($r->name); ?>" value="<?php echo e($r->bets); ?>" placeholder="Макс. кол-во ставок для игрока">
								</div>
							</div>
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
					<div class="tab-pane" id="wheel" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-4">
								<label>Таймер:</label>
								<input type="text" class="form-control" placeholder="Таймер" value="<?php echo e($settings->wheel_timer); ?>" name="wheel_timer">
							</div>
							<div class="col-lg-4">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->wheel_min_bet); ?>" name="wheel_min_bet">
							</div>
							<div class="col-lg-4">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->wheel_max_bet); ?>" name="wheel_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="crash" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-4">
								<label>Таймер:</label>
								<input type="text" class="form-control" placeholder="Таймер" value="<?php echo e($settings->crash_timer); ?>" name="crash_timer">
							</div>
							<div class="col-lg-4">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->crash_min_bet); ?>" name="crash_min_bet">
							</div>
							<div class="col-lg-4">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->crash_max_bet); ?>" name="crash_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="pvp" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-4">
								<label>Комиссия игры в %:</label>
								<input type="text" class="form-control" placeholder="Макс. кол-во активных игр для игрока" value="<?php echo e($settings->flip_commission); ?>" name="flip_commission">
							</div>
							<div class="col-lg-4">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->flip_min_bet); ?>" name="flip_min_bet">
							</div>
							<div class="col-lg-4">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->flip_max_bet); ?>" name="flip_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="battle" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-3">
								<label>Таймер:</label>
								<input type="text" class="form-control" placeholder="Таймер" value="<?php echo e($settings->battle_timer); ?>" name="battle_timer">
							</div>
							<div class="col-lg-3">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->battle_min_bet); ?>" name="battle_min_bet">
							</div>
							<div class="col-lg-3">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->battle_max_bet); ?>" name="battle_max_bet">
							</div>
							<div class="col-lg-3">
								<label>Комиссия игры в %:</label>
								<input type="text" class="form-control" placeholder="Комиссия игры в %" value="<?php echo e($settings->battle_commission); ?>" name="battle_commission">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="dice" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->dice_min_bet); ?>" name="dice_min_bet">
							</div>
							<div class="col-lg-6">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->dice_max_bet); ?>" name="dice_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="hilo" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-3">
								<label>Таймер:</label>
								<input type="text" class="form-control" placeholder="Таймер" value="<?php echo e($settings->hilo_timer); ?>" name="hilo_timer">
							</div>
							<div class="col-lg-3">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->hilo_min_bet); ?>" name="hilo_min_bet">
							</div>
							<div class="col-lg-3">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->hilo_max_bet); ?>" name="hilo_max_bet">
							</div>
							<div class="col-lg-3">
								<label>Кол-во ставок для 1 игрока:</label>
								<input type="text" class="form-control" placeholder="Кол-во ставок" value="<?php echo e($settings->hilo_bets); ?>" name="hilo_bets">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="tower" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-3">
								<label>Минимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки" value="<?php echo e($settings->tower_min_bet); ?>" name="tower_min_bet">
							</div>
							<div class="col-lg-3">
								<label>Максимальная сумма ставки:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки" value="<?php echo e($settings->tower_max_bet); ?>" name="tower_max_bet">
							</div>
						</div>
					</div>
					<div class="tab-pane" id="fake" role="tabpanel">
						<div class="form-group row">
							<div class="col-lg-6">
								<label>Минимальная сумма ставки для фейка:</label>
								<input type="text" class="form-control" placeholder="Минимальная сумма ставки для фейка" value="<?php echo e($settings->fake_min_bet); ?>" name="fake_min_bet">
							</div>
							<div class="col-lg-6">
								<label>Максимальная сумма ставки для фейка:</label>
								<input type="text" class="form-control" placeholder="Максимальная сумма ставки для фейка" value="<?php echo e($settings->fake_max_bet); ?>" name="fake_max_bet">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="kt-portlet__foot">
				<div class="kt-form__actions">
					<button type="submit" class="btn btn-primary">Сохранить</button>
					<button type="reset" class="btn btn-secondary">Сбросить</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/admin/settings.blade.php */ ?>