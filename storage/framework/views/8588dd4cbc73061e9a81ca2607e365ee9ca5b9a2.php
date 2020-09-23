

<?php $__env->startSection('content'); ?>
<script src="/dash/js/dtables.js" type="text/javascript"></script>
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Боты</h3>
	</div>
</div>

<div class="kt-content kt-grid__item kt-grid__item--fluid" id="kt_content">
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand flaticon2-user"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					Список ботов
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						<a data-toggle="modal" href="#new" class="btn btn-success btn-elevate btn-icon-sm">
							<i class="la la-plus"></i>
							Добавить
						</a>
					</div>	
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">

			<!--begin: Datatable -->
			<table class="table table-striped- table-bordered table-hover table-checkable" id="dtable">
				<thead>
					<tr>
						<th>ID</th>
						<th>Пользователь</th>
						<th>Время ставки</th>
						<th>Профиль VK</th>
						<th>Действия</th>
					</tr>
				</thead>
				<tbody>
					<?php $__currentLoopData = $bots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td><?php echo e($bot->id); ?></td>
						<td><img src="<?php echo e($bot->avatar); ?>" style="width:26px;border-radius:50%;margin-right:10px;vertical-align:middle;"><?php echo e($bot->username); ?></td>
						<td><?php echo e($bot->time == 0 ? 'Все время' : ''); ?><?php echo e($bot->time == 1 ? 'Утром (с 6ч до 12ч)' : ''); ?><?php echo e($bot->time == 2 ? 'Днем (с 12ч до 18ч)' : ''); ?><?php echo e($bot->time == 3 ? 'Вечером (с 18ч до 00ч)' : ''); ?><?php echo e($bot->time == 4 ? 'Ночью (с 00ч до 6ч)' : ''); ?></td>
						<td><a href="https://vk.com/id<?php echo e($bot->user_id); ?>" target="_blank">Перейти</a></td>
						<td><a href="/admin/user/<?php echo e($bot->id); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Редактировать"><i class="la la-edit"></i></a><a href="/admin/bots/delete/<?php echo e($bot->id); ?>" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Удалить"><i class="la la-trash"></i></a></td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>

			<!--end: Datatable -->
		</div>
	</div>
</div>
<div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="newLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Добавление бота</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="kt-form-new" method="post" action="/admin/fakeSave" id="save">
				<div class="modal-body">
					<div class="form-group">
						<label for="name">Ссылка на страницу VK</label>
						<input type="text" class="form-control" placeholder="https://vk.com/id..." name="name" id="url">
					</div>
					<div class="form-group">
						<label for="name">Время ставки</label>
						<select class="form-control" name="time">
							<option value="1">Утром (с 6ч до 12ч)</option>
							<option value="2">Днем (с 12ч до 18ч)</option>
							<option value="3">Вечером (с 18ч до 00ч)</option>
							<option value="4">Ночью (с 00ч до 6ч)</option>
							<option value="0">Все время</option>
						</select>
					</div>
					<div class="row" id="prof" style="display: none;">
						<div class="col-xl-12">
							<div class="kt-section__body">
								<input type="hidden" value="" name="vkId" id="vkId">
								<input type="hidden" value="" name="avatar" id="avatar">
								<div class="form-group row">
									<label class="col-xl-3 col-lg-3 col-form-label">Фотография</label>
									<div class="col-lg-9 col-xl-6">
										<div class="kt-avatar kt-avatar--outline kt-avatar--circle" id="kt_apps_user_add_avatar">
											<img class="kt-avatar__holder" id="ava" src=""/>
										</div>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-xl-3 col-lg-3 col-form-label">Имя Фамилия</label>
									<div class="col-lg-9 col-xl-9">
										<input class="form-control" type="text" value="" name="name" id="name" readonly>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
					<button type="submit" class="btn btn-primary">Добавить</button>
				</div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /* /var/www/html/resources/views/admin/bots.blade.php */ ?>