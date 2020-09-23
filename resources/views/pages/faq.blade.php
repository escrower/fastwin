@extends('layout')

@section('content')
<link rel="stylesheet" href="/css/faq.css">
<div class="section">
    <div class="faq-component">
        <div class="faq-head">
            <h1 class="faq-caption">Ответы на вопросы</h1>
            @if($settings->vk_support_link)
            <div class="faq-link"><a class="btn btn-light" href="{{$settings->vk_support_link}}" target="_blank">Написать в поддержку</a></div>
            @endif
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-faq">
                        <use xlink:href="/img/symbols.svg#icon-faq"></use>
                    </svg> О сайте
                </div>
            </div>
            <div class="faq-content">
                <p>{{$settings->sitename}} - это увлекательные и доказуемые честные мини-игры.</p>
                <p>Играйте в игры и выигрывайте монеты, которые сможете обменять на реальные деньги.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-coin">
                        <use xlink:href="/img/symbols.svg#icon-coin"></use>
                    </svg> Монеты
                </div>
            </div>
            <div class="faq-content">
                <p>Монеты - это наша внутригровая валюта. Курс: 1.00 монета = 1.00 рубль</p>
                <p>Вы можете купить монеты на странице <a class="" data-toggle="modal" data-target="#walletModal">покупки монет</a> или получать бесплатно до 0.10 монет каждые 15 минут, на странице <a class="" href="/free">бесплатных монет</a></p>
            </div>
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-fairness">
                        <use xlink:href="/img/symbols.svg#icon-fairness"></use>
                    </svg> Честная игра
                </div>
            </div>
            <div class="faq-content">
                <p>Генератор случайных чисел создает доказуемые и абсолютно честные случайные числа, которые используются для определения результата каждой игры, сыгранной на сайте.</p>
                <p>Каждый пользователь может проверить исход любой игры полностью детерминированным способом. Предоставляя один параметр - клиентский хэш, на входы генератора случайных чисел, {{$settings->sitename}} не может манипулировать результатами в свою пользу.</p>
                <p>Генератор случайных чисел {{$settings->sitename}} позволяет каждой игре запрашивать любое количество случайных чисел из заданного начального числа клиента, начального числа сервера и одноразового номера.</p>
            </div>
        </div>
        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-affiliate">
                        <use xlink:href="/img/symbols.svg#icon-affiliate"></use>
                    </svg> Партнерская программа
                </div>
            </div>
            <div class="faq-content">
                <p>Приглашайте других игроков на наш сайт по <a class="" href="/affiliate">вашей реферальной ссылке</a> и зарабатывайте 5% от нашей прибыли с каждой ставки, сделанной вашим рефералом.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="caption">
                <div class="caption-block">
                    <svg class="icon icon-tasks">
                        <use xlink:href="/img/symbols.svg#icon-tasks"></use>
                    </svg> Вывод
                </div>
            </div>
            <div class="faq-content">
                <p>Деньги заработанные на промокодах выводить ЗАПРЕЩЕНО, Почему? </p>
                <p>Потому, что это как 'Демо' режим для ознакомления и удовольствия игры. </p>
                <p>Выводим только тем, кто пополнял свой баланс. </p>
                <p>Минимальный депозит на сайт 25 рублей, минимальный депозит для вывода 100 рублей.</p>
            </div>
        </div>

    </div>
</div>
@endsection