@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/affiliate.css">
<div class="section">
    <div class="section-page">
        <div class="quest-banner affiliate">
            <div class="caption">
                <h1><span>Реферальная программа</span></h1>
			</div>
            <div class="info"><span>Зарабатывайте {{$settings->ref_perc}}% от суммы выигрыша вашего реферала.</span></div>
            <div class="info"><span>Ваши рефералы получают {{$settings->ref_sum}} бонусов при регистрации!</span></div>
        </div>
        <div class="affiliates-form">
            <div class="text">Ваша ссылка для привлечения рефералов:</div>
            <form>
                <div class="form-row">
                    <div class="form-field input-group">
                        <div class="input-valid">
                            <input class="input-field" type="text" name="code" id="code" readonly="" value="{{ strtolower($_SERVER['REQUEST_SCHEME']).'://' }}{{ strtolower($settings->domain) }}/?ref={{$u->unique_id}}">
                            <div class="input-group-append">
                                <button type="button" class="btn" onclick="copyToClipboard('#code')"><span>Скопировать</span></button>
                                <div class="copy-tooltip"><span>Скопировано</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="affiliate-stats">
            <div class="left">
                <div class="affiliate-stats-item">
                    <div class="wrap">
                        <div class="block">
                            <svg class="icon icon-coin bonus">
                                <use xlink:href="/img/symbols.svg#icon-coin"></use>
                            </svg>
                            <div class="num">{{$u->ref_money_all}}</div>
                            <div class="text">Доход всего</div>
                        </div>
                    </div>
                </div>
                <div class="affiliate-stats-item border-top">
                    <div class="wrap border-right">
                        <div class="block">
                            <svg class="icon icon-network">
                                <use xlink:href="/img/symbols.svg#icon-network"></use>
                            </svg>
                            <div class="num">{{$u->link_trans}}</div>
                            <div class="text">Переходов</div>
                        </div>
                    </div>
                    <div class="wrap">
                        <div class="block">
                            <svg class="icon icon-person">
                                <use xlink:href="/img/symbols.svg#icon-person"></use>
                            </svg>
                            <div class="num">{{$u->link_reg}}</div>
                            <div class="text">Регистраций</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="affiliate-stats-item full">
                    <div class="wrap">
                        <div class="block">
                            <svg class="icon icon-coin bonus">
                                <use xlink:href="/img/symbols.svg#icon-coin"></use>
                            </svg>
                            <div class="num">{{$u->ref_money}}</div>
                            <div class="text">Доступный баланс</div>
                            <span id="withdraw-button" class="" data-toggle="tooltip" data-placement="top" title="Минимальная сумма снятия {{ $settings->min_ref_withdraw }} монет"><button type="button" {{ $u->ref_money < $settings->min_ref_withdraw  ? 'disabled' : '' }} class="btn">Забрать</button></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection