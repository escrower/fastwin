

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="/css/dice.css?v=1">
<script type="text/javascript" src="/js/dice.js?v=1"></script>
<div class="section game-section">
    <div class="container">
        <div class="game">
			<div class="game-component">
				<div class="game-block">
					<div class="game-area__wrap">
						<div class="game-area">
							<div class="progress-wrap">
								<div class="progress-item left">
									<div class="title">Мин. сумма: <span id="minBet"><?php echo e($settings->dice_min_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
									<div class="title">Макс. сумма: <span id="maxBet"><?php echo e($settings->dice_max_bet); ?></span> <svg class="icon icon-coin"><use xlink:href="/img/symbols.svg#icon-coin"></use></svg></div>
								</div>
							</div>
							<div class="top-corners"></div>
							<div class="bottom-corners"></div>
							<div class="game-area-content">
								<div class="dice">
									<div class="game-bar">
									
										<div class="bet-component">
											<div class="bet-form">
												<div class="two-cols">
													<div class="two-cols">
														<div class="form-row">
															<label>
																<div class="form-label"><span>Сумма ставки</span></div>
																<div class="form-row">
																	<div class="form-field">
																		<input type="text" name="sum" class="input-field no-bottom-radius" value="0.00" id="sum">
																		<button type="button" class="btn btn-bet-clear" data-action="clear">
																			<svg class="icon icon-close">
																				<use xlink:href="/img/symbols.svg#icon-close"></use>
																			</svg>
																		</button>
																		<div class="buttons-group no-top-radius">
																			<button type="button" class="btn btn-action" data-action="plus" data-value="0.10">+0.10</button>
																			<button type="button" class="btn btn-action" data-action="plus" data-value="1">+1.00</button>
																			<button type="button" class="btn btn-action" data-action="multiply" data-value="2">2X</button>
																			<button type="button" class="btn btn-action" data-action="divide" data-value="2">1/2</button>
																			<button type="button" class="btn btn-action" data-action="all">MAX</button>
																		</div>
																	</div>
																</div>
															</label>
														</div>
														<div class="form-row">
															<label>
																<div class="form-label"><span>Шанс</span></div>
																<div class="form-field">
																	<div class="input-valid">
																		<input class="input-field" value="90.00" id="chance">
																		<div class="input-suffix"><span id="chance_val">90.00</span> %</div>
																		<div class="valid"></div>
																	</div>
																	<div class="buttons-group no-top-radius">
																		<button type="button" class="btn btn-perc" data-action="min">MIN</button>
																		<button type="button" class="btn btn-perc" data-action="multiply" data-value="2">2X</button>
																		<button type="button" class="btn btn-perc" data-action="divide" data-value="2">1/2</button>
																		<button type="button" class="btn btn-perc" data-action="max">MAX</button>
																	</div>
																</div>
															</label>
														</div>
													</div>
													<div class="form-row">
														<div class="form-row">
															<label class="nvuti-exp">
																<span class="number" id="win">0.00</span>
																<div class="form-label"><span>Возможный выигрыш</span></div>
															</label>
														</div>
														<div class="two-cols">
															<div class="form-row">
																<button type="button" class="btn btn-green btn-play" data-type="min">
																	<div class="bet-chance">
																		<div class="chance-text">
																			<span>Меньше</span>
																			<p id="min_tick">0 - 899999</p>
																		</div>
																	</div>
																</button>
															</div>
															<div class="form-row">
																<button type="button" class="btn btn-green btn-play" data-type="max">
																	<div class="bet-chance">
																		<div class="chance-text">
																			<span>Больше</span>
																			<p id="max_tick">100000 - 999999</p>
																		</div>
																	</div>
																</button>
															</div>
														</div>
													</div>
												</div>
												<div class="game-dice"><span class="result"></span></div>
											</div>
											<div class="bet-footer">
												<button type="button" class="btn btn-light" data-toggle="modal" data-target="#fairModal">
													<svg class="icon icon-fairness">
														<use xlink:href="/img/symbols.svg#icon-fairness"></use>
													</svg><span>Честная игра</span>
												</button>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="game-history__wrap">
					<div class="hash">
						<span class="title">HASH:</span> <span class="text"><?php echo e($hash); ?></span>
					</div>
				</div>
				<?php if(auth()->guard()->guest()): ?>
				<div class="game-sign">
					<div class="game-sign-wrap">
						<div class="game-sign-block auth-buttons">
							Чтобы играть, необходимо быть авторизованным 
							<a href="/auth/vkontakte" class="btn">
								Войти через
								<svg class="icon icon-vk">
									<use xlink:href="/img/symbols.svg#icon-vk"></use>
								</svg>
							</a>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
        </div>
    </div>
</div>
<div class="section bets-section">
	<div class="container">
		<div class="game-stats">
			<div class="table-heading">
				<div class="thead">
					<div class="tr">
						<div class="th">Игрок</div>
						<div class="th">Ставка</div>
						<div class="th">Число</div>
						<div class="th">Цель</div>
						<div class="th">Шанс</div>
						<div class="th">Выигрыш</div>
						<div class="th"></div>
					</div>
				</div>
			</div>
			<div class="table-stats-wrap" style="min-height: 530px; max-height: 100%;">
				<div class="table-wrap">
					<table class="table">
						<tbody>
							<?php $__currentLoopData = $game; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="username">
									<button type="button" class="btn btn-link" data-id="<?php echo e($g['unique_id']); ?>">
										<span class="sanitize-user">
											<div class="sanitize-avatar"><img src="<?php echo e($g['avatar']); ?>" alt=""></div>
											<span class="sanitize-name"><?php echo e($g['username']); ?></span>
										</span>
									</button>
								</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span><?php echo e($g['sum']); ?></span>
											<svg class="icon icon-coin <?php echo e($g['balType']); ?>">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td><?php echo e($g['num']); ?></td>
								<td><?php echo e($g['range']); ?></td>
								<td><?php echo e($g['perc']); ?>%</td>
								<td>
									<div class="bet-number">
										<span class="bet-wrap">
											<span class="<?php echo e($g['win'] ? 'win' : 'lose'); ?>"><?php echo e($g['win'] ? '+'.$g['win_sum'] : $g['win_sum']); ?></span>
											<svg class="icon icon-coin">
												<use xlink:href="/img/symbols.svg#icon-coin"></use>
											</svg>
										</span>
									</div>
								</td>
								<td><button class="btn btn-primary checkGame" data-hash="<?php echo e($g['hash']); ?>">Проверить</button></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/pages/dice.blade.php */ ?>